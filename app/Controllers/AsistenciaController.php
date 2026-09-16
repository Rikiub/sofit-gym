<?php

namespace App\Controllers;

use App\Core\ControllerTools;
use App\Models\AsistenciaModel;
use App\Models\BitacoraModel;
use App\Services\Reportes\ReporteAsistencia;

class AsistenciaController
{
    public function __construct(
        private $logger = new BitacoraModel(),
        private $model = new AsistenciaModel(),
    ) {}

    public function index()
    {
        ControllerTools::protect("asistencia:ver");

        $fechaSeleccionada = $_GET['fecha'] ?? date('Y-m-d');
        unset($_SESSION['mensaje'], $_SESSION['tipo_mensaje']);

        return ControllerTools::render('asistencia', [
            'entradasHoy' => $this->model->obtenerEntradasHoy(),
            'fechaSeleccionada' => $fechaSeleccionada,
            'ocupacion' => $this->model->obtenerOcupacionPorFranjas($fechaSeleccionada),
            'detalleEntradas' => $this->model->obtenerEntradasPorFecha($fechaSeleccionada),
            'mensaje' => $_SESSION['mensaje'] ?? '',
            'tipoMensaje' => $_SESSION['tipo_mensaje'] ?? '',
        ]);
    }

    public function buscar_clientes_ajax()
    {
        ControllerTools::protect("asistencia:ver");

        if (!isset($_GET['ajax']) || $_GET['ajax'] !== 'buscar_clientes')
            return;
        $termino = $_GET['termino'] ?? '';
        $resultados = $this->model->buscarClientes($termino);
        header('Content-Type: application/json');
        echo json_encode($resultados);
        exit;
    }

    public function registrar()
    {
        ControllerTools::protect("asistencia:crear");

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Método no permitido']);
            return;
        }
        $cedula = $_POST['cedula'] ?? '';
        $hora = !empty($_POST['hora']) ? $_POST['hora'] : null;
        if (empty($cedula)) {
            echo json_encode(['success' => false, 'message' => 'Debe seleccionar un cliente.']);
            return;
        }

        $resultado = $this->model->registrarEntrada($cedula, $hora);
        if ($resultado['success']) {
            $this->logger->log("Entrada registrada para cliente '{cedula}'", [
                "modulo" => "asistencia",
                "accion" => "registrar",

                'cedula'        => $cedula,
                'id_asistencia' => $resultado['id'] ?? null,
                'fecha'         => $resultado['fecha'] ?? null,
                'datos_nuevos'  => $resultado,
            ]);
        }

        echo json_encode($resultado);
    }

    public function buscar_entradas_ajax()
    {
        ControllerTools::protect("asistencia:ver");

        if (!isset($_GET['ajax']) || $_GET['ajax'] !== 'buscar_entradas')
            return;
        $termino = $_GET['termino'] ?? '';
        $resultados = $this->model->buscarEntradas($termino);
        header('Content-Type: application/json');
        echo json_encode($resultados);
        exit;
    }

    public function buscar_entradas_hoy()
    {
        ControllerTools::protect("asistencia:ver");

        $resultados = $this->model->obtenerEntradasHoy();
        header('Content-Type: application/json');
        echo json_encode($resultados);
        exit;
    }

    public function obtener_totales()
    {
        ControllerTools::protect("asistencia:ver");

        $inicio = $_GET["inicio"] ?? null;
        $fin = $_GET["fin"] ?? null;
        $resultados = $this->model->obtenerTotalesPorRango($inicio, $fin);

        header('Content-Type: application/json');
        echo json_encode($resultados);
        exit;
    }

    public function editar()
    {
        ControllerTools::protect("asistencia:editar");

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Método no permitido']);
            return;
        }
        $id = intval($_POST['id']);
        $nuevaHora = $_POST['hora'] ?? '';
        if (empty($nuevaHora)) {
            echo json_encode(['success' => false, 'message' => 'La hora es requerida']);
            return;
        }

        // Obtener datos previos usando el modelo
        $old = $this->model->findCliente($id);
        $ok = $this->model->actualizarEntrada($id, $nuevaHora);

        if ($ok) {
            // Obtener datos nuevos después de la actualización
            $new = $this->model->findCliente($id);
            $this->logger->log("Entrada '{id_asistencia}' actualizada", [
                "modulo" => "asistencia",
                "accion" => "editar",

                'id_asistencia' => $id,
                'datos_previos' => $old,
                'datos_nuevos'  => $new,
            ]);
        }
        echo json_encode(['success' => $ok]);
    }

    public function eliminar()
    {
        ControllerTools::protect("asistencia:eliminar");

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Método no permitido']);
            return;
        }

        $id = intval($_POST['id']);
        $old = $this->model->findCliente($id);

        $ok = $this->model->eliminarEntrada($id);
        if ($ok) {
            $this->logger->log("Entrada '{id_asistencia}' eliminada", [
                "modulo" => "asistencia",
                "accion" => "eliminar",

                'id_asistencia' => $id,
                'datos_previos' => $old,
            ]);
        }

        echo json_encode(['success' => $ok]);
    }

    // Reportes
    public function vistaAsistencia()
    {
        // Renderiza el formulario usando el motor Plates cargando tu nueva vista
        ControllerTools::protect("clientes:ver");
        echo ControllerTools::render('reportes/asistencia');
        exit;
    }

    /**
     * Generar reporte PDF del histórico de asistencias (opcionalmente filtrado por rango de fechas)
     */
    public function generarReporte()
    {
        // 1. Proteger la ruta bajo el permiso correspondiente
        ControllerTools::protect("asistencia:ver");

        // 2. Capturar los filtros opcionales de fecha desde la URL ($_GET)
        $fechaInicio = $_GET['inicio'] ?? null;
        $fechaFin = $_GET['fin'] ?? null;

        // 3. Solicitar los datos procesados al modelo
        $asistenciasData = $this->model->obtenerAsistenciasParaReporte($fechaInicio, $fechaFin);

        // Instanciar la clase FPDF del reporte de asistencia
        $pdf = new ReporteAsistencia();

        // Establecer metadatos del documento PDF
        $pdf->SetTitle(utf8_decode('Reporte de Asistencias - SOFIT GYM'));
        $pdf->SetAuthor('Sistema SOFIT GYM');

        // Procesar y estructurar el cuerpo del reporte con los datos provistos
        $pdf->crearReporte($asistenciasData, $fechaInicio, $fechaFin);

        // Renderizar y forzar la visualización limpia en el navegador ('I')
        $pdf->Output('I', 'reporte_asistencias.pdf');
    }
}
