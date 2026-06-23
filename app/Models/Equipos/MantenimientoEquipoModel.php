<?php

namespace App\Models\Equipos;

use App\Helpers\Validator;
use App\Models\BaseModel;
use CuyZ\Valinor\Mapper\TreeMapper;
use DateTimeImmutable;
use InvalidArgumentException;
use PDO;

enum TipoMantenimiento: string
{
    case Preventivo = 'Preventivo';
    case Correctivo = 'Correctivo';
}

readonly class MantenimientoEquipoDTO
{
    public function __construct(
        public ?int $id_mantenimiento = null,
        public ?string $codigo_equipo = null,
        public ?string $cedula_trabajador = null,
        public ?DateTimeImmutable $fecha = null,
        public ?TipoMantenimiento $tipo = null,
        public ?string $descripcion = null,
        public ?float $costo = null,
        public ?EquipoDTO $equipo = null,
    ) {}

    public function validateInsert(): void
    {
        $this->validateShared();

        if (!$this->fecha) {
            throw new InvalidArgumentException('La fecha del mantenimiento es obligatoria');
        }
        if ($this->tipo === null) {
            throw new InvalidArgumentException('El tipo de mantenimiento es obligatorio');
        }
        if ($this->costo !== null && $this->costo < 0) {
            throw new InvalidArgumentException('El costo no puede ser negativo');
        }
    }

    public function validateUpdate()
    {
        $this->validateShared();

        if (!$this->id_mantenimiento) {
            throw new InvalidArgumentException('El ID de mantenimiento es necesario para actualizar');
        }
    }

    private function validateShared()
    {
        if (!$this->codigo_equipo) {
            throw new InvalidArgumentException('El código de equipo es obligatorio');
        }
    }
}

class MantenimientoEquipoModel extends BaseModel
{
    private string $table = 'mantenimiento_equipo';
    private string $primaryKey = 'id_mantenimiento';

    public function __construct(
        PDO $pdo,
        private TreeMapper $mapper,
        private EquiposModel $equiposModel,
    ) {
        return parent::__construct($pdo);
    }

    private function sqlSelect(string $where = ""): string
    {
        return <<<SQL
                SELECT * FROM {$this->table}
                {$where}
                ORDER BY fecha DESC
            SQL;
    }

    private function mapToMantenimiento(array $row): MantenimientoEquipoDTO
    {
        $row['equipo'] = $this->equiposModel->find($row['codigo_equipo']);
        $mantenimiento = $this->mapper->map(MantenimientoEquipoDTO::class, $row);
        return $mantenimiento;
    }

    /**
     * @return MantenimientoEquipoDTO[]
     */
    public function query(): array
    {
        $rows = $this->pdoQuery($this->sqlSelect())->fetchAll();
        return array_map(
            fn($row) => $this->mapToMantenimiento($row),
            $rows
        );
    }

    public function find(int $id): ?MantenimientoEquipoDTO
    {
        $row = $this->pdoQuery(
            $this->sqlSelect("WHERE {$this->primaryKey} = ?"),
            [$id]
        )->fetch();

        return $row
            ? $this->mapToMantenimiento($row)
            : null;
    }

    public function insert(MantenimientoEquipoDTO $mantenimiento): MantenimientoEquipoDTO
    {
        $mantenimiento->validateInsert();

        // Verificar que el equipo exista
        $equipo = $this->equiposModel->find($mantenimiento->codigo_equipo);

        if (!$equipo || !$equipo->activo) {
            throw new InvalidArgumentException("El equipo con código {$mantenimiento->codigo_equipo} no existe o está inactivo");
        }

        $this->pdoInsert($this->table, $this->dtoToArray($mantenimiento));

        $id = (int) $this->pdo->lastInsertId();
        return $this->find($id);
    }

    public function update(MantenimientoEquipoDTO $mantenimiento): MantenimientoEquipoDTO
    {
        $mantenimiento->validateUpdate();

        // Verificar que el equipo exista
        $equipo = $this->equiposModel->find($mantenimiento->codigo_equipo);

        if (!$equipo || !$equipo->activo) {
            throw new InvalidArgumentException("El equipo con código {$mantenimiento->codigo_equipo} no existe o está inactivo");
        }

        $array = $this->dtoToArray($mantenimiento);

        $this->pdoUpdate(
            $this->table,
            $array,
            [$this->primaryKey => $mantenimiento->id_mantenimiento],
        );

        return $this->find($mantenimiento->id_mantenimiento);
    }

    public function delete(int $id): void
    {
        $this->pdoDelete($this->table, [$this->primaryKey => $id]);
    }

    private function dtoToArray(MantenimientoEquipoDTO $dto): array
    {
        return [
            'codigo_equipo' => $dto->codigo_equipo,
            'cedula_trabajador' => $dto->cedula_trabajador,
            'fecha' => Validator::dateToString($dto->fecha),
            'tipo' => $dto->tipo->value,
            'descripcion' => $dto->descripcion,
            'costo' => $dto->costo,
        ];
    }
}
