<?php

namespace App\Models;

use App\Core\Database;
use App\Core\Validator;
use App\Models\Personas\PersonaDTO;
use App\Models\Personas\PersonasModel;
use App\Models\Model;
use CuyZ\Valinor\Mapper\TreeMapper;
use DateTimeImmutable;

use function App\Core\toDbDate;

class TrabajadoresModel extends Model
{
    private string $table = 'trabajador';
    private string $primaryKey = 'cedula';

    public function __construct(
        Database $db,
        private TreeMapper $mapper,
        private PersonasModel $personasModel,
    ) {
        return parent::__construct($db);
    }

    /** Resumen estadisticos */
    public function getSummary(): array
    {
        $rows = $this->db->dbQuery(<<<SQL
            SELECT 
                COUNT(*) AS total_trabajadores,
                COALESCE(SUM(salario), 0) AS salario_total_pagado
            FROM trabajador;
        SQL)->fetch();
        return $rows;
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

        $rows = $this->db->dbQuery($sql, $params)->fetchAll();

        return array_map(
            fn($row) => $this->mapper->map(TrabajadorDTO::class, $row),
            $rows
        );
    }

    public function find(string $cedula): ?TrabajadorDTO
    {
        $row = $this->db->dbQuery(
            $this->sqlSelect(where: "WHERE trabajador.{$this->primaryKey} = ?"),
            [$cedula]
        )->fetch();

        return $row
            ? $this->mapper->map(TrabajadorDTO::class, $row)
            : null;
    }

    /** Comprobar si la cedula ya esta asignada a una persona */
    public function checkDuplicate(string $cedula): bool
    {
        return (bool)$this->personasModel->find($cedula);
    }

    public function insert(TrabajadorDTO $trabajador): TrabajadorDTO
    {
        $trabajador->validateInsert();

        return $this->db->dbTransaction(function () use ($trabajador) {
            $this->personasModel->insert($trabajador);
            $this->db->dbInsert(
                $this->table,
                $this->mapToColumns($trabajador, includeId: true),
            );
            return $this->find($trabajador->cedula);
        });
    }

    public function update(string $cedula, TrabajadorDTO $trabajador): TrabajadorDTO
    {
        return $this->db->dbTransaction(function () use ($cedula, $trabajador) {
            $this->personasModel->update($cedula, $trabajador);

            $this->db->dbUpdate(
                $this->table,
                $this->mapToColumns($trabajador),
                [$this->primaryKey => $cedula],
            );

            return $this->find($cedula);
        });
    }

    public function delete(string $cedula): void
    {
        $this->db->dbDelete($this->table, [$this->primaryKey => $cedula]);
    }

    private function mapToColumns(TrabajadorDTO $dto, bool $includeId = false): array
    {
        $data = [
            'id_rol' => $dto->id_rol,
            'salario' => $dto->salario,
            'fecha_contratacion' => toDbDate($dto->fecha_contratacion),
        ];

        if ($includeId) {
            $data[$this->primaryKey] = $dto->cedula;
        }

        return $data;
    }

    private function sqlSelect(string $where = ""): string
    {
        $pTable = $this->personasModel->table;
        $pKey = $this->personasModel->primaryKey;

        return <<<SQL
                SELECT
                    trabajador.*,
                    persona.*,
                    CONCAT(persona.nombre, ' ', persona.apellido) AS nombre_completo,
                    rol.nombre AS `rol`
                FROM {$this->table} trabajador
                LEFT JOIN {$pTable} persona
                    ON persona.{$pKey} = trabajador.{$this->primaryKey}
                LEFT JOIN rol_trabajador rol
                    ON trabajador.id_rol = rol.id_rol
                {$where}
            SQL;
    }
}

// DTO
readonly class TrabajadorDTO extends PersonaDTO
{
    public function __construct(
        ?string $cedula = null,
        ?string $nombre = null,
        ?string $apellido = null,
        ?string $nombre_completo = null,
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
            nombre_completo: $nombre_completo,
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
