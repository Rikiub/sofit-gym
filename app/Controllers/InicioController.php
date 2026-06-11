<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Helpers\Auth\UsuarioSession;

class InicioController extends BaseController
{
    public function index(): string
    {
        $usuario = UsuarioSession::getUsuario();
        return $this->templates->render('inicio', [
            "usuario" => $usuario,
        ]);
    }
}
