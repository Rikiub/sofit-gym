<?php

namespace App\Controllers;

use App\Core\Route;
use App\Core\Http\Request;
use App\Core\Http\Status;

switch (Route::action()) {
    case "index":
        $status = Status::from(
            Request::queryInt("status") ?? 500
        );
        $message = match ($status) {
            Status::FORBIDDEN => "No tienes permiso para acceder a esta pagina",
            Status::NOT_FOUND => 'Pagina no encontrada',
            Status::METHOD_NOT_ALLOWED => 'Metodo no soportado',
            default => 'Ocurrio un error inesperado en el servidor',
        };

        return Route::render('error', [
            'message' => "{$status->value}: {$message}"
        ]);
}
