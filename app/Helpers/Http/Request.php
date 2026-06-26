<?php

namespace App\Helpers\Http;

use Exception;

const CONTENT_JSON = 'application/json';

/**
 * Helper para manejar peticiones globales
 */
class Request
{
    /**
     * Intentar obtener datos desde el POST o JSON input.
     */
    public static function getParsedBody(): array
    {
        // Si el contenido es JSON, entonces decodificarlo.
        if (Request::isJson()) {
            $rawInput = file_get_contents('php://input');
            $data = json_decode($rawInput, associative: true, flags: JSON_THROW_ON_ERROR);
            return $data;
        }

        // Si el contenido es form POST, entonces devolver directamente
        return $_POST;
    }

    /** Obtiene el parametro desde $_GET. Si no lo encuentra, lanzara una excepcion. */
    public static function requiredParam(string $param): string
    {
        return
            $_GET[$param]
            ?? throw new Exception("Parametro '{$param}' es requerido");
    }

    public static function wantsJson()
    {
        if (
            Request::acceptsJson()
            || Request::isJson()
            || ($_GET['format'] ?? '') === 'json'
        ) {
            return true;
        } else {
            return false;
        }
    }

    public static function isJson(): bool
    {
        return $_SERVER['CONTENT_TYPE'] ?? '' == CONTENT_JSON;
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
