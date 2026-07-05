<?php

namespace App\Models;

use App\Core\Database;
use App\Core\Validator;
use App\Models\Model;
use CuyZ\Valinor\Mapper\TreeMapper;
use DateTimeImmutable;
use InvalidArgumentException;

use function App\Core\toDbDate;

class ClaseGrupalModel extends Model
{
    private string $table = 'clase';
    private string $primaryKey = 'id_clase';

    public function __construct(
        Database $db,
        private TreeMapper $mapper,
    ) {
        parent::__construct($db);
    }

    /**
     * @return ClaseGrupal[]
     */
    public function query(): array
    {
        $rows = $this->db->dbQuery($this->sqlSelect())->fetchAll();
        return array_map(
            fn($row) => $this->mapToClase($row),
            $rows
        );
    }

    public function find(int $id): ?ClaseGrupal
    {
        $row = $this->db->dbQuery(
            $this->sqlSelect(where: "WHERE clase.{$this->primaryKey} = ? "),
            [$id]
        )->fetch();

        return $row
            ? $this->mapToClase($row)
            : null;
    }

    public function insert(ClaseGrupal $clase): ClaseGrupal
    {
        $clase->validateInsert();

        return $this->db->dbTransaction(function () use ($clase) {
            $this->db->dbInsert(
                $this->table,
                $this->mapToColumns($clase)
            );

            $id_clase = (int) $this->db->lastInsertId();
            $this->syncClientes($id_clase, $clase->clientes);

            return $this->find($id_clase);
        });
    }

    public function update(int $id, ClaseGrupal $clase): ClaseGrupal
    {
        return $this->db->dbTransaction(function () use ($id, $clase) {
            $this->db->dbUpdate(
                $this->table,
                $this->mapToColumns($clase),
                [$this->primaryKey => $id]
            );
            $this->syncClientes($id, $clase->clientes);

            return $this->find($clase->id_clase);
        });
    }

    public function delete(int $id): void
    {
        $this->db->dbDelete($this->table, [$this->primaryKey => $id]);
    }

    /** @param ClaseCliente[]|array<string> $clientes */
    private function syncClientes(int $id_clase, array $clientes): void
    {
        $table = "clase_cliente";

        // Eliminar todos los clientes
        foreach ($clientes as $cliente) {
            $this->db->dbDelete($table, ["id_clase" => $id_clase]);
        }

        // Insertar los nuevos clientes
        foreach ($clientes as $cliente) {
            // Extraer solo la cedula
            if ($cliente instanceof ClaseCliente) {
                $cedula = $cliente->cedula;
            } else {
                $cedula = $cliente;
            }

            $this->db->dbInsert($table, [
                "id_clase" => $id_clase,
                "cedula_cliente" => $cedula,
                "asistio" => $cliente->asistio,
            ]);
        }
    }

    private function mapToClase(array $row): ClaseGrupal
    {
        $clientes = json_decode($row["clientes"], true);
        $row["clientes"] = $clientes;
        return $this->mapper->map(ClaseGrupal::class, $row);
    }

    private function mapToColumns(ClaseGrupal $dto): array
    {
        return [
            'cedula_trabajador' => $dto->cedula_trabajador,
            'nombre'            => $dto->nombre,
            'descripcion'       => $dto->descripcion,
            'capacidad_maxima'  => $dto->capacidad_maxima,
            'estado'            => $dto->estado->value,
            'fecha_inicio'      => toDbDate($dto->fecha_inicio),
            'fecha_fin'         => toDbDate($dto->fecha_fin),
        ];
    }

    private function sqlSelect(string $where = ""): string
    {
        return <<<SQL
            SELECT
                clase.*,
                COUNT(cc.id_clase) AS `capacidad_actual`,
                COALESCE(
                (
                    SELECT CONCAT('[', GROUP_CONCAT(
                        JSON_OBJECT(
                            'cedula', cliente.cedula,
                            'nombre', cliente.nombre,
                            'apellido', cliente.apellido,
                            'asistio', cc_sub.asistio
                        )
                    ), ']')
                    FROM clase_cliente cc_sub
                    LEFT JOIN persona cliente
                        ON cliente.cedula = cc_sub.cedula_cliente
                    WHERE cc_sub.id_clase = clase.id_clase
                ),
                '[]'
                ) AS clientes
            FROM {$this->table} clase
            LEFT JOIN clase_cliente cc
                ON clase.id_clase = cc.id_clase
            {$where}
            GROUP BY clase.id_clase
        SQL;
    }
}

// DTOs
readonly class ClaseGrupal
{
    public function __construct(
        public ?int $id_clase = null,
        public ?string $cedula_trabajador = null,
        /** @var ClaseCliente[]|string[] */
        public array $clientes = [],
        public ?string $nombre = null,
        public ?string $descripcion = null,
        public ?int $capacidad_actual = 0,
        public ?int $capacidad_maxima = null,
        public ?EstadoClase $estado = null,
        public ?DateTimeImmutable $fecha_inicio = null,
        public ?DateTimeImmutable $fecha_fin = null,
    ) {
        foreach ($this->clientes as $cliente) {
            if ($cliente instanceof ClaseCliente && !$cliente->cedula) {
                throw new InvalidArgumentException("Cada cliente debe tener una cédula");
            }

            if ((is_string($cliente)) && empty($cliente)) {
                throw new InvalidArgumentException("El ID del cliente no puede estar vacío");
            }
        }
    }

    public function validateInsert()
    {
        Validator::required($this->cedula_trabajador, "cedula_trabajador");
        Validator::required($this->nombre, "nombre");
        Validator::required($this->descripcion, "descripcion");

        Validator::required($this->capacidad_maxima, "capacidad_maxima");
        if ($this->capacidad_maxima <= 0) {
            throw new InvalidArgumentException("Se requiere como minimo una capacidad maxima mayor a 1");
        }
    }
}

readonly class ClaseCliente
{
    public function __construct(
        public string $cedula,
        public string $nombre,
        public string $apellido,
        public bool $asistio = false,
    ) {}
}

enum EstadoClase: string
{
    case PROGRAMADO = "Programado";
    case EN_CURSO = "En curso";
    case FINALIZADO = "Finalizado";
    case CANCELADO = "Cancelado";
}
