<?php

namespace App\Controllers;

use App\Helpers\Auth\UsuarioSession;
use App\Helpers\BitacoraLogger;
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
            http_response_code(403);
            echo "403 Forbidden: No tienes el permiso '$permiso'.";
            exit;
        }
    }
}
