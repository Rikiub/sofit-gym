<?php

namespace App\Models;

use App\Helpers\Validator;
use App\Models\BaseModel;
use CuyZ\Valinor\Mapper\TreeMapper;
use DateTimeImmutable;
use DateTimeInterface;
use Exception;
use PDO;

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

    public function validateInsert() {}
    public function validateUpdate() {}
}

class UsuariosModel extends BaseModel
{
    private string $dbSeguridad = "sofit_gym_seguridad";
    private string $table = 'sofit_gym_seguridad.usuario';
    private string $tableRecuperacion = 'sofit_gym_seguridad.recuperacion_contrasena';
    private string $primaryKey = 'id_usuario';

    public function __construct(
        PDO $pdo,
        private TreeMapper $mapper,
    ) {
        parent::__construct($pdo);
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
                        {$this->dbSeguridad}.permiso p
                    JOIN
                        {$this->dbSeguridad}.rol_permiso rp 
                        ON rp.id_permiso = p.id_permiso
                    WHERE rp.id_rol = usuario.id_rol
                ) AS `permisos`
            FROM {$this->table} usuario
            LEFT JOIN
                {$this->dbSeguridad}.rol rol
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

    /**
     * @return UsuarioDTO[]
     */
    public function query(): array
    {
        $rows = $this->pdoQuery($this->sqlSelect())->fetchAll();
        return array_map($this->mapUsuario(...), $rows);
    }

    public function find(int|string $nombre_usuario): ?UsuarioDTO
    {
        $row = $this->pdoQuery(
            $this->sqlSelect(
                <<<SQL
                WHERE
                    {$this->table}.id_usuario = ?
                    OR {$this->table}.nombre_usuario = ?
                SQL
            ),
            [$nombre_usuario, $nombre_usuario]
        )->fetch();

        return $row
            ? $this->mapUsuario($row)
            : null;
    }

    public function findByEmail(string $email): ?UsuarioDTO
    {
        $row = $this->pdoQuery(
            $this->sqlSelect("WHERE {$this->table}.email = ?"),
            [$email]
        )->fetch();

        return $row
            ?  $this->mapUsuario($row)
            : null;
    }

    public function insert(UsuarioDTO $usuario): UsuarioDTO
    {
        $usuario->validateInsert();
        $this->pdo->beginTransaction();

        $this->pdoInsert(
            $this->table,
            $this->dtoToArray($usuario),
        );

        $id = (int) $this->pdo->lastInsertId();
        $usuario = $this->find($id);

        $this->pdo->commit();
        return $usuario;
    }

    public function update(UsuarioDTO $usuario): UsuarioDTO
    {
        $usuario->validateUpdate();
        $this->pdo->beginTransaction();

        $array = $this->dtoToArray($usuario);
        unset($array["contrasena_hash"]);

        $this->pdoUpdate(
            $this->table,
            $array,
            [$this->primaryKey => $usuario->id_usuario],
        );
        $usuario = $this->find($usuario->nombre_usuario);

        $this->pdo->commit();
        return $usuario;
    }

    public function delete(int|string $id): void
    {
        $this->pdoQuery(
            <<<SQL
                DELETE FROM {$this->table}
                WHERE
                    id_usuario = ?
                    OR nombre_usuario = ?
            SQL,
            [$id, $id],
        );
    }

    public function actualizarUltimoAcceso(int $id)
    {
        $this->pdoUpdate(
            $this->table,
            ["ultimo_acceso" => Validator::dateToString(new DateTimeImmutable())],
            ["id_usuario" => $id]
        );
    }

    private function dtoToArray(UsuarioDTO $dto): array
    {
        $hashedPassword = password_hash($dto->contrasena_hash, PASSWORD_DEFAULT);

        $data = [
            'nombre_usuario' => $dto->nombre_usuario,
            'contrasena_hash' => $hashedPassword,
            'imagen_url' => $dto->imagen_url,
            'email' => $dto->email,
        ];
        if ($dto->id_rol) {
            $data["id_rol"] = $dto->id_rol;
        }

        return $data;
    }

    // ====================================================================
    // MÉTODOS AÑADIDOS PARA LA RECUPERACIÓN DE CONTRASEÑA
    // ====================================================================

    public function saveRecoveryCode(int $id_usuario, string $codigo, DateTimeInterface $expiracion): void
    {
        $this->pdoInsert($this->tableRecuperacion, [
            "id_usuario" => $id_usuario,
            "codigo" => $codigo,
            "creado_en" => Validator::dateToString(new DateTimeImmutable()),
            "expira_en" => Validator::dateToString($expiracion),
        ]);
    }

    public function verifyRecoveryCode(string $codigo): ?UsuarioDTO
    {
        $row = $this->pdoQuery(
            <<<SQL
                SELECT id_usuario
                FROM {$this->tableRecuperacion}
                WHERE
                    codigo = ? 
                    AND expira_en > creado_en
            SQL,
            [$codigo]
        )->fetch();

        if (!$row) return null;
        return $this->find($row["id_usuario"]);
    }

    public function updatePasswordAndClearCode(int $id_usuario, string $new_password): void
    {
        $hashedPassword = password_hash($new_password, PASSWORD_DEFAULT);

        $this->pdo->beginTransaction();

        if (!$this->find($id_usuario)) {
            throw new Exception("Usuario no encontrado");
        }

        $this->pdoQuery(
            <<<SQL
                UPDATE {$this->table}
                SET contrasena_hash = ?
                WHERE id_usuario = ?
            SQL,
            [$hashedPassword, $id_usuario]
        );
        $this->pdoQuery(
            <<<SQL
                DELETE FROM {$this->tableRecuperacion}
                WHERE id_usuario = ?
            SQL,
            [$id_usuario]
        );

        $this->pdo->commit();
    }
}
