<?php

namespace App\Helpers\Auth;

use App\Helpers\Response;

/** 
 * Verifica que el usuario haya inicado sesion
 * y restringe el acceso al usuario segun las rutas definidas.
 */
class AuthMiddleware
{
    public function __construct(private array $routes) {}

    public function checkAccess(string $page, string $action): void
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
        $permisosPermitidos = $route["permisos"] ?? [];

        // Si se solicita una acción específica y está configurada, sobrescribimos los roles
        if (isset($route["actions"][$action])) {
            $permisosPermitidos = $route["actions"][$action];
        }

        if (in_array("todos", $permisosPermitidos, true)) {
            return;
        }

        // Validar autorización
        if (!array_intersect($usuario->permisos, $permisosPermitidos)) {
            Response::redirectToError(403);
            exit;
        }
    }
}
