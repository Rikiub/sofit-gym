<?php

namespace App\Controllers;

use App\Core\Auth\UsuarioSession;
use App\Core\BitacoraLogger;
use App\Core\Http\Request;
use App\Core\Http\Response;
use CuyZ\Valinor\Normalizer\Normalizer;
use DI\Attribute\Inject;
use League\Plates\Engine;

abstract class Controller
{
    /** 
     * Atributos inyectados por PHP-DI automaticamente.
     * Estan aqui por comodidad ya que se usan en la mayoria de vistas.
     */
    #[Inject]
    protected Engine $templates;
    #[Inject]
    protected BitacoraLogger $logger;

    /** Dependencias para helpers internos. */
    #[Inject]
    private Normalizer $normalizer;

    protected function json(mixed $data, int $status = 200): string|null
    {
        // No Content
        if ($data === null) {
            return Response::noContent($status);
        }

        // JSON
        Response::withJsonHeaders();
        return $this->normalizer->normalize($data);
    }

    /** Bloquea el acceso a una ruta y redirige a la pagina de error si el usuario no tiene el permiso. */
    protected function protect(string $permiso): void
    {
        $usuario = UsuarioSession::getCurrent();

        if (!$usuario || !$usuario->hasPermiso($permiso)) {
            if (Request::wantsJson()) {
                // Enviar JSON con mensaje de error
                echo Response::json(["message" => "No tienes permiso para usar esta accion"], 403);
            } else {
                // Redireccionar a pagina de error
                Response::redirect([
                    "page" => "error",
                    "status" => 403,
                ]);
            }
            exit;
        }
    }
}
