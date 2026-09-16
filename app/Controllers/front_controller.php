<?php

namespace App\Controllers;

use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Http\Status;
use App\Core\Config;
use App\Services\Auth\UserSession;
use App\Services\Auth\CurrentUser;
use CuyZ\Valinor\Mapper\MappingError;
use Throwable;
use Exception;

const CONTROLLERS_DIR = 'app/Controllers';
const DEFAULT_PAGE = "dashboard";
const DEFAULT_ACTION = "index";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$user = UserSession::get();
$isDebug = Config::get("debug");

// Obtener parametros para las rutas
$page = $_GET['page'] ?? DEFAULT_PAGE;
$action = $_GET['action'] ?? DEFAULT_ACTION;

try {
    // Construir a partir de los parametros
    $controllerPath = CONTROLLERS_DIR . "/$page.php";

    // Verificar la existencia del controlador
    if (!is_file($controllerPath)) {
        handleNotFound($page, $controllerPath, $isDebug);
        return;
    }

    // Ejecutar middlewares
    middlewares($page, $action, $user);

    // Ejecutar accion del controlador y mostrar resultado
    echo require $controllerPath;
} catch (MappingError $error) {
    handleValidationError($error, $isDebug);
} catch (Throwable $error) {
    handleServerError($error, $isDebug);
}

/** Acciones a ejecutar antes de llegar al controlador. */
function middlewares(string $page, string $action, ?CurrentUser $user): void
{
    // Autentificar usuario actual
    if ($page !== "login" && !$user) {
        Response::redirect(["page" => "login"]);
    }
}

function handleNotFound(string $className, string $classPath, bool $isDebug): void
{
    if (Request::wantsJson()) {
        echo Response::json([
            'error' => 'Not Found',
            'message' => "Page {$className} not found",
            ...($isDebug ? ['controller' => $classPath] : [])
        ], Status::NOT_FOUND);
    } else {
        Response::redirect([
            'page' => 'error',
            'status' => Status::NOT_FOUND->value
        ]);
    }
}

function handleValidationError(MappingError $error, bool $isDebug): void
{
    $errors = [];
    foreach ($error->messages() as $m) {
        $errors[] = $isDebug ? [
            'name' => $m->name(),
            'source' => $m->sourceValue(),
            'expected' => $m->expectedSignature(),
        ] : [
            'name' => $m->name(),
            'message' => 'The provided value is invalid'
        ];
    }

    echo Response::json([
        'error' => 'Validation Error',
        'message' => 'The request contains invalid data',
        'errors' => $errors
    ], Status::BAD_REQUEST);
}

function handleServerError(Throwable $error, bool $isDebug): void
{
    if ($isDebug || Request::wantsJson()) {
        $res = [
            'error' => 'Internal Server Error',
            'message' => $isDebug ? $error->getMessage() : 'An unexpected error occurred on the server'
        ];

        if ($isDebug) {
            $res["exception"] = get_class($error);
            $res['file'] = $error->getFile();
            $res['line'] = $error->getLine();
            $res['trace'] = $error->getTraceAsString();
        }

        echo Response::json($res, Status::INTERNAL_SERVER_ERROR);
    } else {
        Response::redirect([
            'page' => 'error',
            'status' => Status::INTERNAL_SERVER_ERROR->value
        ]);
    }
}
