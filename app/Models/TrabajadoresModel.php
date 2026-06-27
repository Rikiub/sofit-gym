<?php

namespace App\Models;

use App\Core\Validator;
use App\Models\Personas\PersonaDTO;
use App\Models\Personas\PersonasModel;
use App\Models\Model;
use CuyZ\Valinor\Mapper\TreeMapper;
use DateTimeImmutable;
use PDO;

use function App\Core\toDbDate;

class TrabajadoresModel extends Model
{
    private string $table = 'trabajador';
    private string $primaryKey = 'cedula';

    public function __construct(
        PDO $pdo,
        private TreeMapper $mapper,
        private PersonasModel $personasModel,
    ) {
        return parent::__construct($pdo);
    }

    private function sqlSelect(): string
    {
        $pTable = $this->personasModel->table;
        $pKey = $this->personasModel->primaryKey;

        return <<<SQL
                SELECT
                    trabajador.*,
                    persona.*,
                    rol.nombre AS `rol`
                FROM {$this->table} trabajador
                LEFT JOIN {$pTable} persona
                    ON persona.{$pKey} = trabajador.{$this->primaryKey}
                LEFT JOIN rol_trabajador rol
                    ON trabajador.id_rol = rol.id_rol
            SQL;
    }

    /**
     * @return TrabajadorDTO[]
     */
    public function query(?string $search = null, ?int $id_rol = null): array
    {
        $sql = $this->sqlSelect();

        $whereClauses = [];
        $params = [];

        if ($search) {
            $search = trim($search);

            $columns = [
                'persona.nombre',
                'persona.apellido',
                "CONCAT(persona.nombre, ' ', persona.apellido)",
                "CONCAT(persona.apellido, ' ', persona.nombre)",
                'persona.correo',
                'persona.telefono',
                'persona.fecha_nacimiento',
                'persona.fecha_creacion',
                'rol.nombre',
            ];

            $clauses = array_map(fn($col) => "$col LIKE ?", $columns);
            $whereClauses[] = "(" . implode(" OR ", $clauses) . ")";

            foreach ($columns as $col) {
                $params[] = "%" . $search . "%";
            }
        }

        if ($id_rol) {
            $whereClauses[] = "rol.id_rol = ?";
            $params[] = $id_rol;
        }

        if (!empty($whereClauses)) {
            $sql .= " WHERE " . implode(" AND ", $whereClauses);
        }

        $rows = $this->pdoQuery($sql, $params)->fetchAll();

        return array_map(
            fn($row) => $this->mapper->map(TrabajadorDTO::class, $row),
            $rows
        );
    }

    public function find(string $cedula): ?TrabajadorDTO
    {
        $row = $this->pdoQuery(
            "{$this->sqlSelect()} WHERE trabajador.{$this->primaryKey} = ?",
            [$cedula]
        )->fetch();

        if (!$row)
            return null;
        return $this->mapper->map(TrabajadorDTO::class, $row);
    }

    /** Comprobar si la cedula ya esta asignada a una persona */
    public function checkDuplicate(string $cedula): bool
    {
        if ($this->personasModel->find($cedula)) {
            return true;
        }
        return false;
    }

    public function insert(TrabajadorDTO $trabajador): TrabajadorDTO
    {
        $trabajador->validateInsert();

        return $this->pdoTransaction(function () use ($trabajador) {
            $this->personasModel->insert($trabajador);
            $this->pdoInsert(
                $this->table,
                $this->dtoToArray($trabajador),
            );
            return $this->find($trabajador->cedula);
        });
    }

    public function update(TrabajadorDTO $trabajador): TrabajadorDTO
    {
        return $this->pdoTransaction(function () use ($trabajador) {
            $this->personasModel->update($trabajador);

            $array = $this->dtoToArray($trabajador);
            unset($array['cedula']);

            $this->pdoUpdate(
                $this->table,
                $array,
                [$this->primaryKey => $trabajador->cedula],
            );

            return $this->find($trabajador->cedula);
        });
    }

    public function delete(string $cedula): void
    {
        $this->pdoDelete($this->table, [$this->primaryKey => $cedula]);
    }

    private function dtoToArray(TrabajadorDTO $dto): array
    {
        return [
            'cedula' => $dto->cedula,
            'id_rol' => $dto->id_rol,
            'salario' => $dto->salario,
            'fecha_contratacion' => toDbDate($dto->fecha_contratacion),
        ];
    }
}

// DTO
readonly class TrabajadorDTO extends PersonaDTO
{
    public function __construct(
        ?string $cedula = null,
        ?string $nombre = null,
        ?string $apellido = null,
        ?string $correo = null,
        ?string $telefono = null,
        ?string $direccion = null,
        ?bool $activo = true,
        ?DateTimeImmutable $fecha_nacimiento = null,
        ?DateTimeImmutable $fecha_creacion = null,
        public ?int $id_rol = null,
        public ?string $rol = null,
        public ?float $salario = null,
        public ?DateTimeImmutable $fecha_contratacion = null,
    ) {
        parent::__construct(
            cedula: $cedula,
            nombre: $nombre,
            apellido: $apellido,
            correo: $correo,
            telefono: $telefono,
            direccion: $direccion,
            activo: $activo,
            fecha_nacimiento: $fecha_nacimiento,
            fecha_creacion: $fecha_creacion,
        );
    }

    public function validateInsert()
    {
        parent::validateInsert();
        Validator::required($this->id_rol, "id_rol");
    }
}
