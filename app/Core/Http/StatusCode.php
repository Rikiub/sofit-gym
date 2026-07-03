<?php

namespace App\Core\Http;

/** Codigos de estado HTTP. 
 * Si solo tienes el codigo como entero, conviertelo con:
 * 
 * ```
 * StatusCode::from($statusCode)
 * ```
 */
enum StatusCode: int
{
    // ==================== 2xx: Éxito ====================
    /** 200 OK. La solicitud ha tenido éxito. */
    case OK = 200;

    /** 201 Created. La solicitud tuvo éxito y se creó un nuevo recurso. */
    case CREATED = 201;

    /** 202 Accepted. La solicitud ha sido aceptada para procesamiento, pero aún no se completa (tareas asíncronas). */
    case ACCEPTED = 202;

    /** 204 No Content. La solicitud tuvo éxito pero no hay contenido que devolver (típico en DELETE). */
    case NO_CONTENT = 204;

    // ==================== 3xx: Redirecciones ====================
    /** 301 Moved Permanently. El recurso ha cambiado de URL permanentemente. */
    case MOVED_PERMANENTLY = 301;

    /** 302 Found. Redirección temporal. El recurso está en otra URL por ahora. */
    case FOUND = 302;

    /** 304 Not Modified. El recurso no ha cambiado (usa la caché del navegador). */
    case NOT_MODIFIED = 304;

    // ==================== 4xx: Errores del Cliente ====================
    /** 400 Bad Request. Error de sintaxis en la petición o parámetros inválidos. */
    case BAD_REQUEST = 400;

    /** 401 Unauthorized. Falta autenticación o las credenciales son inválidas (no has iniciado sesión). */
    case UNAUTHORIZED = 401;

    /** 403 Forbidden. Estás autenticado, pero NO tienes permisos para acceder a este recurso. */
    case FORBIDDEN = 403;

    /** 404 Not Found. El recurso solicitado no existe en el servidor. */
    case NOT_FOUND = 404;

    /** 405 Method Not Allowed. El método HTTP no está permitido para este endpoint. */
    case METHOD_NOT_ALLOWED = 405;

    /** 409 Conflict. Conflicto con el estado actual (ej: intentar crear un usuario con email duplicado). */
    case CONFLICT = 409;

    /** 422 Unprocessable Entity. Error de validación. La sintaxis es correcta pero los datos no cumplen las reglas de negocio. */
    case UNPROCESSABLE_ENTITY = 422;

    /** 429 Too Many Requests. Has excedido el límite de peticiones por tiempo (rate limiting). */
    case TOO_MANY_REQUESTS = 429;

    // ==================== 5xx: Errores del Servidor ====================
    /** 500 Internal Server Error. Error inesperado en el servidor (excepción no capturada, fallo en BD). */
    case INTERNAL_SERVER_ERROR = 500;

    /**
     * Obtiene una descripción del código de estado.
     */
    public function reason(): string
    {
        return match ($this) {
            self::OK => 'OK',
            self::CREATED => 'Created',
            self::ACCEPTED => 'Accepted',
            self::NO_CONTENT => 'No Content',

            self::MOVED_PERMANENTLY => 'Moved Permanently',
            self::FOUND => 'Found',
            self::NOT_MODIFIED => 'Not Modified',

            self::BAD_REQUEST => 'Bad Request',
            self::UNAUTHORIZED => 'Unauthorized',
            self::FORBIDDEN => 'Forbidden',
            self::NOT_FOUND => 'Not Found',
            self::METHOD_NOT_ALLOWED => 'Method Not Allowed',
            self::CONFLICT => 'Conflict',
            self::UNPROCESSABLE_ENTITY => 'Unprocessable Entity',
            self::TOO_MANY_REQUESTS => 'Too Many Requests',

            self::INTERNAL_SERVER_ERROR => 'Internal Server Error',
        };
    }

    /** Verificar si el codigo de estado indica exito (2xx). */
    public function isSuccess(): bool
    {
        return $this->value >= 200 && $this->value < 300;
    }

    /** Verificar si el codigo de estado indica error del cliente (4xx). */
    public function isClientError(): bool
    {
        return $this->value >= 400 && $this->value < 500;
    }

    /** Verificar si el codigo de estado indica error del servidor (5xx). */
    public function isServerError(): bool
    {
        return $this->value >= 500 && $this->value < 600;
    }
}
