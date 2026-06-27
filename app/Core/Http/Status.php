<?php

namespace App\Core\Http;

enum Status: int
{
    case OK = 200;
    case CREATED = 201;
    case NO_CONTENT = 204;

    case MOVED_PERMANENTLY = 301;
    case FOUND = 302;
    case NOT_MODIFIED = 304;

    case BAD_REQUEST = 400;
    case UNAUTHORIZED = 401;
    case FORBIDDEN = 403;
    case NOT_FOUND = 404;
    case INTERNAL_SERVER_ERROR = 500;
}
