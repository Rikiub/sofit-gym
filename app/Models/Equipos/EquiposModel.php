<?php

namespace App\Models\Equipos;

use App\Models\BaseModel;
use CuyZ\Valinor\Mapper\TreeMapper;
use DateTimeImmutable;
use InvalidArgumentException;
use PDO;

enum EstadoEquipo: string
{
    case Operativo = 'Operativo';
    case Mantenimiento = 'Mantenimiento';
    case FueraDeServicio = 'Fuera de Servicio';
}

readonly class EquipoDTO
{
    public function __construct(
        public ?string $codigo = null,
        public ?string $nombre = null,
        public ?string $tipo = null,
        public ?EstadoEquipo $estado = null,
        public ?string $ubicacion = null,
        public ?bool $activo = true,
        public ?DateTimeImmutable $fecha_creacion = null,
    ) {}

    public function validateInsert(): void
    {
        if (empty($this->codigo)) {
            throw new InvalidArgumentException('El código del equipo es obligatorio');
        }
        if (empty($this->nombre)) {
            throw new InvalidArgumentException('El nombre del equipo es obligatorio');
        }
        if ($this->estado === null) {
            throw new InvalidArgumentException('El estado del equipo es obligatorio');
        }
    }

    public function validateUpdate(): void
    {
        if (empty($this->codigo)) {
            throw new InvalidArgumentException('El código del equipo es necesario para actualizar');
        }
    }
}

class EquiposModel extends BaseModel
{
    private string $table = 'equipo';
    private string $primaryKey = 'codigo_equipo';

    public function __construct(
        PDO $pdo,
        private TreeMapper $mapper,
    ) {
        return parent::__construct($pdo);
    }

    private function sqlSelect(string $where = ""): string
    {
        return <<<SQL
                SELECT
                    codigo_equipo AS `codigo`,
                    equipo.*
                FROM {$this->table} equipo
                {$where}
                ORDER BY fecha_creacion DESC
            SQL;
    }

    /**
     * @return EquipoDTO[]
     */
    public function query(): array
    {
        $rows = $this->pdoQuery($this->sqlSelect())->fetchAll();
        return array_map(
            fn($row) => $this->mapper->map(EquipoDTO::class, $row),
            $rows
        );
    }

    public function find(string $codigo): ?EquipoDTO
    {
        $row = $this->pdoQuery(
            $this->sqlSelect("WHERE {$this->primaryKey} = ?"),
            [$codigo]
        )->fetch();

        if (!$row)
            return null;
        return $this->mapper->map(EquipoDTO::class, $row);
    }

    public function insert(EquipoDTO $equipo): EquipoDTO
    {
        $equipo->validateInsert();

        $this->pdoInsert($this->table, $this->dtoToArray($equipo));
        return $this->find($equipo->codigo);
    }

    public function update(EquipoDTO $equipo): EquipoDTO
    {
        $equipo->validateUpdate();

        $array = $this->dtoToArray($equipo);
        unset($array['codigo_equipo']);

        $this->pdoUpdate(
            $this->table,
            $array,
            [$this->primaryKey => $equipo->codigo],
        );

        return $this->find($equipo->codigo);
    }

    public function delete(string $codigo): void
    {
        $this->pdoDelete($this->table, [$this->primaryKey => $codigo]);
    }

    private function dtoToArray(EquipoDTO $dto): array
    {
        return [
            'codigo_equipo' => $dto->codigo,
            'nombre' => $dto->nombre,
            'tipo' => $dto->tipo,
            'estado' => $dto->estado->value,
            'ubicacion' => $dto->ubicacion,
            'activo' => $dto->activo,
        ];
    }
}
