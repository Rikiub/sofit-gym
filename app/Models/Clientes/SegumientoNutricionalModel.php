<?php

namespace App\Models\Clientes;

use App\Core\Tools;
use App\Models\Model;
use DateTimeImmutable;
use InvalidArgumentException;

use function App\Core\toDbDate;

class SegumientoNutricionalModel extends Model
{
    private string $table = 'seguimiento_nutricional';
    private string $primaryKey = 'id_seguimiento';

    /**
     * Obtiene todos los seguimientos de un cliente.
     * @return SeguimientoNutricional[]
     */
    public function queryByCliente(string $cedula): array
    {
        $rows = $this->db->dbQuery(
            $this->sqlSelect(where: <<<SQL
                WHERE cedula_cliente = ?
                ORDER BY fecha DESC
            SQL),
            [$cedula]
        )->fetchAll();

        return array_map(
            fn($row) => Tools::map(SeguimientoNutricional::class, $row),
            $rows
        );
    }

    /**
     * Busca un seguimiento por su ID.
     */
    public function find(int $id): ?SeguimientoNutricional
    {
        $row = $this->db->dbQuery(
            $this->sqlSelect(where: "WHERE {$this->primaryKey} = ?"),
            [$id]
        )->fetch();

        return $row
            ? Tools::map(SeguimientoNutricional::class, $row)
            : null;
    }

    /**
     * Inserta un nuevo seguimiento.
     */
    public function insert(string $cedula_cliente, SeguimientoNutricional $seguimiento): SeguimientoNutricional
    {
        $seguimiento->validateInsert();

        $this->db->dbInsert(
            $this->table,
            [
                ...$this->mapToColumns($seguimiento),
                "cedula_cliente" => $cedula_cliente
            ]
        );

        $id = (int) $this->db->lastInsertId();
        return $this->find($id);
    }

    /**
     * Actualiza un seguimiento existente.
     */
    public function update(int $id, SeguimientoNutricional $seguimiento): SeguimientoNutricional
    {
        $this->db->dbUpdate(
            $this->table,
            $this->mapToColumns($seguimiento),
            [$this->primaryKey => $id]
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

    private function sqlSelect(string $where = ""): string
    {
        return <<<SQL
                SELECT *
                FROM {$this->table}
                {$where}
            SQL;
    }

    private function mapToColumns(SeguimientoNutricional $dto): array
    {
        $array = (array) $dto;
        $array["fecha"] = toDbDate($dto->fecha);
        unset($array["cedula_cliente"]);
        return $array;
    }
}

// DTO
readonly class SeguimientoNutricional
{
    public function __construct(
        public ?int $id_seguimiento = null,
        public ?string $cedula_cliente = null,
        public ?string $registrado_por = null,
        public ?DateTimeImmutable $fecha = null,
        public ?float $proteinas_g = null,
        public ?float $carbohidratos_g = null,
        public ?float $grasas_g = null,
    ) {}

    public function validateInsert(): void
    {
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
}
