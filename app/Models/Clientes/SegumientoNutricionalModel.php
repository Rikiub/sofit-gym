<?php

namespace App\Models\Clientes;

use App\Helpers\Validator;
use App\Models\BaseModel;
use CuyZ\Valinor\Mapper\TreeMapper;
use DateTimeImmutable;
use InvalidArgumentException;
use PDO;

readonly class SeguimientoNutricionalDTO
{
    public function __construct(
        public ?int $id_seguimiento = null,
        public ?string $cedula_cliente = null,
        public ?DateTimeImmutable $fecha = null,
        public ?float $proteinas_g = null,
        public ?float $carbohidratos_g = null,
        public ?float $grasas_g = null,
    ) {}

    public function validateInsert(): void
    {
        if (!$this->cedula_cliente) {
            throw new InvalidArgumentException('Debe tener una cédula de cliente');
        }
        if (!$this->fecha) {
            throw new InvalidArgumentException('Debe tener una fecha de seguimiento');
        }
    }

    public function validateUpdate(): void
    {
        if (!$this->id_seguimiento) {
            throw new InvalidArgumentException('Se requiere id_seguimiento para actualizar');
        }
    }
}

class SegumientoNutricionalModel extends BaseModel
{
    private string $table = 'seguimiento_nutricional';
    private string $primaryKey = 'id_seguimiento';

    public function __construct(
        PDO $pdo,
        private TreeMapper $mapper,
    ) {
        return parent::__construct($pdo);
    }

    private function sqlSelect(): string
    {
        return <<<SQL
                SELECT * FROM {$this->table}
            SQL;
    }

    /**
     * Obtiene todos los seguimientos de un cliente.
     * @return SeguimientoNutricionalDTO[]
     */
    public function queryByCliente(string $cedula): array
    {
        $rows = $this->pdoQuery(
            <<<SQL
                {$this->sqlSelect()} 
                WHERE cedula_cliente = ?
                ORDER BY fecha DESC
            SQL,
            [$cedula]
        )->fetchAll();

        return array_map(
            fn($row) => $this->mapper->map(SeguimientoNutricionalDTO::class, $row),
            $rows
        );
    }

    /**
     * Busca un seguimiento por su ID.
     */
    public function find(int $id): ?SeguimientoNutricionalDTO
    {
        $row = $this->pdoQuery(
            "{$this->sqlSelect()} WHERE {$this->primaryKey} = ?",
            [$id]
        )->fetch();

        if (!$row)
            return null;
        return $this->mapper->map(SeguimientoNutricionalDTO::class, $row);
    }

    /**
     * Inserta un nuevo seguimiento.
     */
    public function insert(SeguimientoNutricionalDTO $seguimiento): SeguimientoNutricionalDTO
    {
        $seguimiento->validateInsert();
        $this->pdoInsert($this->table, $this->dtoToArray($seguimiento));

        $id = (int) $this->pdo->lastInsertId();
        return $this->find($id);
    }

    /**
     * Actualiza un seguimiento existente.
     */
    public function update(SeguimientoNutricionalDTO $seguimiento): SeguimientoNutricionalDTO
    {
        $seguimiento->validateUpdate();
        $this->pdoUpdate(
            $this->table,
            $this->dtoToArray($seguimiento),
            [$this->primaryKey => $seguimiento->id_seguimiento]
        );

        $id = (int) $this->pdo->lastInsertId();
        return $this->find($id);
    }

    /**
     * Elimina un seguimiento por ID.
     */
    public function delete(int $id): void
    {
        $this->pdoDelete($this->table, [$this->primaryKey => $id]);
    }

    private function dtoToArray(SeguimientoNutricionalDTO $dto): array
    {
        $array = (array) $dto;
        $array["fecha"] = Validator::dateToString($dto->fecha);
        return $array;
    }
}
