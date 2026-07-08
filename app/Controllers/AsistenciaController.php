<?php

namespace App\Controllers;

use App\Controllers\Controller;
use App\Core\Reportes\ReporteAsistencia;
use App\Models\AsistenciaModel;
use App\Services\Logging\BitacoraLogger;

class AsistenciaController extends Controller
{
    public function __construct(
        private $logger = new BitacoraLogger(),
        private $model = new AsistenciaModel(),
    ) {}

    public function index()
    {
        $this->protect("asistencia:ver");

        $fechaSeleccionada = $_GET['fecha'] ?? date('Y-m-d');
        unset($_SESSION['mensaje'], $_SESSION['tipo_mensaje']);

        return $this->render('asistencia', [
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
        $this->protect("asistencia:ver");

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
        $this->protect("asistencia:crear");

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
            $this->logger->info("Entrada registrada para cliente '{cedula}'", [
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
        $this->protect("asistencia:ver");

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
        $this->protect("asistencia:ver");

        $resultados = $this->model->obtenerEntradasHoy();
        header('Content-Type: application/json');
        echo json_encode($resultados);
        exit;
    }

    public function obtener_totales()
    {
        $this->protect("asistencia:ver");

        $inicio = $_GET["inicio"] ?? null;
        $fin = $_GET["fin"] ?? null;
        $resultados = $this->model->obtenerTotalesPorRango($inicio, $fin);

        header('Content-Type: application/json');
        echo json_encode($resultados);
        exit;
    }

    public function editar()
    {
        $this->protect("asistencia:editar");

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
            $this->logger->info("Entrada '{id_asistencia}' actualizada", [
                'id_asistencia' => $id,
                'datos_previos' => $old,
                'datos_nuevos'  => $new,
            ]);
        }
        echo json_encode(['success' => $ok]);
    }

    public function eliminar()
    {
        $this->protect("asistencia:eliminar");

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Método no permitido']);
            return;
        }

        $id = intval($_POST['id']);
        $old = $this->model->findCliente($id);

        $ok = $this->model->eliminarEntrada($id);
        if ($ok) {
            $this->logger->info("Entrada '{id_asistencia}' eliminada", [
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
        $this->protect("clientes:ver");
        echo $this->render('reportes/asistencia');
        exit;
    }

    /**
     * Generar reporte PDF del histórico de asistencias (opcionalmente filtrado por rango de fechas)
     */
    public function generarReporte()
    {
        // 1. Proteger la ruta bajo el permiso correspondiente
        $this->protect("asistencia:ver");

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
