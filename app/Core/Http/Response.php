<?php

namespace App\Core\Http;

/** Helpers para devolver respuestas HTTP, principalmente como JSON. */
class Response
{
    public static function withJsonHeaders()
    {
        header('Content-Type: application/json');
    }

    /**
     * Serializa los datos en una JSON string y envia los headers `application/json`.
     */
    public static function json(mixed $data, int $status = 200): string
    {
        Response::withJsonHeaders();
        http_response_code($status);
        return json_encode($data, JSON_THROW_ON_ERROR);
    }

    /**
     * Envia una respuesta `HTTP 204 No Content` (Sin Contenido).
     */
    public static function noContent(int $status = 204): null
    {
        http_response_code($status);
        return null;
    }

    /**
     * Redirigir a una ruta segun los query params.
     */
    public static function redirect(array $queryParams, int $status = 302): void
    {
        http_response_code($status);
        header('Location: ?' . Request::buildQuery($queryParams));
        exit;
    }
}
