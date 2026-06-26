<?php

use App\Helpers\Auth\UsuarioSession;
use App\Helpers\BitacoraLogger;
use App\Helpers\Http\Request;
use App\Helpers\Http\Response;
use CuyZ\Valinor\Mapper\MappingError;
use DI\ContainerBuilder;

// Constantes locales
const CONTROLLERS_NAMESPACE = 'App\Controllers';

// Obtener query params
$page = $_GET['page'] ?? 'dashboard';
$action = $_GET['action'] ?? 'index';

// Construir clase a partir de los query params
$className = ucfirst($page) . 'Controller';
$classPath = '\\' . CONTROLLERS_NAMESPACE . "\\$className";

// FRONT CONTROLLER
try {
    session_start();

    // Configurar inyector de dependencias (PHP-DI).
    // Dependiendo de las dependencias que tengan en los __contruct de los controladores
    // el inyector las instanciara automaticamente con la configuración definida.
    $builder = new ContainerBuilder();
    $builder->addDefinitions(require "config/container.php")->useAttributes(true);
    if (!DEBUG) {
        // Activar cache en producción
        $builder->enableCompilation(CACHE_DIR . '/php-di');
    }
    $container = $builder->build();

    // Set contexto inicial del logger
    $logger = $container->get(BitacoraLogger::class);
    if (method_exists($logger, 'setRouteContext')) {
        $logger->setRouteContext($page, $action);
    }

    if (!class_exists($classPath)) {
        if (Request::wantsJson()) {
            // Si no se encuentra la pagina, devolver error como JSON
            echo Response::json([
                'error' => 'Not Found',
                'message' => "Controller {$className} not founded",
                ...(DEBUG ? ['controller' => $classPath] : [])
            ], 404);
        } else {
            // Si no se encuentra la pagina, redirigir a pagina de error.
            Response::redirect([
                'page' => 'error',
                'status' => 404,
            ]);
        }
        exit;
    }

    /** Instanciar controlador e inyectar sus dependencias automaticamente
     * @var \App\Controllers\BaseController
     */
    $controller = $container->get($classPath);

    if (!method_exists($controller, $action)) {
        throw new Exception("Method '$action' not founded in controller '$className'");
    }

    // Si el usuario no ha iniciado sesion, redigirir a pagina de login.
    $usuario = UsuarioSession::getUsuario();
    if ($page !== "login" && !$usuario) {
        Response::redirect(["page" => "login"]);
        exit;
    }

    // Ejecutar controlador junto a su metodo
    $respuesta = $controller->$action();

    // Mostrar respuesta como string
    // Si es HTML, el navegador lo renderizara
    echo $respuesta;
} catch (MappingError $error) {
    // Capturar errores de Valinor

    $messages = $error->messages();
    $errors = [];

    foreach ($messages as $m) {
        $errors[] = DEBUG ? [
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
    ], 400);
} catch (Throwable $error) {
    // Capturar todos los errores

    // Registrar error en los logs del servidor en producción
    error_log(sprintf("Error: %s en %s:%d", $error->getMessage(), $error->getFile(), $error->getLine()));

    if (DEBUG || Request::wantsJson()) {
        $res = [
            'error' => 'Internal Server Error',
            'message' => DEBUG ? $error->getMessage() : 'An unexpected error occurred on the server'
        ];

        if (DEBUG) {
            $res["exception"] = get_class($error);
            $res['file'] = $error->getFile();
            $res['line'] = $error->getLine();
            $res['trace'] = $error->getTrace();
        }

        echo Response::json($res, 500);
    } else {
        Response::redirect(['page' => 'error', 'status' => 500]);
    }
}
