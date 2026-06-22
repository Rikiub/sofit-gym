<?php

namespace App\Models;

use App\Models\BaseModel;
use CuyZ\Valinor\Mapper\TreeMapper;
use PDO;

readonly class PermisoDTO
{
    public function __construct(
        public ?int $id_permiso = null,
        public ?string $nombre = null,
    ) {}
}

class PermisosModel extends BaseModel
{
    private string $table = "sofit_gym_seguridad.permiso";
    private string $primaryKey = 'id_permiso';

    public function __construct(
        PDO $pdo,
        private TreeMapper $mapper,
    ) {
        parent::__construct($pdo);
    }

    private function sqlSelect(string $where = ""): string
    {
        return <<<SQL
                SELECT *
                FROM {$this->table}
                {$where}
                ORDER BY nombre
            SQL;
    }

    /**
     * @return PermisoDTO[]
     */
    public function query(): array
    {
        $rows = $this->pdoQuery($this->sqlSelect())->fetchAll();

        return array_map(
            fn($row) => $this->mapper->map(PermisoDTO::class, $row),
            $rows
        );
    }

    public function find(string|int $id): ?PermisoDTO
    {
        $row = $this->pdoQuery(
            $this->sqlSelect(
                <<<SQL
                WHERE
                    {$this->primaryKey} = ?
                    OR nombre = ?
                SQL
            ),
            [$id, $id]
        )->fetch();

        return $row
            ? $this->mapper->map(PermisoDTO::class, $row)
            : null;
    }

    public function insert(PermisoDTO $permiso): PermisoDTO
    {
        $this->pdo->beginTransaction();

        $this->pdoInsert(
            $this->table,
            ["nombre" => $permiso->nombre],
        );

        $id = (int)$this->pdo->lastInsertId();
        $permiso = $this->find($id);
        $this->pdo->commit();

        return $permiso;
    }

    public function update(PermisoDTO $permiso): PermisoDTO
    {
        $this->pdo->beginTransaction();

        $this->pdoUpdate(
            $this->table,
            ["nombre" => $permiso->nombre],
            [$this->primaryKey => $permiso->id_permiso],
        );

        $permiso = $this->find($permiso->id_permiso);
        $this->pdo->commit();

        return $permiso;
    }

    public function delete(int $id): void
    {
        $this->pdoDelete($this->table, [$this->primaryKey => $id]);
    }
}
