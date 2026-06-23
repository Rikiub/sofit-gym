<?php

namespace App\Helpers;

use CuyZ\Valinor\Normalizer\Normalizer;
use Exception;
use JsonException;

/**
 * Helper para manejar peticiones globales
 */
class Response
{
    public const CONTENT_JSON = 'application/json';

    public function __construct(
        private ?Normalizer $normalizer
    ) {}

    /**
     * Convierte un array en una URL query como: ?page=inicio&action=index
     */
    public static function buildQueryParams(array $data): string
    {
        return http_build_query($data);
    }

    /**
     * Intentar obtener los datos desde el POST o JSON
     */
    public static function getParsedBody(): array
    {
        // Si el contenido es JSON, entonces decodificarlo.
        if (Response::isJson()) {
            $rawInput = file_get_contents('php://input');

            try {
                $data = json_decode($rawInput, associative: true, flags: JSON_THROW_ON_ERROR);
            } catch (JsonException $error) {
                throw new Exception("Invalid JSON: " . $error->getMessage());
            }

            return $data;
        }

        // Si el contenido es form POST, entonces devolver directamente
        return $_POST;
    }

    public static function empty(int $status = 204): null
    {
        http_response_code($status);
        return null;
    }

    /**
     * Redirigir a una pagina segun los query params
     */
    public static function redirect(array $queryParams, int $status = 302): void
    {
        http_response_code($status);
        header('Location: ?' . Response::buildQueryParams($queryParams));
    }

    public static function redirectToError(int $status = 404)
    {
        Response::redirect(
            [
                'page' => 'error',
                'status' => $status,
            ],
            404
        );
    }

    /**
     * Codifica los datos en una JSON string. Si no se proporciono un normalizador, fallback a jscon_encode.
     */
    public function json(mixed $data, int $status = 200): string
    {
        Response::setJsonHeaders($status);

        if ($this->normalizer) {
            return $this->normalizer->normalize($data);
        } else {
            return json_encode($data);
        }
    }

    public static function wantsJson()
    {
        if (
            Response::acceptsJson()
            || Response::isJson()
            || ($_GET['format'] ?? '') === 'json'
        ) {
            return true;
        } else {
            return false;
        }
    }

    public static function isJson(): bool
    {
        return $_SERVER['CONTENT_TYPE'] ?? '' == Response::CONTENT_JSON;
    }

    public static function acceptsJson(): bool
    {
        $accept = $_SERVER['HTTP_ACCEPT'] ?? '';
        return str_contains($accept, Response::CONTENT_JSON);
    }

    public static function setJsonHeaders(int $status)
    {
        header('Content-Type: ' . Response::CONTENT_JSON);
        http_response_code($status);
    }
}
