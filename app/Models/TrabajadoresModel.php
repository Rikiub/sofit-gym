<?php

namespace App\Models;

use App\Helpers\Validator;
use App\Models\Personas\PersonaDTO;
use App\Models\Personas\PersonasModel;
use App\Models\BaseModel;
use CuyZ\Valinor\Mapper\TreeMapper;
use DateTimeImmutable;
use PDO;

readonly class TrabajadorDTO extends PersonaDTO
{
    public function __construct(
        // Atributos heredados
        ?string $cedula = null,
        ?string $nombre = null,
        ?string $apellido = null,
        ?string $correo = null,
        ?string $telefono = null,
        ?string $direccion = null,
        ?bool $activo = true,
        ?DateTimeImmutable $fecha_nacimiento = null,
        ?DateTimeImmutable $fecha_registro = new DateTimeImmutable(),
        public ?int $id_rol = null,
        public ?string $rol = null,
        public ?float $salario = null,
        // Nuevos atributos
        public ?DateTimeImmutable $fecha_contratacion = new DateTimeImmutable(),
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
            fecha_registro: $fecha_registro,
        );
    }
}

class TrabajadoresModel extends BaseModel
{
    private string $table = 'trabajador';
    private string $primaryKey = 'cedula_trabajador';

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
                    trabajador.{$this->primaryKey} AS `cedula`,
                    trabajador.*,
                    persona.*,
                    rol.nombre AS `rol`
                FROM {$this->table} trabajador
                LEFT JOIN {$pTable} persona
                    ON persona.{$pKey} = trabajador.{$this->primaryKey}
                LEFT JOIN sofit_gym_seguridad.rol rol
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

        // Bloque de Búsqueda de Texto
        if ($search) {
            $search = trim($search);

            $columns = [
                'persona.nombre',
                'persona.apellido',
                "CONCAT(persona.nombre, ' ', persona.apellido)", // Permite buscar "Juan Perez"
                "CONCAT(persona.apellido, ' ', persona.nombre)", // Permite buscar "Perez Juan"
                'persona.correo',
                'persona.telefono',
                'persona.fecha_nacimiento',
                'persona.fecha_registro',
                'rol.nombre',
            ];

            // Creamos las "columna LIKE ?"
            // Y agrupamos TODOS los ORs dentro de un paréntesis para proteger la lógica
            $clauses = array_map(fn($col) => "$col LIKE ?", $columns);
            $whereClauses[] = "(" . implode(" OR ", $clauses) . ")";

            // Rellenamos los parámetros posicionales uno por uno
            foreach ($columns as $col) {
                $params[] = "%" . $search . "%";
            }
        }

        // Bloque de Filtro por Rol
        if ($id_rol) {
            $whereClauses[] = "rol.id_rol = ?";
            $params[] = $id_rol;
        }

        // Armar SQL
        if (!empty($whereClauses)) {
            $sql .= " WHERE " . implode(" AND ", $whereClauses);
        }

        // Execute
        $rows = $this->pdoQuery($sql, $params)->fetchAll();

        return array_map(
            fn($row) => $this->mapper->map(TrabajadorDTO::class, $row),
            $rows
        );
    }

    public function find(string $cedula): ?TrabajadorDTO
    {
        $row = $this->pdoQuery(
            "{$this->sqlSelect()} WHERE {$this->primaryKey} = ?",
            [$cedula]
        )->fetch();

        if (!$row)
            return null;
        return $this->mapper->map(TrabajadorDTO::class, $row);
    }

    public function insert(TrabajadorDTO $trabajador): TrabajadorDTO
    {
        $trabajador->validateInsert();
        $this->pdo->beginTransaction();

        $this->personasModel->insert($trabajador);
        $this->pdoInsert(
            $this->table,
            $this->dtoToArray($trabajador),
        );

        $this->pdo->commit();
        return $this->find($trabajador->cedula);
    }

    public function update(TrabajadorDTO $trabajador): TrabajadorDTO
    {
        $trabajador->validateUpdate();
        $this->pdo->beginTransaction();

        $this->personasModel->update($trabajador);

        $array = $this->dtoToArray($trabajador);
        unset($array['cedula_trabajador']);

        $this->pdoUpdate(
            $this->table,
            $array,
            [$this->primaryKey => $trabajador->cedula],
        );

        $this->pdo->commit();
        return $this->find($trabajador->cedula);
    }

    public function delete(string $cedula): void
    {
        $this->pdoDelete($this->table, [$this->primaryKey => $cedula]);
    }

    private function dtoToArray(TrabajadorDTO $dto): array
    {
        return [
            'cedula_trabajador' => $dto->cedula,
            'id_rol' => $dto->id_rol,
            'salario' => $dto->salario,
            'fecha_contratacion' => Validator::dateToString($dto->fecha_contratacion),
        ];
    }
}
