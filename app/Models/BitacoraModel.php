<?php

namespace App\Models;

use App\Core\Tools;
use App\Services\Auth\UserSession;
use DateTimeImmutable;

class BitacoraModel extends Model
{
    private string $table = self::DB_SECURITY . ".bitacora";
    private string $primaryKey = 'id_bitacora';

    // Logger

    /** Registra un suceso del sistema. */
    public function log(
        string $mensaje,
        array $contexto = [],
        string|Level $nivel = Level::INFO,
    ): void {
        $nivel = $nivel instanceof Level
            ? $nivel
            : Level::from($nivel);
        $mensaje = $this->interpolate((string) $mensaje, $contexto);

        // Extraer datos
        $modulo = $contexto["modulo"] ?? null;
        $accion = $contexto["accion"] ?? null;

        $datosPrevios = $contexto["datos_previos"] ?? null;
        $datosNuevos = $contexto["datos_nuevos"] ?? null;

        // Limpiar contexto
        $contexto = array_diff_key($contexto, array_flip([
            "modulo",
            "accion",
            "datos_previos",
            "datos_nuevos",
        ]));

        $this->console($nivel, $mensaje);
        $this->insert(new BitacoraLog(
            id_usuario: UserSession::get()->id ?? null,
            modulo: $modulo,
            accion: $accion,
            mensaje: $mensaje,
            nivel: $nivel,
            contexto: $contexto,
            datos_previos: $datosPrevios,
            datos_nuevos: $datosNuevos,
        ));
    }

    /** Remplaza los {placeholder} de un texto con las variables de un array */
    private function interpolate(string $message, array $context): string
    {
        $replace = [];
        foreach ($context as $key => $val) {
            if (is_scalar($val) || (is_object($val) && method_exists($val, '__toString'))) {
                $replace['{' . $key . '}'] = $val;
            }
        }

        // Remplazar todos los placeholders
        return strtr($message, $replace);
    }

    /** Mostrar en consola solo en scripts */
    public function console(Level $level, string $message): void
    {
        if (PHP_SAPI !== 'cli') return;

        $timestamp = date('Y-m-d H:i:s');
        $level = strtoupper($level->value);

        fwrite(STDOUT, "[$timestamp] [$level] $message\n");
    }

    // Modelo

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
            'accion' => strtolower($dto->accion),
            'mensaje' => $dto->mensaje,
            'nivel' => strtolower((string)$dto->nivel->value),
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
        public ?Level $nivel = null,
        public ?DateTimeImmutable $fecha = null,

        public object|array|null $datos_previos = null,
        public object|array|null $datos_nuevos = null,
        public object|array|null $contexto = null,
    ) {}
}

/** Nivel de logging */
enum Level: string
{
    case DEBUG = 'debug';
    case INFO = 'info';
    case NOTICE = 'notice';
    case WARNING = 'warning';
    case ERROR = 'error';
    case CRITICAL = 'critical';
    case ALERT = 'alert';
    case EMERGENCY = 'emergency';
}
