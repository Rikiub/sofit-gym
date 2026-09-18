<?php

namespace App\Controllers;

use App\Core\Route;
use App\Models\AsistenciaModel;
use App\Models\BitacoraModel;
use App\Services\Reportes\ReporteAsistencia;

$model = new AsistenciaModel();

switch (Route::action()) {
    case "index":
        Route::protect("asistencia:ver");

        $fechaSeleccionada = $_GET['fecha'] ?? date('Y-m-d');
        unset($_SESSION['mensaje'], $_SESSION['tipo_mensaje']);

        return Route::render('asistencia', [
            'entradasHoy' => $model->obtenerEntradasHoy(),
            'fechaSeleccionada' => $fechaSeleccionada,
            'ocupacion' => $model->obtenerOcupacionPorFranjas($fechaSeleccionada),
            'detalleEntradas' => $model->obtenerEntradasPorFecha($fechaSeleccionada),
            'mensaje' => $_SESSION['mensaje'] ?? '',
            'tipoMensaje' => $_SESSION['tipo_mensaje'] ?? '',
        ]);

    case "buscar_clientes_ajax":
        Route::protect("asistencia:ver");

        if (!isset($_GET['ajax']) || $_GET['ajax'] !== 'buscar_clientes')
            return;
        $termino = $_GET['termino'] ?? '';
        $resultados = $model->buscarClientes($termino);
        header('Content-Type: application/json');
        echo json_encode($resultados);
        exit;

    case "registrar":
        Route::protect("asistencia:crear");

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Método no permitido']);
            exit;
        }

        $cedula = $_POST['cedula'] ?? '';
        $hora   = !empty($_POST['hora']) ? $_POST['hora'] : null;

        if (empty($cedula)) {
            echo json_encode(['success' => false, 'message' => 'Debe seleccionar un cliente.']);
            exit;
        }

        $resultado = $model->registrarEntrada($cedula, $hora);

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($resultado, JSON_INVALID_UTF8_SUBSTITUTE);
        exit;

    case "buscar_entradas_ajax":
        Route::protect("asistencia:ver");

        if (!isset($_GET['ajax']) || $_GET['ajax'] !== 'buscar_entradas')
            return;
        $termino = $_GET['termino'] ?? '';
        $resultados = $model->buscarEntradas($termino);
        header('Content-Type: application/json');
        echo json_encode($resultados);
        exit;

    case "buscar_entradas_hoy":
        Route::protect("asistencia:ver");

        $resultados = $model->obtenerEntradasHoy();
        header('Content-Type: application/json');
        echo json_encode($resultados);
        exit;

    case "obtener_totales":
        Route::protect("asistencia:ver");

        $inicio = $_GET["inicio"] ?? null;
        $fin = $_GET["fin"] ?? null;
        $resultados = $model->obtenerTotalesPorRango($inicio, $fin);

        header('Content-Type: application/json');
        echo json_encode($resultados);
        exit;

    case "editar":
        Route::protect("asistencia:editar");

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
        $old = $model->findCliente($id);
        $ok = $model->actualizarEntrada($id, $nuevaHora);

        if ($ok) {
            // Obtener datos nuevos después de la actualización
            $new = $model->findCliente($id);
        }
        echo json_encode(['success' => $ok]);
        exit;

    case "eliminar":
        Route::protect("asistencia:eliminar");

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Método no permitido']);
            return;
        }

        $id = intval($_POST['id']);
        $old = $model->findCliente($id);
        $ok = $model->eliminarEntrada($id);

        echo json_encode(['success' => $ok]);
        exit;

        // Reportes
    case "vistaAsistencia":
        // Renderiza el formulario usando el motor Plates cargando tu nueva vista
        Route::protect("clientes:ver");
        echo Route::render('reportes/asistencia');
        exit;

    /**
     * Generar reporte PDF del histórico de asistencias (opcionalmente filtrado por rango de fechas)
     */
    case "generarReporte":
        // 1. Proteger la ruta bajo el permiso correspondiente
        Route::protect("asistencia:ver");

        // 2. Capturar los filtros opcionales de fecha desde la URL ($_GET)
        $fechaInicio = $_GET['inicio'] ?? null;
        $fechaFin = $_GET['fin'] ?? null;

        // 3. Solicitar los datos procesados al modelo
        $asistenciasData = $model->obtenerAsistenciasParaReporte($fechaInicio, $fechaFin);

        // Instanciar la clase FPDF del reporte de asistencia
        $pdf = new ReporteAsistencia();

        // Establecer metadatos del documento PDF
        $pdf->SetTitle(utf8_decode('Reporte de Asistencias - SOFIT GYM'));
        $pdf->SetAuthor('Sistema SOFIT GYM');

        // Procesar y estructurar el cuerpo del reporte con los datos provistos
        $pdf->crearReporte($asistenciasData, $fechaInicio, $fechaFin);

        // Renderizar y forzar la visualización limpia en el navegador ('I')
        $pdf->Output('I', 'reporte_asistencias.pdf');
        exit;
}
