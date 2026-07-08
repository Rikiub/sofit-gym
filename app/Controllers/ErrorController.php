<?php

namespace App\Controllers;

use App\Controllers\Controller;
use App\Core\Http\Request;
use App\Core\Http\Status;

class ErrorController extends Controller
{
    public function index(): string
    {
        $status = Status::from(
            Request::queryInt("status") ?? 500
        );
        $message = match ($status) {
            Status::FORBIDDEN => "No tienes permiso para acceder a esta pagina",
            Status::NOT_FOUND => 'Pagina no encontrada',
            Status::METHOD_NOT_ALLOWED => 'Metodo no soportado',
            default => 'Ocurrio un error inesperado en el servidor',
        };

        return $this->render('error', [
            'message' => "{$status->value}: {$message}"
        ]);
    }
}
