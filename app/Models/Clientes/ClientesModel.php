<?php

namespace App\Models\Clientes;

use App\Helpers\Validator;
use App\Models\Personas\PersonasModel;
use App\Models\BaseModel;
use CuyZ\Valinor\Mapper\TreeMapper;
use PDO;

class ClientesModel extends BaseModel
{
    public function __construct(
        PDO $pdo,
        private TreeMapper $mapper,
        private PersonasModel $personasModel
    ) {
        return parent::__construct($pdo);
    }

    private function sqlSelect(): string
    {
        return <<<SQL
                SELECT
                    cliente.cedula_cliente AS `cedula`,
                    persona.nombre,
                    persona.apellido,
                    persona.correo,
                    persona.telefono,
                    persona.direccion,
                    persona.fecha_nacimiento,
                    persona.fecha_registro,
                    persona.activo,
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
                LEFT JOIN persona ON persona.cedula_persona = cliente.cedula_cliente
                LEFT JOIN membresia m ON cliente.id_membresia = m.id_membresia
                LEFT JOIN tipo_membresia mt ON m.id_tipo = mt.id_tipo
                LEFT JOIN estado_membresia me ON m.id_estado = me.id_estado
            SQL;
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
        $sql = $this->sqlSelect();
        $whereClauses = [];
        $params = [];

        // Busqueda global
        if ($search) {
            $searchColumns = [
                'persona.nombre',
                'persona.apellido',
                'persona.correo',
                'persona.telefono',
                'persona.fecha_nacimiento',
                'persona.fecha_registro',
            ];

            $searchClauses = array_map(fn($col) => "$col LIKE ?", $searchColumns);

            $whereClauses[] = "(" . implode(" OR ", $searchClauses) . ")";

            foreach ($searchColumns as $col) {
                $params[] = "%" . $search . "%";
            }
        }

        // 2. Handle specific filters (mapped and grouped with AND)
        // Define how each allowed filter key maps to the SQL column and operator
        $filterDefinitions = [
            'cedula'             => ['column' => 'persona.cedula', 'op' => 'LIKE'],
            'nombre'             => ['column' => 'persona.nombre', 'op' => 'LIKE'],
            'apellido'           => ['column' => 'persona.apellido', 'op' => 'LIKE'],
            'correo'             => ['column' => 'persona.correo', 'op' => 'LIKE'],
            'telefono'           => ['column' => 'persona.telefono', 'op' => 'LIKE'],
            'activo'             => ['column' => 'cliente.activo', 'op' => '='],
            'id_tipo'            => ['column' => 'membresia.id_tipo', 'op' => '='],
            'id_estado'          => ['column' => 'membresia.id_estado', 'op' => '='],
            'fecha_inicio_desde' => ['column' => 'membresia.fecha_inicio', 'op' => '>='],
            'fecha_inicio_hasta' => ['column' => 'membresia.fecha_inicio', 'op' => '<='],
            'fecha_fin_desde'    => ['column' => 'membresia.fecha_fin', 'op' => '>='],
            'fecha_fin_hasta'    => ['column' => 'membresia.fecha_fin', 'op' => '<='],
        ];

        foreach ($filters as $key => $value) {
            // Solo procesar si el filtro esta permitido y el valor no es nulo
            if (isset($filterDefinitions[$key]) && $value !== null && $value !== '') {
                $column = $filterDefinitions[$key]['column'];
                $op = $filterDefinitions[$key]['op'];

                $whereClauses[] = "$column $op ?";

                if ($op === 'LIKE') {
                    $params[] = "%" . $value . "%";
                } else {
                    $params[] = is_bool($value) ? (int)$value : $value;
                }
            }
        }

        // Construir los WHERE en el SQL
        if (!empty($whereClauses)) {
            $sql .= " WHERE " . implode(" AND ", $whereClauses);
        }

        $rows = $this->pdoQuery($sql, $params)->fetchAll();
        return array_map($this->mapToCliente(...), $rows);
    }

    public function find(string $cedula): ?ClienteDTO
    {
        $row = $this->pdoQuery(
            "{$this->sqlSelect()} WHERE cedula_cliente = ?",
            [$cedula]
        )->fetch();

        if (!$row)
            return null;
        return $this->mapToCliente($row);
    }

    public function getEstadosMembresia(): array
    {
        return $this->pdoQuery('SELECT * FROM estado_membresia')->fetchAll();
    }

    public function getTiposMembresia(): array
    {
        return $this->pdoQuery('SELECT * FROM tipo_membresia')->fetchAll();
    }

    public function insert(ClienteDTO $cliente): ClienteDTO
    {
        $cliente->validateInsert();
        $this->pdo->beginTransaction();

        $this->personasModel->insert($cliente);
        $this->pdoInsert('membresia', $this->membresiaToArray($cliente->membresia));
        $membresiaId = $this->pdo->lastInsertId();

        $this->pdoInsert('cliente', [
            'cedula_cliente' => $cliente->cedula,
            'id_membresia' => $membresiaId,
        ]);

        $this->pdo->commit();
        return $this->find($cliente->cedula);
    }

    public function update(ClienteDTO $cliente): ClienteDTO
    {
        $cliente->validateUpdate();
        $this->pdo->beginTransaction();

        $this->personasModel->update($cliente);

        if ($cliente->membresia) {
            // Obtener ID de la membresia desde el cliente
            $membresiaId = $this->pdoQuery(
                'SELECT id_membresia FROM cliente WHERE cedula_cliente = ?',
                [$cliente->cedula]
            )->fetchColumn();

            $this->pdoUpdate(
                'membresia',
                $this->membresiaToArray($cliente->membresia),
                ['id_membresia' => $membresiaId],
            );
        }

        $this->pdo->commit();
        return $this->find($cliente->cedula);
    }

    public function delete(string $cedula): void
    {
        $this->pdoDelete('cliente', ['cedula_cliente' => $cedula]);
    }

    private function membresiaToArray(MembresiaDTO $membresia): array
    {
        return [
            'id_tipo' => $membresia->id_tipo,
            'id_estado' => $membresia->id_estado,
            'fecha_inicio' => Validator::dateToString($membresia->fecha_inicio),
            'fecha_fin' => Validator::dateToString($membresia->fecha_fin),
        ];
    }
}
