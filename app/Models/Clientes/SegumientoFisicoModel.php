<?php

namespace App\Models\Clientes;

use App\Core\Tools;
use App\Models\BitacoraModel;
use App\Models\Database;
use DateTimeImmutable;
use InvalidArgumentException;

use function App\Core\toDbDate;

class SegumientoFisicoModel extends Database
{
    private string $table = 'seguimiento_fisico';
    private string $primaryKey = 'id_seguimiento';

    public function __construct(
        private $logger = new BitacoraModel()
    ) {
        parent::__construct();
    }

    /**
     * Obtiene todos los seguimientos de un cliente.
     * @return SeguimientoFisico[]
     */
    public function queryByCliente(string $cedula): array
    {
        $rows = $this->dbQuery(
            $this->sqlSelect(where: <<<SQL
                WHERE cedula_cliente = ?
                ORDER BY fecha DESC
            SQL),
            [$cedula],
        )->fetchAll();

        $this->logger->log("Listar seguimientos físicos", [
            "modulo" => "cliente_info",
            "accion" => "listar_seg_fisico",
        ]);

        return array_map(
            fn($row) => Tools::map(SeguimientoFisico::class, $row),
            $rows
        );
    }

    /**
     * Busca un seguimiento por su ID.
     */
    public function find(int $id): ?SeguimientoFisico
    {
        $row = $this->dbQuery(
            $this->sqlSelect(where: "WHERE {$this->primaryKey} = ?"),
            [$id],
        )->fetch();

        return $row
            ? Tools::map(SeguimientoFisico::class, $row)
            : null;
    }

    /**
     * Inserta un nuevo seguimiento.
     */
    public function insert(string $cedula_cliente, SeguimientoFisico $seguimiento): SeguimientoFisico
    {
        $seguimiento->validateInsert();

        $this->dbInsert(
            $this->table,
            [
                ...$this->mapToColumns($seguimiento),
                "cedula_cliente" => $cedula_cliente,
            ],
        );

        $id = (int) $this->pdo->lastInsertId();
        $seg = $this->find($id);

        $this->logger->log("Seguimiento físico para cliente '{cedula}' registrado", [
            "modulo" => "cliente_info",
            "accion" => "crear_seg_fisico",

            'cedula'        => $cedula_cliente,
            'id_seguimiento' => $seg->id_seguimiento,
            'datos_nuevos'  => $seg,
        ]);

        return $seg;
    }

    /**
     * Actualiza un seguimiento existente.
     */
    public function update(int $id, SeguimientoFisico $seguimiento): SeguimientoFisico
    {
        $this->dbUpdate(
            $this->table,
            $this->mapToColumns($seguimiento),
            [$this->primaryKey => $id]
        );

        $id = (int) $this->pdo->lastInsertId();
        return $this->find($id);
    }

    /**
     * Elimina un seguimiento por ID.
     */
    public function delete(int $id): void
    {
        $this->dbDelete($this->table, [$this->primaryKey => $id]);
        $this->logger->log("Seguimiento físico '{id_seguimiento}' eliminado", [
            "modulo" => "cliente_info",
            "accion" => "eliminar_seg_fisico",

            'id_seguimiento' => $id,
            'cedula' => $id,
        ]);
    }

    private function sqlSelect(string $where = ""): string
    {
        return <<<SQL
                SELECT *
                FROM {$this->table}
                {$where}
            SQL;
    }

    private function mapToColumns(SeguimientoFisico $dto): array
    {
        $array = (array) $dto;
        $array["fecha"] = toDbDate($dto->fecha);
        unset($array["cedula_cliente"]);
        return $array;
    }
}

// DTO
readonly class SeguimientoFisico
{
    public function __construct(
        public ?int $id_seguimiento = null,
        public ?string $cedula_cliente = null,
        public ?string $registrado_por = null,
        public ?DateTimeImmutable $fecha = null,
        public ?float $altura_cm = null,
        public ?float $peso_kg = null,
        public ?float $cintura_cm = null,
        public ?float $cadera_cm = null,
        public ?float $pecho_cm = null,
        public ?float $muslo_cm = null,
        public ?float $hombros_cm = null,
        public ?float $pantorrilla_cm = null,
    ) {}

    public function validateInsert(): void
    {
        // Al menos una medida numérica debe existir
        $medidas = [
            $this->altura_cm,
            $this->peso_kg,
            $this->cintura_cm,
            $this->cadera_cm,
            $this->pecho_cm,
            $this->muslo_cm,
            $this->hombros_cm,
            $this->pantorrilla_cm,
        ];

        $todasVacias = true;
        foreach ($medidas as $medida) {
            if ($medida !== null) {
                $todasVacias = false;
                break;
            }
        }

        if ($todasVacias) {
            throw new InvalidArgumentException('Debe proporcionar al menos una medida');
        }
    }
}
