<?php

namespace App\Models\Personas;

use App\Models\BaseModel;
use CuyZ\Valinor\Mapper\TreeMapper;
use PDO;

use function App\Core\toDbDate;

/**
 * Base para realizar operaciones sobre la tabla `persona`.
 */
class PersonasModel extends BaseModel
{
    public string $table = 'persona';
    public string $primaryKey = 'cedula';

    public function __construct(
        PDO $pdo,
        private TreeMapper $mapper,
    ) {
        return parent::__construct($pdo);
    }

    private function sqlSelect(): string
    {
        return <<<SQL
                SELECT persona.*
                FROM {$this->table} persona
            SQL;
    }

    /**
     * @return PersonaDTO[]
     */
    public function query(): array
    {
        $rows = $this->pdoQuery($this->sqlSelect())->fetchAll();
        return array_map(
            fn($row) => $this->mapper->map(PersonaDTO::class, $row),
            $rows
        );
    }

    public function find(string $cedula): ?PersonaDTO
    {
        $row = $this->pdoQuery(
            "{$this->sqlSelect()} WHERE {$this->primaryKey} = ?",
            [$cedula]
        )->fetch();

        if (!$row)
            return null;
        return $this->mapper->map(PersonaDTO::class, $row);
    }

    public function insert(PersonaDTO $persona): PersonaDTO
    {
        $persona->validateInsert();

        $this->pdoInsert(
            $this->table,
            $this->dtoToArray($persona),
        );

        return $this->find($persona->cedula);
    }

    public function update(PersonaDTO $persona): PersonaDTO
    {
        $array = $this->dtoToArray($persona);
        unset($array['cedula']);

        $this->pdoUpdate(
            $this->table,
            $array,
            [$this->primaryKey => $persona->cedula],
        );

        return $this->find($persona->cedula);
    }

    public function delete(string $cedula): void
    {
        $this->pdoDelete($this->table, [$this->primaryKey => $cedula]);
    }

    private function dtoToArray(PersonaDTO $persona): array
    {
        return [
            'cedula' => $persona->cedula,
            'nombre' => $persona->nombre,
            'apellido' => $persona->apellido,
            'correo' => $persona->correo,
            'telefono' => $persona->telefono,
            'direccion' => $persona->direccion,
            'fecha_nacimiento' => toDbDate($persona->fecha_nacimiento),
            'activo' => $persona->activo,
        ];
    }
}
