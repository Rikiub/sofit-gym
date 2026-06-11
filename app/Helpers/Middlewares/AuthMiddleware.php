<?php

namespace App\Helpers\Middlewares;

use App\Helpers\Auth\UsuarioSession;
use App\Helpers\Response;

/** 
 * Verifica que el usuario haya inicado sesion
 * y restringe el acceso al usuario segun las rutas definidas.
 */
class AuthMiddleware
{
    public function __construct(private array $routes) {}

    public function checkAccess(string $page): void
    {
        $usuario = UsuarioSession::getUsuario();

        // Si el usuario no ha iniciado sesion, redigirir a pagina de login.
        if ($page !== "login" && !$usuario) {
            Response::redirect(["page" => "login"]);
            exit;
        }

        $route = $this->routes[$page] ?? null;
        if (!$route) {
            // Si no esta en la lista, se asume que tiene acceso de todos modos
            // Por razones de compatibilidad.
            return;
        }
        $rolesPermitidos = $route["roles"];

        if (!in_array($usuario->rol, $rolesPermitidos, true)) {
            Response::redirectToError(403);
            exit;
        }
    }
}
