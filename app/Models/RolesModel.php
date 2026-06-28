<?php

namespace App\Models;

use App\Core\Database;
use App\Models\Model;
use CuyZ\Valinor\Mapper\TreeMapper;

class RolesModel extends Model
{
    public function __construct(
        Database $db,
        private TreeMapper $mapper,
    ) {
        parent::__construct($db);
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

    /**
     * @return RolDTO[]
     */
    public function query(): array
    {
        $rows = $this->db->dbQuery($this->sqlSelect())->fetchAll();
        return array_map($this->mapRol(...), $rows);
    }

    public function queryPermisos(): array
    {
        $rows = $this->db->dbQuery(
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
        $row = $this->db->dbQuery(
            <<<SQL
                SELECT *
                FROM {$this->dbSecurity("permiso")}
                WHERE nombre = ?
            SQL,
            [$nombre]
        )->fetch();
        return $row ?? null;
    }

    public function find(int $id): ?RolDTO
    {
        $row = $this->db->dbQuery(
            $this->sqlSelect("WHERE id_rol = ?"),
            [$id]
        )->fetch();

        return $row
            ? $this->mapRol($row)
            : null;
    }

    private function mapRol(array $row): RolDTO
    {
        $row["permisos"] = json_decode($row["permisos"], true);
        return $this->mapper->map(RolDTO::class, $row);
    }

    public function insert(RolDTO $rol): RolDTO
    {
        return $this->db->dbTransaction(function () use ($rol) {
            $this->db->dbInsert(
                "rol",
                ["nombre" => $rol->nombre],
            );

            $id = (int)$this->db->lastInsertId();
            $this->syncPermisos($id, $rol->permisos);

            return $this->find($id);
        });
    }

    public function update(RolDTO $rol): RolDTO
    {
        return $this->db->dbTransaction(function () use ($rol) {
            $this->syncPermisos($rol->id_rol, $rol->permisos);
            return $this->find($rol->id_rol);
        });
    }

    public function delete(int $id): void
    {
        $this->db->dbDelete("rol", ["id_rol" => $id]);
    }

    private function syncPermisos(int $id_rol, array $permisos): void
    {
        $table = $this->dbSecurity("rol_permiso");

        // Eliminar todos los permisos del rol
        foreach ($permisos as $p) {
            $this->db->dbDelete($table, ["id_rol" => $id_rol]);
        }

        foreach ($permisos as $p) {
            $permiso = $this->findPermiso($p);

            $this->db->dbInsert($table, [
                "id_rol" => $id_rol,
                "id_permiso" => $permiso["id_permiso"],
            ]);
        }
    }
}

// DTO
readonly class RolDTO
{
    public function __construct(
        public ?int $id_rol = null,
        public ?string $nombre = null,
        /** @var string[] */
        public array $permisos = [],
    ) {}
}
