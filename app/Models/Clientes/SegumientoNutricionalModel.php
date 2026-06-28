<?php

namespace App\Models\Clientes;

use App\Core\Database;
use App\Core\Validator;
use App\Models\Model;
use CuyZ\Valinor\Mapper\TreeMapper;
use DateTimeImmutable;
use InvalidArgumentException;

use function App\Core\toDbDate;

class SegumientoNutricionalModel extends Model
{
    private string $table = 'seguimiento_nutricional';
    private string $primaryKey = 'id_seguimiento';

    public function __construct(
        Database $db,
        private TreeMapper $mapper,
    ) {
        return parent::__construct($db);
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
        $rows = $this->db->dbQuery(
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
        $row = $this->db->dbQuery(
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
        $this->db->dbInsert($this->table, $this->dtoToArray($seguimiento));

        $id = (int) $this->db->lastInsertId();
        return $this->find($id);
    }

    /**
     * Actualiza un seguimiento existente.
     */
    public function update(SeguimientoNutricionalDTO $seguimiento): SeguimientoNutricionalDTO
    {
        $seguimiento->validateUpdate();
        $this->db->dbUpdate(
            $this->table,
            $this->dtoToArray($seguimiento),
            [$this->primaryKey => $seguimiento->id_seguimiento]
        );

        $id = (int) $this->db->lastInsertId();
        return $this->find($id);
    }

    /**
     * Elimina un seguimiento por ID.
     */
    public function delete(int $id): void
    {
        $this->db->dbDelete($this->table, [$this->primaryKey => $id]);
    }

    private function dtoToArray(SeguimientoNutricionalDTO $dto): array
    {
        $array = (array) $dto;
        $array["fecha"] = toDbDate($dto->fecha);
        return $array;
    }
}

// DTO
readonly class SeguimientoNutricionalDTO
{
    public function __construct(
        public ?int $id_seguimiento = null,
        public ?string $cedula_cliente = null,
        public ?string $registrado_por = null,
        public ?DateTimeImmutable $fecha = null,
        public ?float $proteinas_g = null,
        public ?float $carbohidratos_g = null,
        public ?float $grasas_g = null,
    ) {
        if ($this->cedula_cliente) {
            Validator::cedula($this->cedula_cliente, "cedula_cliente");
        }
    }

    public function validateInsert(): void
    {
        Validator::required($this->cedula_cliente, "cedula_cliente");

        // Al menos un valor debe existir
        $medidas = [
            $this->proteinas_g,
            $this->carbohidratos_g,
            $this->grasas_g,
        ];

        $todasVacias = true;
        foreach ($medidas as $medida) {
            if ($medida !== null) {
                $todasVacias = false;
                break;
            }
        }

        if ($todasVacias) {
            throw new InvalidArgumentException('Debe proporcionar al menos un valor');
        }
    }

    public function validateUpdate(): void
    {
        Validator::required($this->id_seguimiento, "id_seguimiento");
    }
}
