<?php

namespace App\Models;

use App\Models\Database;
use App\Core\Tools;

class RolModel extends Database
{
    /**
     * @return Rol[]
     */
    public function query(): array
    {
        $rows = $this->dbQuery($this->sqlSelect())->fetchAll();
        return array_map($this->mapRol(...), $rows);
    }

    public function queryPermisos(): array
    {
        $rows = $this->dbQuery(
            <<<SQL
                SELECT *
                FROM {$this->dbSecurity("permiso")}
                ORDER BY nombre
            SQL
        )->fetchAll();
        return $rows;
    }
    public function findPermiso(string $nombre): array
    {
        $row = $this->dbQuery(
            <<<SQL
                SELECT *
                FROM {$this->dbSecurity("permiso")}
                WHERE nombre = ?
            SQL,
            [$nombre]
        )->fetch();
        return $row ?? null;
    }

    public function find(int $id): ?Rol
    {
        $row = $this->dbQuery(
            $this->sqlSelect(where: "WHERE id_rol = ?"),
            [$id]
        )->fetch();

        return $row
            ? $this->mapRol($row)
            : null;
    }

    public function insert(Rol $rol): Rol
    {
        return $this->dbTransaction(function () use ($rol) {
            $this->dbInsert(
                "rol",
                ["nombre" => $rol->nombre],
            );

            $id = (int)$this->pdo->lastInsertId();
            $this->syncPermisos($id, $rol->permisos);

            return $this->find($id);
        });
    }

    public function update(int $id, Rol $rol): Rol
    {
        return $this->dbTransaction(function () use ($id, $rol) {
            $this->syncPermisos($id, $rol->permisos);
            return $this->find($rol->id_rol);
        });
    }

    public function delete(int $id): void
    {
        $this->dbDelete("rol", ["id_rol" => $id]);
    }

    private function syncPermisos(int $id_rol, array $permisos): void
    {
        $table = $this->dbSecurity("rol_permiso");

        // Eliminar todos los permisos del rol
        foreach ($permisos as $p) {
            $this->dbDelete($table, ["id_rol" => $id_rol]);
        }

        foreach ($permisos as $p) {
            $permiso = $this->findPermiso($p);

            $this->dbInsert($table, [
                "id_rol" => $id_rol,
                "id_permiso" => $permiso["id_permiso"],
            ]);
        }
    }

    private function mapRol(array $row): Rol
    {
        $row["permisos"] = json_decode($row["permisos"], true);
        return Tools::map(Rol::class, $row);
    }

    private function sqlSelect(string $where = ""): string
    {
        return <<<SQL
                SELECT
                    rol.*,
                    (
                        SELECT CONCAT('[', GROUP_CONCAT(CONCAT('"', p.nombre, '"')), ']')
                        FROM
                            {$this->dbSecurity("permiso")} p
                        JOIN
                            {$this->dbSecurity("rol_permiso")} rp 
                            ON rp.id_permiso = p.id_permiso
                        WHERE rp.id_rol = rol.id_rol
                        ORDER BY p.nombre
                    ) AS `permisos`
                FROM {$this->dbSecurity("rol")} rol
                {$where}
            SQL;
    }
}

// DTO
readonly class Rol
{
    public function __construct(
        public ?int $id_rol = null,
        public ?string $nombre = null,
        /** @var string[] */
        public array $permisos = [],
    ) {}
}
