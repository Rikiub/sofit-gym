<?php

namespace App\Models;

use App\Services\ImageStorage;
use App\Core\Database;
use App\Core\Validator;
use CuyZ\Valinor\Mapper\TreeMapper;
use DateTimeImmutable;
use Exception;

use function App\Core\toDbDate;

class UsuarioModel extends Model
{
    private string $table = self::DB_SECURITY . ".usuario";
    private string $primaryKey = "id_usuario";

    public function __construct(
        Database $db,
        private TreeMapper $mapper,
        private ImageStorage $image,
    ) {
        parent::__construct($db);
    }

    /**
     * @return Usuario[]
     */
    public function query(): array
    {
        $rows = $this->db->dbQuery($this->sqlSelect())->fetchAll();
        return array_map($this->mapUsuario(...), $rows);
    }

    public function findById(int $id): ?Usuario
    {
        $row = $this->db->dbQuery(
            $this->sqlSelect(where: "WHERE {$this->primaryKey} = ?"),
            [$id],
        )->fetch();

        return $row
            ? $this->mapUsuario($row)
            : null;
    }

    public function findByUsername(string $username): ?Usuario
    {
        $row = $this->db->dbQuery(
            $this->sqlSelect(where: "WHERE nombre_usuario = ?"),
            [$username],
        )->fetch();

        return $row
            ? $this->mapUsuario($row)
            : null;
    }

    public function findByEmail(string $email): ?Usuario
    {
        $row = $this->db->dbQuery(
            $this->sqlSelect(where: "WHERE {$this->table}.email = ?"),
            [$email]
        )->fetch();

        return $row
            ? $this->mapUsuario($row)
            : null;
    }

    private function getById(int $id): Usuario
    {
        return
            $this->findById($id)
            ?? throw new Exception("Usuario '{$id}' no encontrado");
    }

    public function insert(Usuario $usuario): Usuario
    {
        $usuario->validateInsert();

        $this->db->dbInsert(
            $this->table,
            $this->mapToColumns($usuario, insertMode: true),
        );

        $id = (int) $this->db->lastInsertId();
        $this->syncImage($id, $usuario->imagen_url);

        return $this->findById($id);
    }

    public function update(int $id, Usuario $usuario): Usuario
    {
        return $this->db->dbTransaction(function () use ($id, $usuario) {
            $this->db->dbUpdate(
                $this->table,
                $this->mapToColumns($usuario),
                [$this->primaryKey => $id],
            );
            $usuario = $this->syncImage($id, $usuario->imagen_url);
            return $usuario;
        });
    }

    public function updateUltimoAcceso(int $id): void
    {
        $this->db->dbUpdate(
            $this->table,
            ["ultimo_acceso" => toDbDate(new DateTimeImmutable())],
            [$this->primaryKey => $id]
        );
    }

    public function delete(int $id): void
    {
        $usuario = $this->getById($id);

        $this->db->dbDelete($this->table, [$this->primaryKey => $id]);
        $this->image->delete($usuario->imagen_url);
    }

    private function syncImage(int $id, string $imagen_url = null): Usuario
    {
        $oldUsuario = $this->getById($id);

        if ($imagen_url && $imagen_url !== $oldUsuario->imagen_url) {
            $imagen_url = $this->image->moveFromTemp($imagen_url, "/usuarios");

            $this->db->dbUpdate(
                $this->table,
                ["imagen_url" => $imagen_url],
                [$this->primaryKey => $id],
            );

            $this->image->delete($oldUsuario->imagen_url ?? "");
            return $this->findById($id);
        }
        return $oldUsuario;
    }

    private function sqlSelect(?string $where = ""): string
    {
        return <<<SQL
            SELECT
                usuario.*,
                rol.nombre AS `rol`,
                (
                    SELECT CONCAT('[', GROUP_CONCAT(CONCAT('"', p.nombre, '"')), ']')
                    FROM
                        {$this->dbSecurity("permiso")} p
                    JOIN
                        {$this->dbSecurity("rol_permiso")} rp 
                        ON rp.id_permiso = p.id_permiso
                    WHERE rp.id_rol = usuario.id_rol
                ) AS `permisos`
            FROM {$this->table} usuario
            LEFT JOIN
                {$this->dbSecurity("rol")} rol
                ON rol.id_rol = usuario.id_rol
            {$where}
        SQL;
    }

