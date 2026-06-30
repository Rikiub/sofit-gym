<?php

namespace App\Models\Equipos;

use App\Core\Database;
use App\Core\Validator;
use App\Models\Model;
use CuyZ\Valinor\Mapper\TreeMapper;
use DateTimeImmutable;

class EquiposModel extends Model
{
    private string $table = 'equipo';
    private string $primaryKey = 'codigo_equipo';

    public function __construct(
        Database $db,
        private TreeMapper $mapper,
    ) {
        return parent::__construct($db);
    }

    /**
     * @return EquipoDTO[]
     */
    public function query(): array
    {
        $rows = $this->db->dbQuery($this->sqlSelect())->fetchAll();
        return array_map(
            fn($row) => $this->mapper->map(EquipoDTO::class, $row),
            $rows
        );
    }

    public function find(string $codigo): ?EquipoDTO
    {
        $row = $this->db->dbQuery(
            $this->sqlSelect("WHERE {$this->primaryKey} = ?"),
            [$codigo]
        )->fetch();

        return $row
            ? $this->mapper->map(EquipoDTO::class, $row)
            : null;
    }

    public function insert(EquipoDTO $equipo): EquipoDTO
    {
        $equipo->validateInsert();

        $this->db->dbInsert(
            $this->table,
            $this->mapToColumns($equipo, includeId: true),
        );

        return $this->find($equipo->codigo_equipo);
    }

    public function update(string $codigo, EquipoDTO $equipo): EquipoDTO
    {
        $this->db->dbUpdate(
            $this->table,
            $this->mapToColumns($equipo),
            [$this->primaryKey => $codigo],
        );
        return $this->find($codigo);
    }

    public function delete(string $codigo): void
    {
        $this->db->dbDelete($this->table, [$this->primaryKey => $codigo]);
    }

    private function mapToColumns(EquipoDTO $dto, bool $includeId = false): array
    {
        $data = [
            'nombre' => $dto->nombre,
            'tipo' => $dto->tipo,
            'estado' => $dto->estado->value,
            'ubicacion' => $dto->ubicacion,
            'activo' => $dto->activo,
        ];

        if ($includeId) {
            $data[$this->primaryKey] = $dto->codigo_equipo;
        }

        return $data;
    }

    private function sqlSelect(string $where = ""): string
    {
        return <<<SQL
                SELECT *
                FROM {$this->table}
                {$where}
                ORDER BY fecha_creacion DESC
            SQL;
    }
}

// DTO
enum EstadoEquipo: string
{
    case Operativo = 'Operativo';
    case Mantenimiento = 'Mantenimiento';
    case FueraDeServicio = 'Fuera de Servicio';
}

readonly class EquipoDTO
{
    public function __construct(
        public ?string $codigo_equipo = null,
        public ?string $nombre = null,
        public ?string $tipo = null,
        public ?EstadoEquipo $estado = null,
        public ?string $ubicacion = null,
        public ?bool $activo = true,
        public ?DateTimeImmutable $fecha_creacion = null,
    ) {}

    public function validateInsert(): void
    {
        Validator::required($this->codigo_equipo, "codigo_equipo");
        Validator::required($this->nombre, "nombre");
        Validator::required($this->estado, "estado");
    }
}
