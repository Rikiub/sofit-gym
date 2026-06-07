<?php

use App\Helpers\Auth\UsuarioSession;
use App\Helpers\Response;
use CuyZ\Valinor\Mapper\MappingError;
use DI\ContainerBuilder;
use Psr\Log\LoggerInterface;

// Directorios
define("ROOT_DIR", dirname(__DIR__));
define("CACHE_DIR", ROOT_DIR . "/.cache");

define('BASE_DIR', rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\'));
define('ASSETS_DIR', BASE_DIR . '/assets');

// Modo Desarrollo
define('DEBUG', filter_var($_ENV["DEBUG"] ?? true, FILTER_VALIDATE_BOOLEAN));

// Timezones
define("TIMEZONE", $_ENV["TIMEZONE"] ?? 'America/Caracas');
define('TIMEZONE_OFFSET', (new DateTime('now', new DateTimeZone(TIMEZONE)))->format('P'));
date_default_timezone_set(TIMEZONE);

// Constantes
const CONTAINER_FILE = 'app/container.php';
const CONTROLLERS_NAMESPACE = 'App\Controllers';

// Obtener query params
$page = $_GET['page'] ?? 'inicio';
$action = $_GET['action'] ?? 'index';

// Construir clase a partir de los query params
$className = ucfirst($page) . 'Controller';
$classPath = '\\' . CONTROLLERS_NAMESPACE . "\\$className";

// Response
$response = new Response(normalizer: null);
$wantsJson = $response->acceptsJson()
    || $response->isJson()
    || ($_GET['format'] ?? '') === 'json';

// FRONT CONTROLLER
try {
    session_start();

    // Configurar inyector de dependencias (PHP-DI).
    // Dependiendo de las dependencias que tengan en los __contruct de los controladores
    // el inyector las instanciara automaticamente con la configuración definida
    // en el archivo CONTAINER_FILE.
    $builder = new ContainerBuilder();
    $builder->addDefinitions(CONTAINER_FILE)->useAttributes(true);
    if (!DEBUG) {
        // Activar cache en producción
        $builder->enableCompilation(CACHE_DIR . '/php-di');
    }
    $container = $builder->build();

    // Set contexto inicial del logger
    $logger = $container->get(LoggerInterface::class);
    if (method_exists($logger, 'setRouteContext')) {
        $logger->setRouteContext($page, $action);
    }

    if (!class_exists($classPath)) {
        if ($wantsJson) {
            // Si no se encuentra la pagina, devolver error como JSON
            echo $response->json([
                'error' => 'Not Found',
                'message' => "Controller {$className} not founded",
                ...(DEBUG ? ['controller' => $classPath] : [])
            ], 404);
        } else {
            // Si no se encuentra la pagina, redirigir a pagina de error.
            $response->redirect([
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

    // Si no se ha iniciado sesión, redigir a pagina 'login' siempre.
    if ($page !== "login" && !UsuarioSession::getUsuario()) {
        $response->redirect(["page" => "login"]);
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

    echo $response->json([
        'error' => 'Validation Error',
        'message' => 'The request contains invalid data',
        'errors' => $errors
    ], 400);
} catch (Throwable $error) {
    // Capturar todos los errores

    // Registrar error en los logs del servidor en producción
    error_log(sprintf("Error: %s en %s:%d", $error->getMessage(), $error->getFile(), $error->getLine()));

    if ($wantsJson) {
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

        echo $response->json($res, 500);
    } else {
        $response->redirect(['page' => 'error', 'status' => 500]);
    }
}
