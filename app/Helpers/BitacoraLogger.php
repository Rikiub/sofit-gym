<?php

namespace App\Helpers;

use App\Helpers\Auth\UsuarioSession;
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

        $context["modulo"] = $context["modulo"] ?? $this->modulo;
        $context["accion"] = $context["accion"] ?? $this->accion;

        $this->bitacoraModel->insert(new BitacoraDTO(
            id_usuario: UsuarioSession::getUsuario()->id ?? null,
            modulo: $context["modulo"],
            accion: $context["accion"],
            mensaje: $message,
            nivel: strtoupper((string)$level),
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
