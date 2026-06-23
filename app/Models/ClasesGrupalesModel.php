<?php

namespace App\Models;

use App\Helpers\Validator;
use App\Models\BaseModel;
use CuyZ\Valinor\Mapper\TreeMapper;
use DateTimeImmutable;
use InvalidArgumentException;
use PDO;

enum EstadoClase: string
{
    case PROGRAMADO = "Programado";
    case EN_CURSO = "En curso";
    case FINALIZADO = "Finalizado";
    case CANCELADO = "Cancelado";
}

readonly class ClaseClienteDTO
{
    public function __construct(
        public string $cedula,
        public string $nombre,
        public string $apellido,
        public bool $asistio = false,
    ) {}
}

readonly class ClaseGrupalDTO
{
    public function __construct(
        public ?int $id_clase = null,
        public ?string $cedula_trabajador = null,
        /** @var ClaseClienteDTO[]|string[] */
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
            if ($cliente instanceof ClaseClienteDTO && !$cliente->cedula) {
                throw new InvalidArgumentException("Cada cliente debe tener una cédula.");
            }

            if ((is_string($cliente)) && empty($cliente)) {
                throw new InvalidArgumentException("El ID del cliente no puede estar vacío.");
            }
        }
    }

    public function validateInsert() {}
    public function validateUpdate() {}
}

class ClasesGrupalesModel extends BaseModel
{
    private string $table = 'clase';
    private string $primaryKey = 'id_clase';

    public function __construct(
        PDO $pdo,
        private TreeMapper $mapper,
    ) {
        parent::__construct($pdo);
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

    private function map(array $row): ClaseGrupalDTO
    {
        $clientes = json_decode($row["clientes"], true);
        $row["clientes"] = $clientes;
        return $this->mapper->map(ClaseGrupalDTO::class, $row);
    }

    /**
     * @return ClaseGrupalDTO[]
     */
    public function query(): array
    {
        $rows = $this->pdoQuery($this->sqlSelect())->fetchAll();
        return array_map(
            fn($row) => $this->map($row),
            $rows
        );
    }

    public function find(int $id): ?ClaseGrupalDTO
    {
        $row = $this->pdoQuery(
            $this->sqlSelect(" WHERE clase.{$this->primaryKey} = ? "),
            [$id]
        )->fetch();

        if (!$row) return null;
        return $this->map($row);
    }

    public function insert(ClaseGrupalDTO $clase): ClaseGrupalDTO
    {
        $clase->validateInsert();

        return $this->pdoTransaction(function () use ($clase) {
            $this->pdoInsert($this->table, $this->dtoToArray($clase));

            $id_clase = (int) $this->pdo->lastInsertId();
            $this->syncClientes($id_clase, $clase->clientes);

            return $this->find($id_clase);
        });
    }

    public function update(ClaseGrupalDTO $clase): ClaseGrupalDTO
    {
        $clase->validateUpdate();
        $this->pdo->beginTransaction();

        $array = $this->dtoToArray($clase);
        unset($array[$this->primaryKey]);

        $this->pdoUpdate(
            $this->table,
            $array,
            [$this->primaryKey => $clase->id_clase]
        );
        $this->syncClientes($clase->id_clase, $clase->clientes);

        $clase = $this->find($clase->id_clase);
        $this->pdo->commit();

        return $clase;
    }

    public function delete(int $id): void
    {
        $this->pdoDelete($this->table, [$this->primaryKey => $id]);
    }

    /** @param ClaseClienteDTO[]|array<string> $clientes */
    private function syncClientes(int $id_clase, array $clientes): void
    {
        $table = "clase_cliente";

        // Eliminar todos los clientes
        foreach ($clientes as $cliente) {
            $this->pdoDelete($table, ["id_clase" => $id_clase]);
        }

        // Insertar los nuevos clientes
        foreach ($clientes as $cliente) {
            // Extraer solo la cedula
            if ($cliente instanceof ClaseClienteDTO) {
                $cedula = $cliente->cedula;
            } else {
                $cedula = $cliente;
            }

            $this->pdoInsert($table, [
                "id_clase" => $id_clase,
                "cedula_cliente" => $cedula,
                "asistio" => $cliente->asistio,
            ]);
        }
    }

    private function dtoToArray(ClaseGrupalDTO $dto): array
    {
        return [
            'id_clase'          => $dto->id_clase,
            'cedula_trabajador' => $dto->cedula_trabajador,
            'nombre'            => $dto->nombre,
            'descripcion'       => $dto->descripcion,
            'capacidad_maxima'  => $dto->capacidad_maxima,
            'estado'            => $dto->estado->value,
            'fecha_inicio'      => Validator::dateToString($dto->fecha_inicio),
            'fecha_fin'         => Validator::dateToString($dto->fecha_fin),
        ];
    }
}
