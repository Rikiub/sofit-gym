<?php

namespace App\Core;

use DateTimeImmutable;
use Exception;
use InvalidArgumentException;

/**
 * Grupo de validaciones reutilizables
 */
class Validator
{
    public static function required(mixed $value, string $key): mixed
    {
        if (empty($value)) {
            throw new InvalidArgumentException("El campo '{$key}' es requerido y no puede estar vacío");
        }
        return $value;
    }

    public static function length(string $value, string $key, int $min, int $max): string
    {
        Validator::minLength($value, $key, $min);
        Validator::maxLength($value, $key, $max);
        return $value;
    }

    public static function minLength(string $value, string $key, int $min): string
    {
        if (strlen($value) < $min) {
            throw new InvalidArgumentException("El campo '{$key}' no cumple el minimo de {$min} caracteres");
        }
        return $value;
    }

    public static function maxLength(string $value, string $key, int $max): string
    {
        if (strlen($value) > $max) {
            throw new InvalidArgumentException("El campo '{$key}' excede el limite de {$max} caracteres");
        }
        return $value;
    }

    public static function cedula(string $value, string $key): string
    {
        // Limpiar espacios y convertir la letra a mayúscula automáticamente
        $cleanValue = strtoupper(trim((string)$value));

        // Patrón que acepta V o E, un guion, y entre 7 y 8 números
        $pattern = '/^[VE]-\d{7,8}$/';

        if (preg_match($pattern, $cleanValue) !== 1) {
            throw new InvalidArgumentException(
                "El campo '{$key}' no tiene un formato de cédula válido"
            );
        }

        return $cleanValue;
    }

    public static function email(string $value, string $key): string
    {
        $valid = (bool) filter_var($value, FILTER_VALIDATE_EMAIL);

        if (!$valid) {
            throw new InvalidArgumentException("El campo '{$key}' tiene un formato invalido de email");
        }

        return $value;
    }

    public static function telefono(string $value, string $key): string
    {
        $cleanValue = trim((string)$value);
        $pattern = '/^04(12|14|16|24|26)-\d{7}$/';

        if (preg_match($pattern, $cleanValue) !== 1) {
            throw new InvalidArgumentException(
                "El campo '{$key}' no tiene un formato de teléfono válido"
            );
        }

        return $cleanValue;
    }

    public static function date(string $value, string $key): string
    {
        $value = Validator::required($value, $key);
        $outputFormat = "YYYY-MM-DD HH:mm:ss";

        try {
            $date = new DateTimeImmutable($value);
            return $date->format($outputFormat);
        } catch (Exception) {
            throw new InvalidArgumentException("El campo '{$key}' no es una fecha válida");
        }
    }
}
