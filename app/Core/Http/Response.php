<?php

namespace App\Core\Http;

/** Helpers para devolver respuestas HTTP, principalmente como JSON. */
class Response
{
    public static function withJsonHeaders(): void
    {
        header('Content-Type: application/json');
    }

    public static function withStatus(StatusCode $code): void
    {
        http_response_code($code->value);
    }

    /**
     * Serializa los datos en una JSON string y envia los headers `application/json`.
     */
    public static function json(mixed $data, StatusCode $status = StatusCode::OK): string
    {
        self::withJsonHeaders();
        http_response_code($status->value);
        return json_encode($data, JSON_THROW_ON_ERROR);
    }

    /**
     * Envia una respuesta `HTTP 204 No Content` (Sin Contenido).
     */
    public static function noContent(StatusCode $status = StatusCode::NO_CONTENT): null
    {
        http_response_code($status->value);
        return null;
    }

    /**
     * Redirigir a una ruta segun los query params.
     */
    public static function redirect(array $queryParams, StatusCode $status = StatusCode::FOUND): void
    {
        http_response_code($status->value);
        header('Location: ?' . Request::buildQuery($queryParams));
        exit;
    }
}
