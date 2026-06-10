<?php

namespace App\Models;

use App\Models\BaseModel;
use CuyZ\Valinor\Mapper\TreeMapper;
use DateTimeImmutable;
use PDO;

readonly class BitacoraDTO
{
    public function __construct(
        public ?int $id_bitacora = null,
        public ?int $id_usuario = null,
        public ?string $modulo = null,
        public ?string $accion = null,
        public ?string $mensaje = null,
        public ?string $nivel = null,
        public ?string $id_registro = null,
        public ?string $datos_previos = null,
        public ?string $datos_nuevos = null,
        public ?DateTimeImmutable $fecha = null,
    ) {}

    public function validateInsert() {}
    public function validateUpdate() {}
}

class BitacoraModel extends BaseModel
{
    private string $table = 'sofit_gym_seguridad.bitacora';
    private string $primaryKey = 'id_bitacora';

    public function __construct(
        PDO $pdo,
        private TreeMapper $mapper,
    ) {
        return parent::__construct($pdo);
    }

    private function dtoToArray(BitacoraDTO $dto): array
    {
        return [
            'id_usuario' => $dto->id_usuario,
            'modulo' => $dto->modulo,
            'accion' => $dto->accion,
            'mensaje' => $dto->mensaje,
            'nivel' => $dto->nivel,
        ];
    }

    private function sqlSelect(string $where = ""): string
    {
        return <<<SQL
                SELECT *
                FROM {$this->table}
                {$where}
                ORDER BY fecha DESC
            SQL;
    }

    /**
     * @return BitacoraDTO[]
     */
    public function query(): array
    {
        // Execute
        $rows = $this->pdoQuery($this->sqlSelect())->fetchAll();

        return array_map(
            fn($row) => $this->mapper->map(BitacoraDTO::class, $row),
            $rows
        );
    }

    public function find(int $id): ?BitacoraDTO
    {
        $row = $this->pdoQuery(
            $this->sqlSelect("WHERE {$this->primaryKey} = ?"),
            [$id]
        )->fetch();

        if (!$row)
            return null;
        return $this->mapper->map(BitacoraDTO::class, $row);
    }

    public function insert(BitacoraDTO $bitacora): BitacoraDTO
    {
        $bitacora->validateInsert();
        $this->pdo->beginTransaction();

        $data = $this->dtoToArray($bitacora);

        $this->pdoInsert(
            $this->table,
            $this->dtoToArray($bitacora),
        );

        $id = (int)$this->pdo->lastInsertId();
        $bitacora = $this->find($id);
        $this->pdo->commit();

        return $bitacora;
    }

    public function update(BitacoraDTO $bitacora): BitacoraDTO
    {
        $bitacora->validateUpdate();
        $this->pdo->beginTransaction();

        $this->pdoUpdate(
            $this->table,
            $this->dtoToArray($bitacora),
            [$this->primaryKey => $bitacora->id_bitacora],
        );

        $bitacora = $this->find($bitacora->id_bitacora);
        $this->pdo->commit();

        return $bitacora;
    }

    public function delete(int $id): void
    {
        $this->pdoDelete($this->table, [$this->primaryKey => $id]);
    }
}
