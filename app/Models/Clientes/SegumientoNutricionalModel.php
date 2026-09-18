<?php

namespace App\Models\Clientes;

use App\Core\Tools;
use App\Models\BitacoraModel;
use App\Models\Database;
use DateTimeImmutable;
use InvalidArgumentException;

use function App\Core\toDbDate;

class SegumientoNutricionalModel extends Database
{
    private string $table = 'seguimiento_nutricional';
    private string $primaryKey = 'id_seguimiento';

    public function __construct(
        private $logger = new BitacoraModel()
    ) {
        parent::__construct();
    }

    /**
     * Obtiene todos los seguimientos de un cliente.
     * @return SeguimientoNutricional[]
     */
    public function queryByCliente(string $cedula): array
    {
        $rows = $this->dbQuery(
            $this->sqlSelect(where: <<<SQL
                WHERE cedula_cliente = ?
                ORDER BY fecha DESC
            SQL),
            [$cedula]
        )->fetchAll();

        $this->logger->log("Listar seguimientos nutricionales", [
            "modulo" => "cliente_info",
            "accion" => "listar_seg_nutricion",
        ]);

        return array_map(
            fn($row) => Tools::map(SeguimientoNutricional::class, $row),
            $rows
        );
    }

    /**
     * Busca un seguimiento por su ID.
     */
    public function find(int $id): ?SeguimientoNutricional
    {
        $row = $this->dbQuery(
            $this->sqlSelect(where: "WHERE {$this->primaryKey} = ?"),
            [$id]
        )->fetch();

        return $row
            ? Tools::map(SeguimientoNutricional::class, $row)
            : null;
    }

    /**
     * Inserta un nuevo seguimiento.
     */
    public function insert(string $cedula_cliente, SeguimientoNutricional $seguimiento): SeguimientoNutricional
    {
        $seguimiento->validateInsert();

        $this->dbInsert(
            $this->table,
            [
                ...$this->mapToColumns($seguimiento),
                "cedula_cliente" => $cedula_cliente
            ]
        );

        $id = (int) $this->pdo->lastInsertId();
        $seg = $this->find($id);

        $this->logger->log("Seguimiento nutricional para cliente '{cedula}' registrado", [
            "modulo" => "cliente_info",
            "accion" => "crear_seg_nutricion",

            'cedula' => $cedula_cliente,
            'id_seguimiento' => $seg->id_seguimiento,
            'datos_nuevos' => $seg,
        ]);

        return $seg;
    }

    /**
     * Actualiza un seguimiento existente.
     */
    public function update(int $id, SeguimientoNutricional $seguimiento): SeguimientoNutricional
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
        $this->logger->log("Seguimiento nutricional '{id_seguimiento}' eliminado", [
            "modulo" => "cliente_info",
            "accion" => "eliminar_seg_nutricion",
            'id_seguimiento' => $id,
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

    private function mapToColumns(SeguimientoNutricional $dto): array
    {
        $array = (array) $dto;
        $array["fecha"] = toDbDate($dto->fecha);
        unset($array["cedula_cliente"]);
        return $array;
    }
}

// DTO
readonly class SeguimientoNutricional
{
    public function __construct(
        public ?int $id_seguimiento = null,
        public ?string $cedula_cliente = null,
        public ?string $registrado_por = null,
        public ?DateTimeImmutable $fecha = null,
        public ?float $proteinas_g = null,
        public ?float $carbohidratos_g = null,
        public ?float $grasas_g = null,
    ) {}

    public function validateInsert(): void
    {
        // Al menos un valor debe existir
        $medidas = [
            $this->proteinas_g,
            $this->carbohidratos_g,
            $this->grasas_g,
        ];

        $todasVacias = true;
        foreach ($medidas as $medida) {
            if ($medida !== null) {
                $todasVacias = false;
                break;
            }
        }

        if ($todasVacias) {
            throw new InvalidArgumentException('Debe proporcionar al menos un valor');
        }
    }
}
