<?php

namespace App\Models\Equipos;

use App\Core\Database;
use App\Core\Validator;
use App\Models\Model;
use CuyZ\Valinor\Mapper\TreeMapper;
use DateTimeImmutable;
use InvalidArgumentException;

use function App\Core\toDbDate;

class MantenimientoEquipoModel extends Model
{
    private string $table = 'mantenimiento_equipo';
    private string $primaryKey = 'id_mantenimiento';

    public function __construct(
        Database $db,
        private TreeMapper $mapper,
        private EquiposModel $equiposModel,
    ) {
        return parent::__construct($db);
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
        $rows = $this->db->pdoQuery($this->sqlSelect())->fetchAll();
        return array_map(
            fn($row) => $this->mapToMantenimiento($row),
            $rows
        );
    }

    public function find(int $id): ?MantenimientoEquipoDTO
    {
        $row = $this->db->pdoQuery(
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

        $this->db->pdoInsert($this->table, $this->dtoToArray($mantenimiento));

        $id = (int) $this->db->lastInsertId();
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

        $this->db->pdoUpdate(
            $this->table,
            $this->dtoToArray($mantenimiento),
            [$this->primaryKey => $mantenimiento->id_mantenimiento],
        );

        return $this->find($mantenimiento->id_mantenimiento);
    }

    public function delete(int $id): void
    {
        $this->db->pdoDelete($this->table, [$this->primaryKey => $id]);
    }

    private function dtoToArray(MantenimientoEquipoDTO $dto): array
    {
        return [
            'codigo_equipo' => $dto->codigo_equipo,
            'cedula_trabajador' => $dto->cedula_trabajador,
            'fecha' => toDbDate($dto->fecha),
            'tipo' => $dto->tipo->value,
            'descripcion' => $dto->descripcion,
            'costo' => $dto->costo,
        ];
    }
}

// DTO
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
        Validator::required($this->tipo, "tipo");

        if ($this->costo !== null && $this->costo < 0) {
            throw new InvalidArgumentException('El costo no puede ser negativo');
        }
    }

    public function validateUpdate()
    {
        $this->validateShared();
        Validator::required($this->id_mantenimiento, "id_mantenimiento");
    }

    private function validateShared()
    {
        Validator::required($this->codigo_equipo, "codigo_equipo");
    }
}
