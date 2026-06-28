<?php

namespace App\Core;

use App\Core\Auth\UsuarioSession;
use App\Models\BitacoraDTO;
use App\Models\BitacoraModel;
use Psr\Log\AbstractLogger;
use Stringable;

/** Logger que registra eventos en la base de datos.
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

        $modulo = $context["modulo"] ?? $this->modulo;
        $accion = $context["accion"] ?? $this->accion;

        $datosPrevios = $context["datos_previos"] ?? null;
        $datosNuevos = $context["datos_nuevos"] ?? null;

        $this->bitacoraModel->insert(new BitacoraDTO(
            id_usuario: UsuarioSession::getCurrent()->id ?? null,
            modulo: $modulo,
            accion: $accion,
            mensaje: $message,
            nivel: strtoupper((string)$level),
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
}
