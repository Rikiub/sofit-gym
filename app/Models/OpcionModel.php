<?php

namespace App\Models;

use PDO;

class OpcionModel extends Model
{
    private string $table = self::DB_SECURITY . ".opcion";

    public function query(): array
    {
        return $this->db->dbQuery($this->sqlSelect())
            ->fetchAll(PDO::FETCH_UNIQUE | PDO::FETCH_ASSOC);
    }

    /** Obtener el valor de una clave directamente. */
    public function get(string $clave): null|string|int|bool
    {
        $row = $this->find($clave);
        return $row
            ? $row["valor"]
            : null;
    }

    /** Obtener el array desde una clave. */
    public function find(string $clave): ?array
    {
        $row = $this->db->dbQuery(
            $this->sqlSelect(where: "WHERE opcion.clave = ?"),
            [$clave]
        )->fetch();
        return $row ?: null;
    }

    public function update(string $clave, string $valor)
    {
        $this->db->dbUpdate(
            $this->table,
            ["valor" => $valor],
            ["clave" => $clave],
        );
    }

    private function sqlSelect(string $where = ""): string
    {
        $security = self::DB_SECURITY;
        return <<<SQL
            SELECT
                opcion.clave,
                opcion.*,
                grupo.nombre AS grupo_nombre,
                grupo.descripcion AS grupo_descripcion
            FROM {$this->table} opcion
            LEFT JOIN {$security}.opcion_grupo grupo ON opcion.id_grupo = grupo.id_grupo
            {$where}
        SQL;
    }
}
