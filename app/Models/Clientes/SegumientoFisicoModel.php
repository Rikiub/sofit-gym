<?php

namespace App\Models\Clientes;

use App\Core\Database;
use App\Core\Validator;
use App\Models\Model;
use CuyZ\Valinor\Mapper\TreeMapper;
use DateTimeImmutable;
use InvalidArgumentException;

use function App\Core\toDbDate;

class SegumientoFisicoModel extends Model
{
    private string $table = 'seguimiento_fisico';
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
                SELECT *
                FROM {$this->table}
            SQL;
    }

    /**
     * Obtiene todos los seguimientos de un cliente.
     * @return SeguimientoFisicoDTO[]
     */
    public function queryByCliente(string $cedula): array
    {
        $rows = $this->db->pdoQuery(
            <<<SQL
                {$this->sqlSelect()} 
                WHERE cedula_cliente = ?
                ORDER BY fecha DESC
            SQL,
            [$cedula],
        )->fetchAll();

        return array_map(
            fn($row) => $this->mapper->map(SeguimientoFisicoDTO::class, $row),
            $rows
        );
    }

    /**
     * Busca un seguimiento por su ID.
     */
    public function find(int $id): ?SeguimientoFisicoDTO
    {
        $row = $this->db->pdoQuery(
            "{$this->sqlSelect()} WHERE {$this->primaryKey} = ?",
            [$id],
        )->fetch();

        if (!$row)
            return null;
        return $this->mapper->map(SeguimientoFisicoDTO::class, $row);
    }

    /**
     * Inserta un nuevo seguimiento.
     */
    public function insert(SeguimientoFisicoDTO $seguimiento): SeguimientoFisicoDTO
    {
        $seguimiento->validateInsert();
        $this->db->pdoInsert($this->table, $this->dtoToArray($seguimiento),);

        $id = (int) $this->db->lastInsertId();
        return $this->find($id);
    }

    /**
     * Actualiza un seguimiento existente.
     */
    public function update(SeguimientoFisicoDTO $seguimiento): SeguimientoFisicoDTO
    {
        $seguimiento->validateUpdate();

        $this->db->pdoUpdate(
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
        $this->db->pdoDelete($this->table, [$this->primaryKey => $id]);
    }

    private function dtoToArray(SeguimientoFisicoDTO $dto): array
    {
        $array = (array) $dto;
        $array["fecha"] = toDbDate($dto->fecha);
        return $array;
    }
}

// DTO
readonly class SeguimientoFisicoDTO
{
    public function __construct(
        public ?int $id_seguimiento = null,
        public ?string $cedula_cliente = null,
        public ?string $registrado_por = null,
        public ?DateTimeImmutable $fecha = null,
        public ?float $altura_cm = null,
        public ?float $peso_kg = null,
        public ?float $cintura_cm = null,
        public ?float $cadera_cm = null,
        public ?float $pecho_cm = null,
        public ?float $muslo_cm = null,
        public ?float $hombros_cm = null,
        public ?float $pantorrilla_cm = null,
    ) {
        if ($this->cedula_cliente) {
            Validator::cedula($this->cedula_cliente, "cedula_cliente");
        }
    }

    public function validateInsert(): void
    {
        Validator::required($this->cedula_cliente, "cedula_cliente");

        // Al menos una medida numérica debe existir
        $medidas = [
            $this->altura_cm,
            $this->peso_kg,
            $this->cintura_cm,
            $this->cadera_cm,
            $this->pecho_cm,
            $this->muslo_cm,
            $this->hombros_cm,
            $this->pantorrilla_cm,
        ];

        $todasVacias = true;
        foreach ($medidas as $medida) {
            if ($medida !== null) {
                $todasVacias = false;
                break;
            }
        }

        if ($todasVacias) {
            throw new InvalidArgumentException('Debe proporcionar al menos una medida');
        }
    }

    public function validateUpdate()
    {
        Validator::required($this->id_seguimiento, "id_seguimiento");
    }
}
