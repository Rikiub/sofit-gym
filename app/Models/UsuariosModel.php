<?php

namespace App\Models;

use App\Helpers\Validator;
use App\Models\BaseModel;
use CuyZ\Valinor\Mapper\TreeMapper;
use DateTimeImmutable;
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
        public ?DateTimeImmutable $fecha_registro = new DateTimeImmutable(),
    ) {}

    public function validateInsert() {}

    public function validateUpdate() {}
}

class UsuariosModel extends BaseModel
{
    private string $table = 'sofit_gym_seguridad.usuario';
    private string $primaryKey = 'id_usuario';

    public function __construct(
        PDO $pdo,
        private TreeMapper $mapper,
    ) {
        return parent::__construct($pdo);
    }

    private function sqlSelect(): string
    {
        return <<<SQL
                SELECT
                    usuario.*,
                    rol.nombre AS `rol`
                FROM {$this->table} usuario
                LEFT JOIN sofit_gym_seguridad.rol rol
                    ON rol.id_rol =  usuario.id_rol
            SQL;
    }

    /**
     * @return UsuarioDTO[]
     */
    public function query(): array
    {
        $rows = $this->pdoQuery($this->sqlSelect())->fetchAll();
        return array_map(
            fn($row) => $this->mapper->map(UsuarioDTO::class, $row),
            $rows
        );
    }

    public function find(int|string $nombre_usuario): ?UsuarioDTO
    {
        $row = $this->pdoQuery(
            <<<SQL
                {$this->sqlSelect()}
                WHERE
                    usuario.id_usuario = ?
                    OR usuario.nombre_usuario = ?
            SQL,
            [$nombre_usuario, $nombre_usuario]
        )->fetch();

        if (!$row)
            return null;
        return $this->mapper->map(UsuarioDTO::class, $row);
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

    private function dtoToArray(UsuarioDTO $dto): array
    {
        $hashedPassword = password_hash($dto->contrasena_hash, PASSWORD_DEFAULT);

        return [
            'id_rol' => $dto->id_rol,
            'nombre_usuario' => $dto->nombre_usuario,
            'contrasena_hash' => $hashedPassword,
            'imagen_url' => $dto->imagen_url,
            'fecha_registro' => Validator::dateToString($dto->fecha_registro),
        ];
    }
}
