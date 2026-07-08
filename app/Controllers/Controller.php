<?php

namespace App\Controllers;

use App\Core\Config;
use App\Services\Auth\UserSession;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Http\Status;
use App\Core\Plates\AssetExtension;
use League\Plates\Engine;
use League\Plates\Template\Theme;

abstract class Controller
{
    /** Renderiza una plantilla HTML */
    protected function render(string $name, array $data = []): string
    {
        // Listado de directorios donde buscar plantillas
        $dir = 'app/views/';
        $engine = Engine::fromTheme(Theme::hierarchy([
            Theme::new($dir . 'base', 'Base'),
            Theme::new($dir . 'components', 'Components'),
            Theme::new($dir . 'emails', 'Emails'),
            Theme::new($dir . 'pages', 'Pages'),
        ]))
            ->loadExtension(new AssetExtension(Config::get("web.assets")));

        // Información de la sesión accesible para las plantillas.
        $user = UserSession::get();
        $engine->addData(["sesion_usuario" => $user]);

        // Buscar y renderizar plantilla
        return $engine->render($name, $data);
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
                    Status::FORBIDDEN
                );
            } else {
                // Redireccionar a pagina de error
                Response::redirect([
                    "page" => "error",
                    "status" => Status::FORBIDDEN->value,
                ]);
            }
            exit;
        }
    }
}
