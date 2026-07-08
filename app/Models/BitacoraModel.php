<?php

namespace App\Models;

use App\Core\Tools;
use App\Core\Validator;
use DateTimeImmutable;

class BitacoraModel extends Model
{
    private string $table = self::DB_SECURITY . ".bitacora";
    private string $primaryKey = 'id_bitacora';

    /**
     * @return BitacoraLog[]
     */
    public function query(): array
    {
        $rows = $this->db->dbQuery($this->sqlSelect())->fetchAll();

        return array_map(
            fn($row) => $this->mapToLog($row),
            $rows
        );
    }

    public function find(int $id): ?BitacoraLog
    {
        $row = $this->db->dbQuery(
            $this->sqlSelect(where: "WHERE {$this->table}.{$this->primaryKey} = ?"),
            [$id]
        )->fetch();

        return $row
            ? $this->mapToLog($row)
            : null;
    }

    public function insert(BitacoraLog $bitacora): BitacoraLog
    {
        $bitacora->validateInsert();

        return $this->db->dbTransaction(function () use ($bitacora) {
            // Crear modulo dinamicamente si no existe
            $this->db->dbQuery(
                <<<SQL
                    INSERT INTO
                        {$this->dbSecurity("modulo")} (nombre)
                    VALUES
                        (?)
                    ON DUPLICATE KEY UPDATE
                        id_modulo = LAST_INSERT_ID(id_modulo)
                SQL,
                [$bitacora->modulo],
            );
            $idModulo = (int)$this->db->lastInsertId();

            // Insertar bitacora
            $this->db->dbInsert($this->table, [
                ...$this->mapToColumns($bitacora),
                "id_modulo" => $idModulo,
            ]);

            $id = (int)$this->db->lastInsertId();
            return $this->find($id);
        });
    }

    public function limpiarRegistros(int $dias_retencion): void
    {
        $this->db->dbQuery(
            <<<SQL
                CALL {$this->dbSecurity("sp_limpiar_registros")}(?)
            SQL,
            [$dias_retencion]
        );
    }

    // Helpers
    private function mapToLog(array $row): BitacoraLog
    {
        $row["contexto"] = json_decode($row["contexto"], true);
        $row["datos_previos"] = json_decode($row["datos_previos"], true);
        $row["datos_nuevos"] = json_decode($row["datos_nuevos"], true);

        return Tools::map(BitacoraLog::class, $row);
    }

    private function mapToColumns(BitacoraLog $dto): array
    {
        return [
            'id_usuario' => $dto->id_usuario,
            'id_modulo' => $dto->id_modulo,
            'accion' => $dto->accion,
            'mensaje' => $dto->mensaje,
            'nivel' => $dto->nivel,
            'contexto' => $dto->contexto ? json_encode($dto->contexto) : null,
            'datos_previos' => $dto->datos_previos ? json_encode($dto->datos_previos) : null,
            'datos_nuevos' => $dto->datos_nuevos ? json_encode($dto->datos_nuevos) : null,
        ];
    }

    private function sqlSelect(string $where = ""): string
    {
        return <<<SQL
                SELECT
                    bitacora.*,
                    usuario.nombre_usuario,
                    modulo.nombre AS `modulo`
                FROM
                    {$this->table} bitacora
                LEFT JOIN {$this->dbSecurity("modulo")}
                    ON bitacora.id_modulo = modulo.id_modulo
                LEFT JOIN {$this->dbSecurity("usuario")}
                    ON usuario.id_usuario = bitacora.id_usuario
                {$where}
                ORDER BY fecha DESC
            SQL;
    }
}

// DTO
readonly class BitacoraLog
{
    public function __construct(
        public ?int $id_bitacora = null,
        public ?int $id_usuario = null,
        public ?string $nombre_usuario = null,
        public ?int $id_modulo = null,
        public ?string $modulo = null,
        public ?string $accion = null,
        public ?string $mensaje = null,
        public ?string $nivel = null,
        public ?DateTimeImmutable $fecha = null,

        /** @var array<string, mixed>|null */
        public ?array $datos_previos = null,

        /** @var array<string, mixed>|null */
        public ?array $datos_nuevos = null,

        /** @var array<string, mixed>|null */
        public ?array $contexto = null,
    ) {}

    public function validateInsert()
    {
        Validator::required($this->accion, "accion");
        Validator::required($this->modulo, "modulo");
        Validator::required($this->nivel, "nivel");
    }
}
