<?php

namespace App\Models;

use App\Core\Database;
use App\Core\Validator;
use App\Models\Model;
use CuyZ\Valinor\Mapper\TreeMapper;
use DateTimeImmutable;
use DateTimeInterface;
use Exception;

use function App\Core\toDbDate;

class UsuariosModel extends Model
{
    private string $table = self::DB_SECURITY . ".usuario";
    private string $primaryKey = "id_usuario";

    public function __construct(
        Database $db,
        private TreeMapper $mapper,
    ) {
        parent::__construct($db);
    }

    /**
     * @return UsuarioDTO[]
     */
    public function query(): array
    {
        $rows = $this->db->dbQuery($this->sqlSelect())->fetchAll();
        return array_map($this->mapUsuario(...), $rows);
    }

    public function findById(int $id): ?UsuarioDTO
    {
        $row = $this->db->dbQuery(
            $this->sqlSelect(where: "WHERE {$this->primaryKey} = ?"),
            [$id],
        )->fetch();

        return $row
            ? $this->mapUsuario($row)
            : null;
    }

    public function findByUsername(string $username): ?UsuarioDTO
    {
        $row = $this->db->dbQuery(
            $this->sqlSelect(where: "WHERE nombre_usuario = ?"),
            [$username],
        )->fetch();

        return $row
            ? $this->mapUsuario($row)
            : null;
    }

    public function findByEmail(string $email): ?UsuarioDTO
    {
        $row = $this->db->dbQuery(
            $this->sqlSelect(where: "WHERE {$this->table}.email = ?"),
            [$email]
        )->fetch();

        return $row
            ? $this->mapUsuario($row)
            : null;
    }

    public function insert(UsuarioDTO $usuario): UsuarioDTO
    {
        $usuario->validateInsert();

        $this->db->dbInsert(
            $this->table,
            $this->mapToColumns($usuario, insertMode: true),
        );

        $id = (int) $this->db->lastInsertId();
        return $this->findById($id);
    }

    public function update(int $id, UsuarioDTO $usuario): UsuarioDTO
    {
        $this->db->dbUpdate(
            $this->table,
            $this->mapToColumns($usuario),
            [$this->primaryKey => $id],
        );
        return $this->findById($id);
    }

    public function updateImagen(int $id, string $imagen_url): UsuarioDTO
    {
        $this->db->dbUpdate(
            $this->table,
            ["imagen_url" => $imagen_url],
            [$this->primaryKey => $id],
        );
        return $this->findById($id);
    }

    public function delete(int|string $id): void
    {
        $this->db->dbDelete($this->table, [$this->primaryKey => $id]);
    }

    public function actualizarUltimoAcceso(int $id): void
    {
        $this->db->dbUpdate(
            $this->table,
            ["ultimo_acceso" => toDbDate(new DateTimeImmutable())],
            [$this->primaryKey => $id]
        );
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

    private function mapUsuario(array $row): UsuarioDTO
    {
        $row["permisos"] = json_decode($row["permisos"], true);
        $usuario = $this->mapper->map(UsuarioDTO::class, $row);
        return $usuario;
    }

    private function mapToColumns(UsuarioDTO $dto, bool $insertMode = true): array
    {
        $data = [
            'nombre_usuario' => $dto->nombre_usuario,
            'email' => $dto->email,
        ];
        if ($dto->id_rol) {
            $data["id_rol"] = $dto->id_rol;
        }

        if ($insertMode) {
            $hashedPassword = password_hash($dto->contrasena_hash, PASSWORD_DEFAULT);
            $data['contrasena_hash'] = $hashedPassword;
        }

        return $data;
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

    // ====================================================================
    // MÉTODOS AÑADIDOS PARA LA RECUPERACIÓN DE CONTRASEÑA
    // ====================================================================

    public function saveRecoveryCode(int $id_usuario, string $codigo, DateTimeInterface $expiracion): void
    {
        $this->db->dbInsert(
            $this->dbSecurity("recuperacion_contrasena"),
            [
                "id_usuario" => $id_usuario,
                "codigo" => $codigo,
                "creado_en" => toDbDate(new DateTimeImmutable()),
                "expira_en" => toDbDate($expiracion),
            ]
        );
    }

    public function verifyRecoveryCode(string $codigo): ?UsuarioDTO
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
            $hashedPassword = password_hash($new_password, PASSWORD_DEFAULT);

            $this->db->dbUpdate(
                table: $this->table,
                data: ["contrasena_hash" => $hashedPassword],
                conditions: ["id_usuario" => $id_usuario],
            );
            $this->db->dbDelete(
                table: $this->dbSecurity("recuperacion_contrasena"),
                conditions: ["id_usuario" => $id_usuario],
            );
        });
    }
}

// DTO
readonly class UsuarioDTO
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
