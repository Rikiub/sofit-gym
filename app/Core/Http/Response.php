<?php

namespace App\Core\Http;

use App\Core\Tools;

/** Helpers para devolver respuestas HTTP, principalmente como JSON. */
class Response
{
    public static function withJsonHeaders(): void
    {
        header('Content-Type: application/json');
    }

    public static function withStatus(Status $code): void
    {
        http_response_code($code->value);
    }

    /**
     * Serializa los datos en una JSON string y envia los headers `application/json`.
     */
    public static function json(mixed $data, Status $status = Status::OK): string
    {
        self::withJsonHeaders();
        self::withStatus($status);
        return Tools::normalizeJson($data);
    }

    /**
     * Envia una respuesta `HTTP 204 No Content` (Sin Contenido).
     */
    public static function noContent(Status $status = Status::NO_CONTENT): null
    {
        self::withStatus($status);
        return null;
    }

    /**
     * Redirigir a una ruta segun los query params.
     */
    public static function redirect(array $query, Status $status = Status::FOUND): void
    {
        self::withStatus($status);
        header('Location: ?' . Request::buildQuery($query));
        exit;
    }
}
