<?php

namespace App\Models\Clientes;

use App\Helpers\Validator;
use App\Models\BaseModel;
use App\Models\Personas\PersonasModel;
use CuyZ\Valinor\Mapper\TreeMapper;
use PDO;

class ClientesModel extends BaseModel
{
    public function __construct(
        PDO $pdo,
        private TreeMapper $mapper,
        private PersonasModel $personasModel,
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
                    persona.fecha_creacion,
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
        $sql = $this->sqlSelect();
        $whereClauses = [];
        $params = [];

        // Busqueda global
        if ($search) {
            $search = trim($search);

            $columns = [
                'persona.nombre',
                'persona.apellido',
                "CONCAT(persona.nombre, ' ', persona.apellido)", // Permite buscar "Juan Perez"
                "CONCAT(persona.apellido, ' ', persona.nombre)", // Permite buscar "Perez Juan"
                'persona.correo',
                'persona.telefono',
                'persona.fecha_nacimiento',
                'persona.fecha_creacion',
            ];

            // Creamos las "columna LIKE ?"
            // Y agrupamos TODOS los ORs dentro de un paréntesis para proteger la lógica
            $searchClauses = array_map(fn($col) => "$col LIKE ?", $columns);
            $whereClauses[] = "(" . implode(" OR ", $searchClauses) . ")";

            // Rellenamos los parámetros posicionales uno por uno
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

                // Si el operador es LIKE, aplicamos la colación para ignorar acentos en los filtros individuales
                if ($op === 'LIKE') {
                    $whereClauses[] = "$column LIKE ? COLLATE utf8mb4_unicode_ci";
                    $params[] = "%" . $value . "%";
                } else {
                    $whereClauses[] = "$column $op ?";
                    $params[] = is_bool($value) ? (int)$value : $value;
                }
            }
        }

        // Construir los WHERE en el SQL
        if (!empty($whereClauses)) {
            $sql .= " WHERE " . implode(" AND ", $whereClauses);
        }

        $rows = $this->pdoQuery($sql, $params)->fetchAll();
        return array_map(
            $this->mapToCliente(...),
            $rows
        );
    }

    public function find(string $cedula): ?ClienteDTO
    {
        $row = $this->pdoQuery(
            "{$this->sqlSelect()} WHERE cedula_cliente = ?",
            [$cedula]
        )->fetch();

        return $row
            ? $this->mapToCliente($row)
            : null;
    }

    public function insert(ClienteDTO $cliente): ClienteDTO
    {
        $cliente->validateInsert();
        $this->pdo->beginTransaction();

        $this->personasModel->insert($cliente);
        $this->pdoInsert('cliente', [
            'cedula_cliente' => $cliente->cedula,
        ]);
        $cliente = $this->find($cliente->cedula);

        $this->pdo->commit();
        return $cliente;
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

        $cliente = $this->find($cliente->cedula);

        $this->pdo->commit();
        return $cliente;
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
