<?php

namespace App\Controllers;

use App\Core\Route;
use App\Models\AsistenciaModel;
use App\Models\BitacoraModel;
use App\Services\Reportes\ReporteAsistencia;
use Throwable;

$bitacora = new BitacoraModel();
$model    = new AsistenciaModel();

function jsonOut($data, int $code = 200): void
{
    if (ob_get_level() > 0) {
        ob_clean();
    }
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-store');

    try {
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
    } catch (Throwable $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Error al serializar la respuesta.',
        ]);
    }
    exit;
}

$action = Route::action();
if ($action !== 'index' && $action !== 'vistaAsistencia') {
    ini_set('display_errors', '0');
    ini_set('display_startup_errors', '0');
    error_reporting(E_ALL);
    if (ob_get_level() === 0) {
        ob_start();
    }
}

switch ($action) {

    case "index":
        Route::protect("asistencia:ver");

        $fechaSeleccionada = $_GET['fecha'] ?? date('Y-m-d');
        unset($_SESSION['mensaje'], $_SESSION['tipo_mensaje']);

        echo Route::render('asistencia', [
            'entradasHoy'       => $model->obtenerEntradasHoy(),
            'fechaSeleccionada' => $fechaSeleccionada,
            'ocupacion'         => $model->obtenerOcupacionPorFranjas($fechaSeleccionada),
            'detalleEntradas'   => $model->obtenerEntradasPorFecha($fechaSeleccionada),
            'mensaje'           => '',
            'tipoMensaje'       => '',
        ]);
        exit;

    case "buscar_clientes_ajax":
        Route::protect("asistencia:ver");
        jsonOut($model->buscarClientes($_GET['termino'] ?? ''));

    case "verificar_entrada_reciente":
        Route::protect("asistencia:ver");

        $cedula = trim($_GET['cedula'] ?? '');
        if ($cedula === '') {
            jsonOut(['success' => false, 'message' => 'Cédula requerida.']);
        }

        $reciente = $model->tieneEntradaReciente($cedula);

        jsonOut($reciente
            ? [
                'success'       => true,
                'existe'        => true,
                'ultima_hora'   => $reciente['fecha'],
                'id_asistencia' => (int) $reciente['id_asistencia'],
            ]
            : ['success' => true, 'existe' => false]
        );

    case "historial_asistencias":
        Route::protect("asistencia:ver");

        $cedula      = trim($_GET['cedula'] ?? '');
        $termino     = trim($_GET['termino'] ?? '');
        $fechaInicio = !empty($_GET['inicio']) ? $_GET['inicio'] : null;
        $fechaFin    = !empty($_GET['fin'])    ? $_GET['fin']    : null;

        jsonOut([
            'success'     => true,
            'cedula'      => $cedula,
            'resumen'     => $model->obtenerResumenAsistencias($cedula !== '' ? $cedula : null),
            'asistencias' => $model->obtenerHistorialAsistencias(
                $cedula !== '' ? $cedula : null,
                $fechaInicio,
                $fechaFin,
                $termino !== '' ? $termino : null
            ),
        ]);

    case "registrar":
        Route::protect("asistencia:crear");

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            jsonOut(['success' => false, 'message' => 'Método no permitido'], 405);
        }

        $cedula = trim($_POST['cedula'] ?? '');
        $hora   = !empty($_POST['hora']) ? $_POST['hora'] : null;

        if ($cedula === '') {
            jsonOut(['success' => false, 'message' => 'Debe seleccionar un cliente.']);
        }

        $resultado = $model->registrarEntrada($cedula, $hora);

        if (!empty($resultado['success'])) {
            try {
                $bitacora->log("Entrada registrada para cliente '{$cedula}'", [
                    "modulo"        => "asistencia",
                    "accion"        => "registrar",
                    'cedula'        => $cedula,
                    'id_asistencia' => $resultado['id']    ?? null,
                    'fecha'         => $resultado['fecha'] ?? null,
                    'datos_nuevos'  => $resultado,
                ]);
            } catch (Throwable $e) {}
        }

        jsonOut($resultado);

    case "buscar_entradas_ajax":
        Route::protect("asistencia:ver");
        jsonOut($model->buscarEntradas($_GET['termino'] ?? ''));

    case "buscar_entradas_hoy":
        Route::protect("asistencia:ver");
        jsonOut($model->obtenerEntradasHoy());

    case "obtener_totales":
        Route::protect("asistencia:ver");
        jsonOut($model->obtenerTotalesPorRango(
            $_GET["inicio"] ?? null,
            $_GET["fin"]    ?? null
        ));

    case "editar":
        Route::protect("asistencia:editar");

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            jsonOut(['success' => false, 'message' => 'Método no permitido'], 405);
        }

        $id        = (int) ($_POST['id'] ?? 0);
        $nuevaHora = trim($_POST['hora'] ?? '');

        if ($id <= 0 || $nuevaHora === '') {
            jsonOut(['success' => false, 'message' => 'Datos inválidos.']);
        }

        $old = $model->findCliente($id);
        if (!$old) {
            jsonOut(['success' => false, 'message' => 'La entrada no existe.']);
        }

        $ok = $model->actualizarEntrada($id, $nuevaHora);

        if ($ok) {
            try {
                $bitacora->log("Entrada '{$id}' actualizada", [
                    "modulo"        => "asistencia",
                    "accion"        => "editar",
                    'id_asistencia' => $id,
                    'datos_previos' => $old,
                    'datos_nuevos'  => $model->findCliente($id),
                ]);
            } catch (Throwable $e) {}
        }

        jsonOut($ok
            ? ['success' => true]
            : ['success' => false, 'message' => 'No se pudo actualizar la hora.']
        );

    case "eliminar":
        Route::protect("asistencia:eliminar");

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            jsonOut(['success' => false, 'message' => 'Método no permitido'], 405);
        }

        $id = (int) ($_POST['id'] ?? 0);
        if ($id <= 0) {
            jsonOut(['success' => false, 'message' => 'ID inválido.']);
        }

        $old = $model->findCliente($id);
        if (!$old) {
            jsonOut(['success' => false, 'message' => 'La entrada no existe.']);
        }

        $ok = $model->eliminarEntrada($id);

        if ($ok) {
            try {
                $bitacora->log("Entrada '{$id}' eliminada", [
                    "modulo"        => "asistencia",
                    "accion"        => "eliminar",
                    'id_asistencia' => $id,
                    'datos_previos' => $old,
                ]);
            } catch (Throwable $e) {}
        }

        jsonOut($ok
            ? ['success' => true]
            : ['success' => false, 'message' => 'No se pudo eliminar la entrada.']
        );

    case "vistaAsistencia":
        Route::protect("clientes:ver");
        echo Route::render('reportes/asistencia');
        exit;

    case "generarReporte":
        Route::protect("asistencia:ver");

        $fechaInicio = $_GET['inicio'] ?? null;
        $fechaFin    = $_GET['fin']    ?? null;

        $asistenciasData = $model->obtenerAsistenciasParaReporte($fechaInicio, $fechaFin);

        $pdf = new ReporteAsistencia();
        $pdf->SetTitle('Reporte de Asistencias - SOFIT GYM');
        $pdf->SetAuthor('Sistema SOFIT GYM');

        $pdf->crearReporte($asistenciasData, $fechaInicio, $fechaFin);
        $pdf->Output('I', 'reporte_asistencias.pdf');
        exit;

    default:
        http_response_code(404);
        jsonOut(['success' => false, 'message' => 'Acción no reconocida.']);
}