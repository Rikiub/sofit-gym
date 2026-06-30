<?php

namespace App\Models\Clientes;

use App\Core\Database;
use App\Models\Model;
use App\Models\Personas\PersonasModel;
use CuyZ\Valinor\Mapper\TreeMapper;

class ClientesModel extends Model
{
    public string $table = 'cliente';
    public string $primaryKey = 'cedula';

    public function __construct(
        Database $db,
        private TreeMapper $mapper,
        private PersonasModel $personasModel,
    ) {
        return parent::__construct($db);
    }

    public function getSummary(): array
    {
        $rows = $this->db->dbQuery(<<<SQL
            SELECT COUNT(*) AS total_clientes
            FROM {$this->table}
        SQL)->fetch();
        return $rows;
    }

    /**
     * @return ClienteDTO[]
     */
    public function query(?string $search = null, array $filters = []): array
    {
        $whereClauses = [];
        $params = [];

        // Busqueda global
        if ($search) {
            $search = trim($search);

            $columns = [
                'persona.nombre',
                'persona.apellido',
                "CONCAT(persona.nombre, ' ', persona.apellido)",
                "CONCAT(persona.apellido, ' ', persona.nombre)",
                'persona.correo',
                'persona.telefono',
                'persona.fecha_nacimiento',
                'persona.fecha_creacion',
            ];

            $searchClauses = array_map(fn($col) => "$col LIKE ?", $columns);
            $whereClauses[] = "(" . implode(" OR ", $searchClauses) . ")";

            foreach ($columns as $col) {
                $params[] = "%" . $search . "%";
            }
        }

        // 2. Handle specific filters (mapped and grouped with AND)
        $filterDefinitions = [
            'cedula'             => ['column' => 'persona.cedula', 'op' => 'LIKE'],
            'nombre'             => ['column' => 'persona.nombre', 'op' => 'LIKE'],
            'apellido'           => ['column' => 'persona.apellido', 'op' => 'LIKE'],
            'correo'             => ['column' => 'persona.correo', 'op' => 'LIKE'],
            'telefono'           => ['column' => 'persona.telefono', 'op' => 'LIKE'],
            'activo'             => ['column' => 'persona.activo', 'op' => '='],
            'id_tipo'            => ['column' => 'm.id_tipo', 'op' => '='],
            'id_estado'          => ['column' => 'm.id_estado', 'op' => '='],
            'fecha_inicio_desde' => ['column' => 'm.fecha_inicio', 'op' => '>='],
            'fecha_inicio_hasta' => ['column' => 'm.fecha_inicio', 'op' => '<='],
            'fecha_fin_desde'    => ['column' => 'm.fecha_fin', 'op' => '>='],
            'fecha_fin_hasta'    => ['column' => 'm.fecha_fin', 'op' => '<='],
        ];

        foreach ($filters as $key => $value) {
            if (isset($filterDefinitions[$key]) && $value !== null && $value !== '') {
                $column = $filterDefinitions[$key]['column'];
                $op = $filterDefinitions[$key]['op'];

                if ($op === 'LIKE') {
                    $whereClauses[] = "$column LIKE ? COLLATE utf8mb4_unicode_ci";
                    $params[] = "%" . $value . "%";
                } else {
                    $whereClauses[] = "$column $op ?";
                    $params[] = is_bool($value) ? (int)$value : $value;
                }
            }
        }

        $sql = $this->sqlSelect(
            !empty($whereClauses)
                ? " WHERE " . implode(" AND ", $whereClauses)
                : ""
        );

        $rows = $this->db->dbQuery($sql, $params)->fetchAll();
        return array_map(
            $this->mapToCliente(...),
            $rows
        );
    }

    public function find(string $cedula): ?ClienteDTO
    {
        $row = $this->db->dbQuery(
            $this->sqlSelect(where: "WHERE cliente.{$this->primaryKey} = ?"),
            [$cedula]
        )->fetch();

        return $row
            ? $this->mapToCliente($row)
            : null;
    }

