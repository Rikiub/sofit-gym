<?php

namespace App\Controllers;

use App\Controllers\Controller;
use App\Services\Auth\UserSession;
use App\Models\AsistenciaModel;
use App\Models\FacturacionModel;
use App\Models\Clientes\ClienteModel;

class DashboardController extends Controller
{
    public function __construct(
        private AsistenciaModel $asistenciaModel,
        private ClienteModel $clienteModel,
        private FacturacionModel $facturacionModel,
    ) {}

    public function index(): string
    {
        $asistencias = $this->asistenciaModel->obtenerEntradasHoy();
        $clientesMensuales = $this->clienteModel->query(filters: [
            'fecha_inicio_desde' => date('Y-m-01'),
            'fecha_inicio_hasta' => date('Y-m-t'),
        ]);
        $ingresosMensuales = $this->facturacionModel->obtenerIngresosMesActual();

        $usuario = UserSession::get();
        return $this->render('dashboard', [
            "usuario" => $usuario,
            "asistencias" => $asistencias,
            "clientesMensuales" => $clientesMensuales,
            "ingresosMensuales" => $ingresosMensuales,
        ]);
    }
}
