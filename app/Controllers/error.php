<?php

namespace App\Controllers;

use App\Core\ControllerTools;
use App\Core\Http\Request;
use App\Core\Http\Status;

switch (ControllerTools::action()) {
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

        return ControllerTools::render('error', [
            'message' => "{$status->value}: {$message}"
        ]);
}
