<?php

namespace App\Controllers;

use App\Core\Route;
use App\Services\Auth\UserSession;
use App\Models\AsistenciaModel;
use App\Models\FacturacionModel;
use App\Models\Clientes\ClienteModel;

$asistenciaModel = new AsistenciaModel();
$clienteModel = new ClienteModel();
$facturacionModel = new FacturacionModel();

switch (Route::action()) {
    case "index":
        $asistencias = $asistenciaModel->obtenerEntradasHoy();
        $clientesMensuales = $clienteModel->query(filters: [
            'fecha_inicio_desde' => date('Y-m-01'),
            'fecha_inicio_hasta' => date('Y-m-t'),
        ]);
        $ingresosMensuales = $facturacionModel->obtenerIngresosMesActual();

        $usuario = UserSession::get();
        return Route::render('dashboard', [
            "usuario" => $usuario,
            "asistencias" => $asistencias,
            "clientesMensuales" => $clientesMensuales,
            "ingresosMensuales" => $ingresosMensuales,
        ]);
}
