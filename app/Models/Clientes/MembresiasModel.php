<?php

namespace App\Models\Clientes;

use App\Helpers\Validator;
use App\Models\BaseModel;
use CuyZ\Valinor\Mapper\TreeMapper;
use DateTimeImmutable;
use PDO;

readonly class MembresiaDTO
{
    public function __construct(
        public ?int $id_membresia = null,
        public ?int $id_tipo = null,
        public ?int $id_estado = null,
        public ?string $tipo = null,
        public ?string $estado = null,
        public ?DateTimeImmutable $fecha_inicio = null,
        public ?DateTimeImmutable $fecha_fin = null,
    ) {}

    public function validateInsert() {}
    public function validateUpdate() {}
}

class MembresiasModel extends BaseModel
{
    private string $table = "membresia";
    private string $primaryKey = "id_membresia";

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
                    membresia.*,
                    mt.tipo AS `tipo`,
                    me.estado AS `estado`
                FROM {$this->table} membresia
                LEFT JOIN tipo_membresia mt ON m.id_tipo = mt.id_tipo
                LEFT JOIN estado_membresia me ON m.id_estado = me.id_estado
            SQL;
    }

    /**
     * @return MembresiaDTO[]
     */
    public function query(): array
    {
        $rows = $this->pdoQuery(
            <<<SQL
                {$this->sqlSelect()}
                ORDER BY fecha_fin DESC
            SQL
        )->fetchAll();

        return array_map(
            fn($row) => $this->mapper->map(MembresiaDTO::class, $row),
            $rows
        );
    }

    public function getByCliente(string $cedula_cliente): ?MembresiaDTO
    {
        $row = $this->pdoQuery(
            <<<SQL
                {$this->sqlSelect()}
                LEFT JOIN cliente
                    ON membresia.id_membresia = cliente.id_membresia
                WHERE membresia.id_membresia = ?
                ORDER BY fecha_fin DESC
            SQL,
            [$cedula_cliente],
        )->fetch();

        return $row
            ? $this->mapper->map(MembresiaDTO::class, $row)
            : null;
    }

    public function find(string $id_membresia): ?MembresiaDTO
    {
        $row = $this->pdoQuery(
            "{$this->sqlSelect()} WHERE {$this->primaryKey} = ?",
            [$id_membresia]
        )->fetch();

        return $row
            ? $this->mapper->map(MembresiaDTO::class, $row)
            : null;
    }

    public function queryMetadata(): array
    {
        $tipos = $this->pdoQuery('SELECT * FROM tipo_membresia')->fetchAll();
        $estados = $this->pdoQuery('SELECT * FROM estado_membresia')->fetchAll();

        return [
            "tipos" => $tipos,
            "estados" => $estados,
        ];
    }

    public function insert(MembresiaDTO $membresia): MembresiaDTO
    {
        $membresia->validateInsert();
        $this->pdo->beginTransaction();

        $this->pdoInsert(
            $this->table,
            $this->dtoToArray($membresia)
        );
        $id = (int) $this->pdo->lastInsertId();
        $membresia = $this->find($id);

        $this->pdo->commit();
        return $membresia;
    }

    public function update(MembresiaDTO $membresia): MembresiaDTO
    {
        $membresia->validateUpdate();
        $this->pdo->beginTransaction();

        $this->pdoUpdate(
            $this->table,
            $this->dtoToArray($membresia),
            [$this->primaryKey => $membresia->id_membresia],
        );
        $membresia = $this->find($membresia->id_membresia);

        $this->pdo->commit();
        return $membresia;
    }

    public function delete(string $id): void
    {
        $this->pdoDelete($this->table, [$this->primaryKey => $id]);
    }

    private function dtoToArray(MembresiaDTO $dto): array
    {
        $array = (array) $dto;

        $array["fecha_inicio"] = Validator::dateToString($array["fecha_inicio"]);
        $array["fecha_fin"] = Validator::dateToString($array["fecha_fin"]);

        return $array;
    }
}
