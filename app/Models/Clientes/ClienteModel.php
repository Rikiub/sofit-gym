<?php

namespace App\Models\Clientes;

use App\Core\Tools;
use App\Models\BitacoraModel;
use App\Models\Database;
use App\Models\Personas\PersonaModel;

class ClienteModel extends Database
{
    public string $table = 'cliente';
    public string $primaryKey = 'cedula';

    public function __construct(
        private BitacoraModel $logger = new BitacoraModel(),
        private PersonaModel $personaModel = new PersonaModel(),
    ) {
        parent::__construct();
    }

    /** Resumen estadisticos */
    public function getSummary(): array
    {
        $rows = $this->dbQuery(<<<SQL
            SELECT 
                -- Cantidad de clientes totales
                (SELECT COUNT(*) FROM cliente) AS total_clientes,

                -- Número de membresías activas (aún vigentes)
                (SELECT COUNT(*) 
                    FROM membresia 
                    WHERE fecha_fin >= CURDATE()) AS membresias_activas,

                    -- Ganancias del mes actual por membresías de clientes con membresía activa
                    (SELECT COALESCE(SUM(p.monto), 0)
                        FROM pago p
                        WHERE YEAR(p.fecha_pago) = YEAR(CURDATE())
                            AND MONTH(p.fecha_pago) = MONTH(CURDATE())
                            AND p.cedula_cliente IS NOT NULL
                            AND p.id_pago NOT IN (SELECT id_pago FROM venta_producto)
                            AND EXISTS (
                                SELECT 1
                                FROM membresia m
                                WHERE m.cedula_cliente = p.cedula_cliente
                                    AND m.fecha_fin >= CURDATE()
                            )
                    ) AS ganancias_totales;
        SQL)->fetch();

        return $rows;
    }

    /**
     * @return Cliente[]
     */
    public function query(?string $search = null, array $filters = []): array
    {
        $whereClauses = [];
        $params = [];

        // Busqueda global
        if ($search) {
            $search = trim($search);

            $columns = [
                'persona.cedula',
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
            'estado_membresia' => ['column' => 'me.nombre', 'op' => '=']
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

        $rows = $this->dbQuery($sql, $params)->fetchAll();
        $this->logger->log(
            "Clientes listados",
            [
                "modulo" => "clientes",
                "accion" => "listar",
            ]
        );
        return array_map(
            $this->mapToCliente(...),
            $rows
        );
    }

    public function find(string $cedula): ?Cliente
    {
        $row = $this->dbQuery(
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
        return (bool)$this->personaModel->find($cedula);
    }

    public function insert(Cliente $cliente): Cliente
    {
        $cliente->validateInsert();

        return $this->dbTransaction(function () use ($cliente) {
            $this->personaModel->insert($cliente);
            $this->dbInsert(
                $this->table,
                [$this->primaryKey => $cliente->cedula]
            );

            $this->logger->log(
                "Cliente '{cedula}' creado",
                [
                    "modulo" => "clientes",
                    "accion" => "crear",

                    "cedula" => $cliente->cedula,
                    "datos_nuevos" => $cliente,
                ],
            );

            return $this->find($cliente->cedula);
        });
    }

    public function update(string $cedula, Cliente $cliente): Cliente
    {
        $this->personaModel->update($cedula, $cliente);
        $this->logger->log(
            "Cliente '{cedula}' actualizado",
            [
                "modulo" => "clientes",
                "accion" => "editar",

                "cedula" => $cliente->cedula,
                "datos_nuevos" => $cliente,
            ],
        );
        return $this->find($cliente->cedula);
    }

    public function delete(string $cedula): void
    {
        $this->dbDelete($this->table, [$this->primaryKey => $cedula]);
        $this->logger->log(
            "Cliente '{cedula}' eliminado",
            [
                "modulo" => "clientes",
                "accion" => "eliminar",
                "cedula" => $cedula
            ]
        );
    }

    private function mapToCliente(array $row): Cliente
    {
        $membresia = $row['membresia'] ?? null;
        if ($membresia) {
            $row['membresia'] = json_decode($membresia, true);
        }
        return Tools::map(Cliente::class, $row);
    }

    private function sqlSelect(string $where = ""): string
    {
        return <<<SQL
                SELECT
                    persona.*,
                    CONCAT(persona.nombre, ' ', persona.apellido) AS nombre_completo,
                    IF(m.fecha_fin >= CURDATE(),
                        JSON_OBJECT(
                            "id_membresia", m.id_membresia,
                            "id_tipo", m.id_tipo,
                            "estado", me.nombre,
                            "id_estado", m.id_estado,
                            "tipo", mt.nombre,
                            "fecha_inicio", m.fecha_inicio,
                            "fecha_fin", m.fecha_fin
                        ),
                        NULL
                    ) AS membresia 
                FROM cliente
                LEFT JOIN persona ON persona.cedula = cliente.cedula
                LEFT JOIN (
                    SELECT 
                        m1.*, 
                        ROW_NUMBER() OVER (PARTITION BY m1.cedula_cliente ORDER BY m1.fecha_inicio DESC) as rn
                    FROM membresia m1
                ) m ON m.cedula_cliente = cliente.cedula AND m.rn = 1
                LEFT JOIN tipo_membresia mt ON m.id_tipo = mt.id_tipo
                LEFT JOIN estado_membresia me ON m.id_estado = me.id_estado
                {$where} 
                ORDER BY
                    CASE
                        WHEN m.id_membresia IS NULL THEN 3
                        WHEN m.fecha_fin >= CURDATE() THEN 1
                        ELSE 2
                    END ASC,
                    CASE
                        WHEN m.id_membresia IS NULL THEN '9999-12-31'
                        ELSE m.fecha_fin
                    END ASC;
            SQL;
    }
}
