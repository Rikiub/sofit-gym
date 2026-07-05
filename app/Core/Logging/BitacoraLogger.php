<?php

namespace App\Core\Logging;

use App\Core\Auth\UserSessionManager;
use App\Core\Logging\LogLevel;
use App\Models\BitacoraDTO;
use App\Models\BitacoraModel;
use Psr\Log\AbstractLogger;
use Stringable;

/** Logger para registrar eventos del sistema.
 * 
 * Sigue la interfaz PSR-3 para facil extensibilidad con futuras herramientas. */
class BitacoraLogger extends AbstractLogger
{
    public function __construct(private BitacoraModel $bitacoraModel) {}

    private string $modulo = 'sistema';
    private string $accion = 'desconocido';

    public function setRouteContext(string $modulo, string $accion): void
    {
        $this->modulo = $modulo;
        $this->accion = $accion;
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

        $this->consoleLog($message, LogLevel::from($level));
        $this->bitacoraModel->insert(new BitacoraDTO(
            id_usuario: UserSessionManager::getCurrent()->id ?? null,
            modulo: $modulo,
            accion: $accion,
            mensaje: $message,
            nivel: strtolower((string)$level),
            contexto: $context ? json_encode($context) : null,
            datos_previos: $datosPrevios ? json_encode($datosPrevios) : null,
            datos_nuevos: $datosNuevos ? json_encode($datosNuevos) : null,
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
    private function consoleLog(string $message, LogLevel $level = LogLevel::INFO): void
    {
        if (PHP_SAPI !== 'cli') return;

        $timestamp = date('Y-m-d H:i:s');
        $level = strtoupper($level->value);

        fwrite(STDOUT, "[$timestamp] [$level] $message\n");
    }
}
