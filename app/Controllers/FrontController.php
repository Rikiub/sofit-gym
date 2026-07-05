<?php

namespace App\Controllers;

use Psr\Container\ContainerInterface;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Http\StatusCode;
use App\Services\Auth\UserSession;
use App\Services\Auth\AuthenticatedUser;
use App\Core\Config;
use App\Services\Logging\BitacoraLogger;
use CuyZ\Valinor\Mapper\MappingError;
use Throwable;
use Exception;

/** Punto de entrada a la aplicación. */
class FrontController
{
    private const CONTROLLERS_NAMESPACE = 'App\Controllers';
    private const DEFAULT_PAGE = "dashboard";
    private const DEFAULT_ACTION = "index";

    private ?AuthenticatedUser $user;
    private bool $isDebug;

    public function __construct(private ContainerInterface $container)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $this->user = UserSession::getCurrent();
        $this->isDebug = Config::get("debug");
    }

    /**
     * Iniciar gestión de  la aplicacion
     */
    public function run(): void
    {
        // Obtener parametros para las rutas
        $page = $_GET['page'] ?? self::DEFAULT_PAGE;
        $action = $_GET['action'] ?? self::DEFAULT_ACTION;

        try {
            // Construir clase a partir de los parametros
            $className = ucfirst($page) . 'Controller';
            $classPath = '\\' . self::CONTROLLERS_NAMESPACE . "\\$className";

            // Verificar la existencia del controlador
            if (!class_exists($classPath)) {
                $this->handleNotFound($className, $classPath);
                return;
            }

            // Ejecutar middlewares
            $this->middlewares($page, $action);

            // Resolver controlador desde el contenedor DI
            /** @var Controller */
            $controller = $this->container->get($classPath);

            if (!method_exists($controller, $action)) {
                throw new Exception("Method '$action' not found in controller '$className'");
            }

            // Ejecutar accion del controlador y mostrar resultado
            echo $controller->$action();
        } catch (MappingError $error) {
            $this->handleValidationError($error);
        } catch (Throwable $error) {
            $this->handleServerError($error);
        }
    }

    /** Acciones a ejecutar antes de llegar al controlador. */
    private function middlewares(string $page, string $action): void
    {
        // Autentificar usuario actual
        if ($page !== "login" && !$this->user) {
            Response::redirect(["page" => "login"]);
        }

        // Inicializar contexto del logger
        $logger = $this->container->get(BitacoraLogger::class);
        $logger->setRouteContext($page, $action);
    }

    private function handleNotFound(string $className, string $classPath): void
    {
        if (Request::wantsJson()) {
            echo Response::json([
                'error' => 'Not Found',
                'message' => "Controller {$className} not found",
                ...($this->isDebug ? ['controller' => $classPath] : [])
            ], StatusCode::NOT_FOUND);
        } else {
            Response::redirect([
                'page' => 'error',
                'status' => StatusCode::NOT_FOUND->value
            ]);
        }
    }

    private function handleValidationError(MappingError $error): void
    {
        $errors = [];
        foreach ($error->messages() as $m) {
            $errors[] = $this->isDebug ? [
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
        ], StatusCode::BAD_REQUEST);
    }

    private function handleServerError(Throwable $error): void
    {
        error_log(sprintf("Error: %s en %s:%d", $error->getMessage(), $error->getFile(), $error->getLine()));

        if ($this->isDebug || Request::wantsJson()) {
            $res = [
                'error' => 'Internal Server Error',
                'message' => $this->isDebug ? $error->getMessage() : 'An unexpected error occurred on the server'
            ];

            if ($this->isDebug) {
                $res["exception"] = get_class($error);
                $res['file'] = $error->getFile();
                $res['line'] = $error->getLine();
                $res['trace'] = $error->getTrace();
            }

            echo Response::json($res, StatusCode::INTERNAL_SERVER_ERROR);
        } else {
            Response::redirect([
                'page' => 'error',
                'status' => StatusCode::INTERNAL_SERVER_ERROR->value
            ]);
        }
    }
}
