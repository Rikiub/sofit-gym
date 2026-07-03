<?php

namespace App\Controllers;

use App\Controllers\Controller;
use App\Core\Http\Request;
use App\Core\Http\StatusCode;

class ErrorController extends Controller
{
    public function index(): string
    {
        $status = StatusCode::from(
            Request::queryInt("status") ?? 500
        );
        $message = match ($status) {
            StatusCode::FORBIDDEN => "No tienes permiso para acceder a esta pagina",
            StatusCode::NOT_FOUND => 'Pagina no encontrada',
            StatusCode::METHOD_NOT_ALLOWED => 'Metodo no soportado',
            default => 'Ocurrio un error inesperado en el servidor',
        };

        return $this->templates->render('error', [
            'message' => "{$status->value}: {$message}"
        ]);
    }
}
