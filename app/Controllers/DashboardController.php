<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Helpers\Auth\UsuarioSession;

class DashboardController extends BaseController
{
    public function index(): string
    {
        $usuario = UsuarioSession::getUsuario();
        return $this->templates->render('dashboard', [
            "usuario" => $usuario,
        ]);
    }
}
