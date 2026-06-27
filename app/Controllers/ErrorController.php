<?php

namespace App\Controllers;

use App\Controllers\Controller;
use App\Core\Http\Request;

class ErrorController extends Controller
{
    public function index(): string
    {
        $status = Request::queryInt("status") ?? null;
        $message = match ($status) {
            403 => "Forbidden: No tienes permiso para acceder a esta pagina",
            404 => 'Pagina no encontrada',
            405 => 'Metodo no soportado',
            500 => 'Ocurrio un error inesperado en el servidor',
            default => "Ocurrio un error inesperado en el servidor",
        };

        return $this->templates->render('error', [
            'message' => "{$status}: {$message}"
        ]);
    }
}
