<?php

namespace App\Controllers;

use App\Helpers\Auth\UsuarioSession;
use App\Helpers\BitacoraLogger;
use App\Helpers\Response;
use DI\Attribute\Inject;
use League\Plates\Engine;

abstract class BaseController
{
    #[Inject]
    protected Engine $templates;

    #[Inject]
    protected BitacoraLogger $logger;

    protected function protect(string $permiso)
    {
        $usuario = UsuarioSession::getUsuario();

        if (!$usuario || !$usuario->hasPermiso($permiso)) {
            if (Response::wantsJson()) {
                // Enviar JSON con mensaje de error
                echo (new Response(null))
                    ->json(["message" => "No tienes permiso para usar esta accion"], 403);
            } else {
                // Redireccionar a pagina de error
                Response::redirectToError(403);
            }

            exit;
        }
    }
}
