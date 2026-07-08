<?php

namespace App\Models\Personas;

use App\Core\Tools;
use App\Models\Model;

use function App\Core\toDbDate;

/**
 * Base para realizar operaciones sobre la tabla `persona`.
 */
class PersonaModel extends Model
{
    public string $table = 'persona';
    public string $primaryKey = 'cedula';

    /**
     * @return Persona[]
     */
    public function query(): array
    {
        $rows = $this->db->dbQuery($this->sqlSelect())->fetchAll();
        return array_map(
            fn($row) => Tools::map(Persona::class, $row),
            $rows
        );
    }

    public function find(string $cedula): ?Persona
    {
        $row = $this->db->dbQuery(
            "{$this->sqlSelect()} WHERE {$this->primaryKey} = ?",
            [$cedula]
        )->fetch();

        return $row
            ? Tools::map(Persona::class, $row)
            : null;
    }

    public function insert(Persona $persona): Persona
    {
        $persona->validateInsert();

        $this->db->dbInsert(
            $this->table,
            $this->mapToColumns($persona, includeId: true),
        );
        return $this->find($persona->cedula);
    }

    public function update(string $cedula, Persona $persona): Persona
    {
        $this->db->dbUpdate(
            $this->table,
            $this->mapToColumns($persona),
            [$this->primaryKey => $cedula],
        );
        return $this->find($cedula);
    }

    public function delete(string $cedula): void
    {
        $this->db->dbDelete($this->table, [$this->primaryKey => $cedula]);
    }

    private function mapToColumns(Persona $dto, bool $includeId = false): array
    {
        $data = [
            'nombre' => $dto->nombre,
            'apellido' => $dto->apellido,
            'correo' => $dto->correo,
            'telefono' => $dto->telefono,
            'direccion' => $dto->direccion,
            'fecha_nacimiento' => toDbDate($dto->fecha_nacimiento),
            'activo' => $dto->activo,
        ];

        if ($includeId) {
            $data[$this->primaryKey] = $dto->cedula;
        }

        return $data;
    }

    private function sqlSelect(): string
    {
        return <<<SQL
                SELECT *
                FROM {$this->table}
            SQL;
    }
}
