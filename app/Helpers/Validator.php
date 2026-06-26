<?php

namespace App\Helpers;

use InvalidArgumentException;

/**
 * Grupo de validaciones reutilizables
 */
class Validator
{
    public static function required(mixed $value, string $fieldName = 'field'): mixed
    {
        if (is_array($value) && count($value) === 0) {
            throw new InvalidArgumentException("El campo '{$fieldName}' es un array vacío y no tiene contenido");
        }

        if (is_string($value) && $value === '') {
            throw new InvalidArgumentException("El campo '{$fieldName} no puede estar vacio");
        }

        if (!$value) {
            throw new InvalidArgumentException("El campo '{$fieldName}' es requerido");
        }

        return $value;
    }

    public static function cedula(mixed $value, string $fieldName = 'cedula'): string
    {
        // Limpiar espacios y convertir la letra a mayúscula automáticamente
        $cleanValue = strtoupper(trim((string)$value));

        // Patrón que acepta V o E, un guion, y entre 7 y 8 números
        $pattern = '/^[VE]-\d{7,8}$/';

        if (preg_match($pattern, $cleanValue) !== 1) {
            throw new InvalidArgumentException(
                "El campo '{$fieldName}' no tiene un formato de cédula válido"
            );
        }

        return $cleanValue;
    }

    public static function email(mixed $value, string $fieldName = 'email'): string
    {
        $valid = (bool) filter_var($value, FILTER_VALIDATE_EMAIL);

        if (!$valid) {
            throw new InvalidArgumentException("El campo '{$fieldName}' tiene un formato invalido de email");
        }

        return $value;
    }

    public static function telefono(mixed $value, string $fieldName = 'telefono'): string
    {
        $cleanValue = trim((string)$value);
        $pattern = '/^04(12|14|16|24|26)-\d{7}$/';

        if (preg_match($pattern, $cleanValue) !== 1) {
            throw new InvalidArgumentException(
                "El campo '{$fieldName}' no tiene un formato de teléfono válido"
            );
        }

        return $cleanValue;
    }
}