    private function mapUsuario(array $row): Usuario
    {
        $row["permisos"] = json_decode($row["permisos"], true);
        $usuario = $this->mapper->map(Usuario::class, $row);
        return $usuario;
    }

    private function mapToColumns(Usuario $dto, bool $insertMode = false): array
    {
        $data["email"] = $dto->email;
        if ($dto->id_rol) {
            $data["id_rol"] = $dto->id_rol;
        }

        if ($insertMode) {
            $data['nombre_usuario'] = $dto->nombre_usuario;
            $data['contrasena_hash'] = $this->hashPassword($dto->contrasena_hash);
        }

        return $data;
    }

    private function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    // Intentos

    public function insertIntentoAcceso(int $id_usuario, bool $exito): void
    {
        $this->db->dbInsert(
            $this->dbSecurity('intento_acceso'),
            [
                "id_usuario" => $id_usuario,
                "exito" => $exito
            ],
        );
    }

    public function intentosFallidos(int $id_usuario, DateTimeImmutable $duracion): int
    {
        $intentos = $this->db->dbQuery(
            <<<SQL
                SELECT COUNT(*)
                FROM
                    {$this->dbSecurity('intento_acceso')}
                WHERE
                    id_usuario = ?
                    AND exito = 0
                    AND fecha_creacion > ?;
            SQL,
            [
                $id_usuario,
                toDbDate($duracion)
            ]
        )->fetchColumn();
        return (int)$intentos;
    }

    // Recuperación Contraseña

    public function createRecoveryCode(int $id_usuario): string
    {
        $codigo = $this->generateRecoveryCode();
        $expiracion = new DateTimeImmutable('+15 minutes');

        $this->db->dbInsert(
            $this->dbSecurity("recuperacion_contrasena"),
            [
                "id_usuario" => $id_usuario,
                "codigo" => $codigo,
                "creado_en" => toDbDate(new DateTimeImmutable()),
                "expira_en" => toDbDate($expiracion),
            ]
        );

        return $codigo;
    }

    public function verifyRecoveryCode(string $codigo): ?Usuario
    {
        $row = $this->db->dbQuery(
            <<<SQL
                SELECT id_usuario
                FROM {$this->dbSecurity("recuperacion_contrasena")}
                WHERE
                    codigo = ? 
                    AND expira_en > creado_en
            SQL,
            [$codigo]
        )->fetch();

        return $row
            ? $this->findById($row["id_usuario"])
            : null;
    }

    public function updatePasswordAndClearCode(int $id_usuario, string $new_password): void
    {
        $this->db->dbTransaction(function () use ($id_usuario, $new_password) {
            $this->db->dbUpdate(
                table: $this->table,
                data: ["contrasena_hash" => $this->hashPassword($new_password)],
                conditions: ["id_usuario" => $id_usuario],
            );
            $this->db->dbDelete(
                table: $this->dbSecurity("recuperacion_contrasena"),
                conditions: ["id_usuario" => $id_usuario],
            );
        });
    }

    private function generateRecoveryCode(int $length = 8): string
    {
        $chars = '23456789ABCDEFGHJKLMNPQRSTUVWXYZ';
        $charCount = strlen($chars);
        $code = '';

        for ($i = 0; $i < $length; $i++) {
            $code .= $chars[random_int(0, $charCount - 1)];
        }

        // Formatear como XXXX-XXXX
        return substr($code, 0, 4) . '-' . substr($code, 4, 4);
    }
}

// DTO
readonly class Usuario
{
    public function __construct(
        public ?int $id_usuario = null,
        public ?int $id_rol = null,
        public ?string $rol = null,
        public ?string $nombre_usuario = null,
        public ?string $contrasena_hash = null,
        public ?string $imagen_url = null,
        public ?string $email = null,
        public ?DateTimeImmutable $fecha_creacion = null,
        public ?DateTimeImmutable $ultimo_acceso = null,
        /** @var string[] */
        public array $permisos = [],
    ) {}

    public function validateInsert()
    {
        Validator::required($this->id_rol, "id_rol");
        Validator::required($this->nombre_usuario, "nombre_usuario");
        Validator::required($this->nombre_usuario, "contrasena_hash");
    }
}
