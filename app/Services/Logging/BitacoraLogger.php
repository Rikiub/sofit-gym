<?php

namespace App\Services\Logging;

use App\Services\Auth\UserSession;
use App\Models\BitacoraLog;
use App\Models\BitacoraModel;
use Psr\Log\AbstractLogger;
use Stringable;

/** Logger para registrar eventos del sistema.
 * 
 * Sigue la interfaz PSR-3. */
class BitacoraLogger extends AbstractLogger
{
    private string $modulo;
    private string $accion;

    public function __construct(
        private $bitacoraModel = new BitacoraModel()
    ) {
        // Determinar parametros segun la URL
        $this->modulo = $_GET["page"] ?? "?";
        $this->accion = $_GET["action"] ?? "?";
    }

    public function log(
        $level,
        string|Stringable $message,
        array $context = [],
    ): void {
        $message = $this->interpolate((string) $message, $context);

        // Extraer datos
        $modulo = $context["modulo"] ?? $this->modulo;
        $accion = $context["accion"] ?? $this->accion;

        $datosPrevios = $context["datos_previos"] ?? null;
        $datosNuevos = $context["datos_nuevos"] ?? null;

        // Limpiar contexto
        $context = array_diff_key($context, array_flip([
            "modulo",
            "accion",
            "datos_previos",
            "datos_nuevos",
        ]));

        $this->console(Level::from($level), $message);
        $this->bitacoraModel->insert(new BitacoraLog(
            id_usuario: UserSession::get()->id ?? null,
            modulo: $modulo,
            accion: $accion,
            mensaje: $message,
            nivel: strtolower((string)$level),
            contexto: $context,
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
}
