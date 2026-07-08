<?php

namespace App\Models;

use App\Core\Tools;
use App\Core\Validator;
use App\Models\Model;
use DateTimeImmutable;
use InvalidArgumentException;

use function App\Core\toDbDate;

class MantenimientoEquipoModel extends Model
{
    private string $table = 'mantenimiento_equipo';
    private string $primaryKey = 'id_mantenimiento';

    public function __construct(
        private $equipoModel = new EquipoModel(),
    ) {
        return parent::__construct();
    }

    /**
     * @return MantenimientoEquipo[]
     */
    public function query(): array
    {
        $rows = $this->db->dbQuery($this->sqlSelect())->fetchAll();
        return array_map(
            fn($row) => $this->mapToMantenimiento($row),
            $rows
        );
    }

    /**
     * Obtiene mantenimientos preventivos programados para los próximos X días
     */
    public function getMantenimientosProximos(int $dias): array
    {
        $sql = "SELECT me.*, e.nombre AS equipo_nombre
            FROM mantenimiento_equipo me
            JOIN equipo e ON me.codigo_equipo = e.codigo_equipo
            WHERE me.tipo = 'Preventivo'
              AND me.fecha BETWEEN CURDATE() + INTERVAL 1 DAY AND CURDATE() + INTERVAL ? DAY
              AND e.activo = 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$dias]);
        return $stmt->fetchAll();
    }

    public function find(int $id): ?MantenimientoEquipo
    {
        $row = $this->db->dbQuery(
            $this->sqlSelect(where: "WHERE {$this->primaryKey} = ?"),
            [$id]
        )->fetch();

        return $row
            ? $this->mapToMantenimiento($row)
            : null;
    }

    public function insert(MantenimientoEquipo $mantenimiento): MantenimientoEquipo
    {
        $mantenimiento->validateInsert();

        $this->db->dbInsert(
            table: $this->table,
            data: $this->mapToColumns($mantenimiento),
        );

        $id = (int) $this->db->lastInsertId();
        return $this->find($id);
    }

    public function update(string $id, MantenimientoEquipo $mantenimiento): MantenimientoEquipo
    {
        $this->db->dbUpdate(
            table: $this->table,
            data: $this->mapToColumns($mantenimiento),
            conditions: [$this->primaryKey => $mantenimiento->id_mantenimiento],
        );
        return $this->find($id);
    }

    public function delete(int $id): void
    {
        $this->db->dbDelete($this->table, [$this->primaryKey => $id]);
    }

    private function sqlSelect(string $where = ""): string
    {
        return <<<SQL
                SELECT * FROM {$this->table}
                {$where}
                ORDER BY fecha DESC
            SQL;
    }

    private function mapToMantenimiento(array $row): MantenimientoEquipo
    {
        $row['equipo'] = $this->equipoModel->find($row['codigo_equipo']);
        $mantenimiento = Tools::map(MantenimientoEquipo::class, $row);
        return $mantenimiento;
    }

    private function mapToColumns(MantenimientoEquipo $dto): array
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
readonly class MantenimientoEquipo
{
    public function __construct(
        public ?int $id_mantenimiento = null,
        public ?string $codigo_equipo = null,
        public ?string $cedula_trabajador = null,
        public ?DateTimeImmutable $fecha = null,
        public ?TipoMantenimiento $tipo = null,
        public ?string $descripcion = null,
        public ?float $costo = null,
        public ?Equipo $equipo = null,
    ) {}

    public function validateInsert(): void
    {
        Validator::required($this->codigo_equipo, "codigo_equipo");
        Validator::required($this->tipo, "tipo");

        if ($this->costo !== null && $this->costo < 0) {
            throw new InvalidArgumentException('El costo no puede ser negativo');
        }
    }
}

enum TipoMantenimiento: string
{
    case Preventivo = 'Preventivo';
    case Correctivo = 'Correctivo';
}
