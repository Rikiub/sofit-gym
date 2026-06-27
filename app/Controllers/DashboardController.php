<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Core\Auth\UsuarioSession;
use App\Models\AsistenciaModel;
use App\Models\Clientes\ClientesModel;
use App\Models\FacturacionPagosModel;

class DashboardController extends BaseController
{
    public function __construct(
        private AsistenciaModel $asistenciaModel,
        private ClientesModel $clientesModel,
        private FacturacionPagosModel $facturacionModel,
    ) {}

    public function index(): string
    {
        $asistencias = $this->asistenciaModel->obtenerEntradasHoy();
        $clientesMensuales = $this->clientesModel->query(filters: [
            'fecha_inicio_desde' => date('Y-m-01'),
            'fecha_inicio_hasta' => date('Y-m-t'),
        ]);
        $ingresosMensuales = $this->facturacionModel->obtenerIngresosMesActual();

        $usuario = UsuarioSession::getUsuario();
        return $this->templates->render('dashboard', [
            "usuario" => $usuario,
            "asistencias" => $asistencias,
            "clientesMensuales" => $clientesMensuales,
            "ingresosMensuales" => $ingresosMensuales,
        ]);
    }
}
