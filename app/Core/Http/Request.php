<?php

namespace App\Core\Http;

const CONTENT_JSON = 'application/json';

/**
 * Helpers para manejar peticiones.
 */
class Request
{
    /** Obtiene un parámetro de la URL como string. */
    public static function query(string $param): ?string
    {
        $value = $_GET[$param] ?? null;

        if (is_string($value)) {
            $value = trim($value);
        }

        return $value;
    }

    /** Obtiene un parámetro de la URL y lo convierte en un entero. */
    public static function queryInt(string $param): ?int
    {
        $value = $_GET[$param] ?? null;

        if ($value === null || is_array($value)) {
            return null;
        }

        // Valida que sea un entero numérico real
        $filtered = filter_var($value, FILTER_VALIDATE_INT);

        return $filtered
            ? $filtered
            : null;
    }

    /** Obtiene un parámetro de la URL y lo convierte en un booleano. */
    public static function queryBool(string $param): bool
    {
        $value = $_GET[$param] ?? null;

        if ($value === null || is_array($value)) {
            return false;
        }

        $filtered = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
        return $filtered ?? false;
    }

    /** Intenta obtener datos desde el POST o JSON input. */
    public static function getParsedBody(): array
    {
        // JSON
        if (self::isJson()) {
            $raw = file_get_contents('php://input');
            if (empty($raw)) {
                return [];
            }

            $data = json_decode($raw, associative: true, flags: JSON_THROW_ON_ERROR);

            if (!is_array($data)) {
                return [];
            }
            return $data;
        }

        // POST
        return $_POST;
    }

    public static function wantsJson()
    {
        if (
            self::acceptsJson()
            || self::isJson()
            || ($_GET['format'] ?? '') === 'json'
        ) {
            return true;
        } else {
            return false;
        }
    }

    public static function isJson(): bool
    {
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        return str_starts_with($contentType, CONTENT_JSON);
    }

    public static function acceptsJson(): bool
    {
        $accept = $_SERVER['HTTP_ACCEPT'] ?? '';
        return str_contains($accept, CONTENT_JSON);
    }

    /**
     * Convierte un array en una URL query como: `?page=inicio&action=index`
     */
    public static function buildQuery(array $data): string
    {
        return http_build_query($data);
    }
}
