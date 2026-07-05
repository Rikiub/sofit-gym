<?php

namespace App\Controllers;

use App\Services\Auth\UserSession;
use App\Services\Logging\BitacoraLogger;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Http\StatusCode;
use DI\Attribute\Inject;
use League\Plates\Engine;
use CuyZ\Valinor\Normalizer\Normalizer;

abstract class Controller
{
    // Atributos inyectados por PHP-DI automaticamente.
    // Estan por comodidad ya que se usan en la mayoria de controladores.
    // De esta forma se evita instanciarlos manualmente en cada controlador.

    /** Renderizador de plantillas. Utilizado para cargar las vistas. */
    #[Inject]
    protected Engine $templates;

    /** Logger para registrar eventos en la bitacora. */
    #[Inject]
    protected BitacoraLogger $logger;

    /** Convertidor de objetos a JSON. */
    #[Inject]
    private Normalizer $normalizer;

    // Helpers

    /** Convierte cualquier dato en una respuesta JSON. */
    protected function json(mixed $data, StatusCode $status = StatusCode::OK): string|null
    {
        // No Content
        if ($data === null) {
            return Response::noContent($status);
        }

        // JSON
        Response::withJsonHeaders();
        Response::withStatus($status);
        return $this->normalizer->normalize($data);
    }

    /** Bloquea el acceso a una ruta y redirige a la pagina de error si el usuario no tiene el permiso. */
    protected function protect(string $permiso): void
    {
        $user = UserSession::get();

        if (!$user || !$user->hasPermiso($permiso)) {
            if (Request::wantsJson()) {
                // Enviar JSON con mensaje de error
                echo Response::json(
                    ["message" => "No tienes permiso para usar esta accion"],
                    StatusCode::FORBIDDEN
                );
            } else {
                // Redireccionar a pagina de error
                Response::redirect([
                    "page" => "error",
                    "status" => StatusCode::FORBIDDEN->value,
                ]);
            }
            exit;
        }
    }
}