    /** Comprobar si la cedula ya esta asignada a una persona */
    public function checkDuplicate(string $cedula): bool
    {
        return (bool)$this->personasModel->find($cedula);
    }

    public function insert(ClienteDTO $cliente): ClienteDTO
    {
        $cliente->validateInsert();

        return $this->db->dbTransaction(function () use ($cliente) {
            $this->personasModel->insert($cliente);
            $this->db->dbInsert(
                $this->table,
                [$this->primaryKey => $cliente->cedula]
            );
            return $this->find($cliente->cedula);
        });
    }

    public function update(string $cedula, ClienteDTO $cliente): ClienteDTO
    {
        $this->personasModel->update($cedula, $cliente);
        return $this->find($cliente->cedula);
    }

    public function delete(string $cedula): void
    {
        $this->db->dbDelete($this->table, [$this->primaryKey => $cedula]);
    }

    private function mapToCliente(array $row): ClienteDTO
    {
        $row['membresia'] = json_decode($row['membresia'], true);
        return $this->mapper->map(ClienteDTO::class, $row);
    }

    private function sqlSelect(string $where = ""): string
    {
        return <<<SQL
                SELECT
                    persona.*,
                    JSON_OBJECT(
                        "id_membresia", m.id_membresia,
                        "id_tipo", m.id_tipo,
                        "estado", me.nombre,
                        "id_estado", m.id_estado,
                        "tipo", mt.nombre,
                        "fecha_inicio", m.fecha_inicio,
                        "fecha_fin", m.fecha_fin
                    ) AS membresia
                FROM cliente
                LEFT JOIN persona ON persona.cedula = cliente.cedula
                LEFT JOIN membresia m ON m.id_membresia = (
                    SELECT m2.id_membresia 
                    FROM membresia m2 
                    WHERE m2.cedula_cliente = cliente.cedula 
                    ORDER BY m2.id_membresia DESC 
                    LIMIT 1
                )
                LEFT JOIN tipo_membresia mt ON m.id_tipo = mt.id_tipo
                LEFT JOIN estado_membresia me ON m.id_estado = me.id_estado
                {$where} 
                ORDER BY
                    CASE
                        WHEN m.id_membresia IS NOT NULL THEN 0
                        ELSE 1
                    END ASC;
            SQL;
    }

    // REPORTES

    /**
     * Obtiene los datos limpios y estructurados de los clientes para el reporte general.
     * Reutiliza la lógica relacional de la persona con su membresía más reciente.
     * Diseñado de manera homóloga a ProductosModel para integrarse con reporteClientes.
     *
     * @param string|null $estado Filtro opcional por el nombre del estado (ej: 'Activo', 'Inactivo')
     * @return array Listado de clientes como arreglos asociativos
     */
    public function obtenerClientesParaReporte(?string $estado = null): array
    {
        $sql = "SELECT
                    p.cedula,
                    p.nombre,
                    p.apellido,
                    p.correo,
                    p.telefono,
                    JSON_OBJECT(
                        'tipo', mt.nombre,
                        'estado', me.nombre
                    ) AS membresia
                FROM cliente c
                LEFT JOIN persona p ON p.cedula = c.cedula
                LEFT JOIN membresia m ON m.id_membresia = (
                    SELECT m2.id_membresia 
                    FROM membresia m2 
                    WHERE m2.cedula_cliente = c.cedula 
                    ORDER BY m2.id_membresia DESC \n                    LIMIT 1
                )
                LEFT JOIN tipo_membresia mt ON m.id_tipo = mt.id_tipo
                LEFT JOIN estado_membresia me ON m.id_estado = me.id_estado";

        $where = [];
        $params = [];

        // Permite opcionalmente filtrar en el reporte solo los activos, vencidos, etc.
        if (!empty($estado)) {
            $where[] = "me.nombre = :estado";
            $params['estado'] = $estado;
        }

        if (!empty($where)) {
            $sql .= " WHERE " . implode(" AND ", $where);
        }

        // Ordenamos alfabéticamente por apellido y nombre
        $sql .= " ORDER BY p.apellido ASC, p.nombre ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}
