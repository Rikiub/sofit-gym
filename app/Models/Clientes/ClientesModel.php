<?php

namespace App\Models\Clientes;

use App\Models\BaseModel;
use App\Models\Personas\PersonasModel;
use CuyZ\Valinor\Mapper\TreeMapper;
use PDO;

class ClientesModel extends BaseModel
{
    public string $table = 'cliente';
    public string $primaryKey = 'cedula';

    public function __construct(
        PDO $pdo,
        private TreeMapper $mapper,
        private PersonasModel $personasModel,
    ) {
        return parent::__construct($pdo);
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

    public function queryMembresiaMetadata(): array
    {
        $tipos = $this->pdoQuery('SELECT * FROM tipo_membresia')->fetchAll();
        $estados = $this->pdoQuery('SELECT * FROM estado_membresia')->fetchAll();

        return [
            "tipos" => $tipos,
            "estados" => $estados,
        ];
    }

    private function mapToCliente(array $row): ClienteDTO
    {
        $row['membresia'] = json_decode($row['membresia'], true);
        return $this->mapper->map(ClienteDTO::class, $row);
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

        $rows = $this->pdoQuery($sql, $params)->fetchAll();
        return array_map(
            $this->mapToCliente(...),
            $rows
        );
    }

    public function find(string $cedula): ?ClienteDTO
    {
        $row = $this->pdoQuery(
            $this->sqlSelect("WHERE cliente.{$this->primaryKey} = ?"),
            [$cedula]
        )->fetch();

        return $row
            ? $this->mapToCliente($row)
            : null;
    }

    public function insert(ClienteDTO $cliente): ClienteDTO
    {
        $cliente->validateInsert();

        return $this->pdoTransaction(function () use ($cliente) {
            $this->personasModel->insert($cliente);
            $this->pdoInsert($this->table, [
                $this->primaryKey => $cliente->cedula,
            ]);
            return $this->find($cliente->cedula);
        });
    }

    public function update(ClienteDTO $cliente): ClienteDTO
    {
        $cliente->validateUpdate();

        $this->personasModel->update($cliente);
        return $this->find($cliente->cedula);
    }

    public function delete(string $cedula): void
    {
        $this->pdoDelete($this->table, [$this->primaryKey => $cedula]);
    }
}
