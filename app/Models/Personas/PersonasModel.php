<?php

namespace App\Models\Personas;

use App\Helpers\Validator;
use App\Models\BaseModel;
use CuyZ\Valinor\Mapper\TreeMapper;
use PDO;

/**
 * Base para realizar operaciones sobre la tabla `persona`.
 */
class PersonasModel extends BaseModel
{
    public string $table = 'persona';
    public string $primaryKey = 'cedula_persona';

    public function __construct(
        PDO $pdo,
        private TreeMapper $mapper,
    ) {
        return parent::__construct($pdo);
    }

    private function sqlSelect(): string
    {
        return <<<SQL
                SELECT
                    cedula_persona AS `cedula`,
                    nombre,
                    apellido,
                    correo,
                    telefono,
                    direccion,
                    fecha_nacimiento,
                    fecha_registro,
                    activo
                FROM {$this->table}
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
        $persona->validateUpdate();

        $array = $this->dtoToArray($persona);
        unset($array['cedula_persona']);

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
            'cedula_persona' => $persona->cedula,
            'nombre' => $persona->nombre,
            'apellido' => $persona->apellido,
            'correo' => $persona->correo,
            'telefono' => $persona->telefono,
            'direccion' => $persona->direccion,
            'fecha_nacimiento' => Validator::dateToString($persona->fecha_nacimiento),
            'fecha_registro' => Validator::dateToString($persona->fecha_registro),
            'activo' => $persona->activo,
        ];
    }
}
