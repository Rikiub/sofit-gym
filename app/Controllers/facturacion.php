<?php

namespace App\Controllers;

use App\Core\Route;
use App\Core\Http\Response;
use App\Models\FacturacionModel;
use App\Models\BitacoraModel;
use App\Services\Reportes\ReporteFinanciero;
use Throwable;

$bitacora = new BitacoraModel();
$model    = new FacturacionModel();

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

function obtenerPagoPorId(int $idPago): ?array
{
    global $model;
    if ($idPago <= 0) return null;

    $resultados = $model->buscarPagos((string) $idPago);
    foreach ($resultados as $pago) {
        if ((int) $pago['id_pago'] === $idPago) {
            return $pago;
        }
    }
    return null;
}

$action = Route::action();
if ($action !== 'index' && $action !== 'reporteVista') {
    ini_set('display_errors', '0');
    ini_set('display_startup_errors', '0');
    error_reporting(E_ALL);
    if (ob_get_level() === 0) {
        ob_start();
    }
}

switch ($action) {

    case "index":
        Route::protect("facturacion:ver");

        $mensaje     = $_SESSION['mensaje'] ?? '';
        $tipoMensaje = $_SESSION['tipo_mensaje'] ?? '';
        unset($_SESSION['mensaje'], $_SESSION['tipo_mensaje']);

        try {
            $pagos          = $model->obtenerTodosPagos();
            $clientes       = $model->obtenerClientesSimples();
            $tiposMembresia = $model->obtenerTiposMembresia();
        } catch (Throwable $e) {
            $pagos = [];
            $clientes = [];
            $tiposMembresia = [];
            $mensaje = '❌ Error al cargar datos: ' . $e->getMessage();
            $tipoMensaje = 'danger';
        }

        echo Route::render('facturacion', [
            'clientes'       => $clientes,
            'pagos'          => $pagos,
            'tiposMembresia' => $tiposMembresia,
            'activeTab'      => $_GET['tab'] ?? 'tab-pagos',
            'mensaje'        => $mensaje,
            'tipoMensaje'    => $tipoMensaje,
        ]);
        exit;

    case "registrar":
        Route::protect("facturacion:crear");

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['mensaje'] = '❌ Método no permitido.';
            $_SESSION['tipo_mensaje'] = 'danger';
            Response::redirect(['page' => 'facturacion']);
            return;
        }

        $cedula   = trim($_POST['cedula'] ?? '');
        $monto    = floatval($_POST['monto'] ?? 0);
        $metodo   = $_POST['metodo_pago'] ?? 'Efectivo';
        $planTipo = !empty($_POST['plan_tipo']) ? intval($_POST['plan_tipo']) : null;

        if ($cedula === '') {
            $_SESSION['mensaje'] = '❌ Debe seleccionar un cliente.';
            $_SESSION['tipo_mensaje'] = 'danger';
            Response::redirect(['page' => 'facturacion']);
            return;
        }

        if ($monto <= 0) {
            $_SESSION['mensaje'] = '❌ El monto debe ser mayor a 0.';
            $_SESSION['tipo_mensaje'] = 'danger';
            Response::redirect(['page' => 'facturacion']);
            return;
        }

        try {
            $res = $model->registrarPago($cedula, $monto, $metodo, $planTipo);
            $_SESSION['mensaje'] = '✅ ' . $res['mensaje'];
            $_SESSION['tipo_mensaje'] = 'success';

            $bitacora->log("Pago registrado", [
                'modulo'          => 'facturacion',
                'accion'          => 'crear',
                'cedula'          => $cedula,
                'id_pago'         => $res['id_pago'] ?? null,
                'monto'           => $monto,
                'metodo'          => $metodo,
                'estado_cliente'  => $res['estado_cliente'] ?? null,
                'nueva_fecha_fin' => $res['nueva_fecha_vencimiento'] ?? null,
            ]);
        } catch (Throwable $e) {
            $_SESSION['mensaje'] = '❌ Error al registrar: ' . $e->getMessage();
            $_SESSION['tipo_mensaje'] = 'danger';
        }

        Response::redirect(['page' => 'facturacion']);
        exit;

    case "editar":
        Route::protect("facturacion:editar");

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Response::redirect(['page' => 'facturacion']);
            exit;
        }

        $idPago           = (int) ($_POST['id_pago'] ?? 0);
        $monto            = floatval($_POST['monto'] ?? 0);
        $metodo           = $_POST['metodo_pago'] ?? 'Efectivo';
        $estado           = $_POST['estado'] ?? 'Pagado';
        $fechaPago        = $_POST['fecha_pago'] ?? date('Y-m-d');
        $fechaVencimiento = $_POST['fecha_vencimiento'] ?? date('Y-m-d');

        if ($idPago <= 0 || $monto <= 0) {
            $_SESSION['mensaje'] = '❌ Datos inválidos.';
            $_SESSION['tipo_mensaje'] = 'danger';
            Response::redirect(['page' => 'facturacion']);
            exit;
        }

        $old = obtenerPagoPorId($idPago);

        try {
            $success = $model->actualizarPago($idPago, $monto, $metodo, $estado, $fechaPago, $fechaVencimiento);

            if ($success) {
                $_SESSION['mensaje'] = '✅ Pago actualizado correctamente.';
                $_SESSION['tipo_mensaje'] = 'success';

                $bitacora->log("Pago actualizado", [
                    'modulo'        => 'facturacion',
                    'accion'        => 'editar',
                    'id_pago'       => $idPago,
                    'datos_previos' => $old,
                    'datos_nuevos'  => obtenerPagoPorId($idPago),
                ]);
            } else {
                $_SESSION['mensaje'] = '❌ No se pudo actualizar el pago.';
                $_SESSION['tipo_mensaje'] = 'danger';
            }
        } catch (Throwable $e) {
            $_SESSION['mensaje'] = '❌ Error: ' . $e->getMessage();
            $_SESSION['tipo_mensaje'] = 'danger';
        }

        Response::redirect(['page' => 'facturacion']);
        exit;

    case "eliminar":
        Route::protect("facturacion:eliminar");

        $idPago = (int) ($_GET['eliminar_pago'] ?? 0);
        if ($idPago <= 0) {
            Response::redirect(['page' => 'facturacion']);
            exit;
        }

        $old = obtenerPagoPorId($idPago);

        try {
            $success = $model->eliminarPago($idPago);

            if ($success) {
                $_SESSION['mensaje'] = '🗑️ Pago eliminado correctamente.';
                $_SESSION['tipo_mensaje'] = 'warning';

                $bitacora->log("Pago eliminado", [
                    'modulo'        => 'facturacion',
                    'accion'        => 'eliminar',
                    'id_pago'       => $idPago,
                    'datos_previos' => $old,
                ]);
            } else {
                $_SESSION['mensaje'] = '❌ No se pudo eliminar el pago.';
                $_SESSION['tipo_mensaje'] = 'danger';
            }
        } catch (Throwable $e) {
            $_SESSION['mensaje'] = '❌ Error: ' . $e->getMessage();
            $_SESSION['tipo_mensaje'] = 'danger';
        }

        Response::redirect(['page' => 'facturacion']);
        exit;

    case "buscar_ajax":
        Route::protect("facturacion:ver");
        jsonOut($model->buscarPagos($_GET['termino'] ?? ''));

    case "ingresos_mensuales":
        Route::protect("facturacion:ver");
        jsonOut($model->obtenerIngresosMesActual());

    case "resumen_semana":
        Route::protect("facturacion:ver");
        jsonOut($model->obtenerResumenFinancieroSemanal());

    case "estado_clientes":
        Route::protect("facturacion:ver");

        $termino    = trim($_GET['termino'] ?? '');
        $fechaDesde = !empty($_GET['desde']) ? $_GET['desde'] : null;
        $fechaHasta = !empty($_GET['hasta']) ? $_GET['hasta'] : null;

        $data = $model->obtenerEstadoClientes(
            $termino !== '' ? $termino : null,
            $fechaDesde,
            $fechaHasta
        );

        jsonOut([
            'success'  => true,
            'conteos'  => $data['conteos'],
            'clientes' => $data['clientes'],
        ]);

    case "marcar_moroso":
        Route::protect("facturacion:editar");

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            jsonOut(['success' => false, 'message' => 'Método no permitido'], 405);
        }

        $cedula = trim($_POST['cedula'] ?? '');
        if ($cedula === '') {
            jsonOut(['success' => false, 'message' => 'Cédula requerida.']);
        }

        $ok = $model->marcarClienteMoroso($cedula);

        if ($ok) {
            try {
                $bitacora->log("Cliente marcado como moroso", [
                    'modulo' => 'facturacion',
                    'accion' => 'marcar_moroso',
                    'cedula' => $cedula,
                ]);
            } catch (Throwable $e) {}
        }

        jsonOut($ok
            ? ['success' => true, 'message' => 'Cliente marcado como moroso.']
            : ['success' => false, 'message' => 'No se pudo marcar como moroso.']
        );

    case "reactivar_cliente":
        Route::protect("facturacion:editar");

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            jsonOut(['success' => false, 'message' => 'Método no permitido'], 405);
        }

        $cedula = trim($_POST['cedula'] ?? '');
        if ($cedula === '') {
            jsonOut(['success' => false, 'message' => 'Cédula requerida.']);
        }

        $ok = $model->reactivarCliente($cedula);

        if ($ok) {
            try {
                $bitacora->log("Cliente reactivado", [
                    'modulo' => 'facturacion',
                    'accion' => 'reactivar_cliente',
                    'cedula' => $cedula,
                ]);
            } catch (Throwable $e) {}
        }

        jsonOut($ok
            ? ['success' => true, 'message' => 'Cliente reactivado correctamente.']
            : ['success' => false, 'message' => 'No se pudo reactivar.']
        );

    case "reporteVista":
        Route::protect("facturacion:ver");
        echo Route::render('reportes/facturacion');
        exit;

    case "reporte":
        Route::protect("facturacion:ver");

        $mes  = $_GET['mes']  ?? null;
        $anio = $_GET['anio'] ?? null;

        if (empty($mes) && empty($anio)) {
            $mes  = date('m');
            $anio = date('Y');
        }

        $pagosData = $model->obtenerPagosPorPeriodo($mes, $anio);

        $pdf = new ReporteFinanciero();
        $pdf->SetTitle('Reporte Financiero - SOFIT GYM');
        $pdf->SetAuthor('Sistema SOFIT GYM');

        $tipoReporte = (!empty($mes)) ? 'MENSUAL' : 'ANUAL';
        $pdf->setPeriodo($mes, $anio, $tipoReporte);
        $pdf->generar($pagosData);

        $nombreArchivo = 'reporte_financiero_' . ($mes ? $mes . '_' : '') . $anio . '.pdf';
        $pdf->Output('I', $nombreArchivo);
        exit;

    default:
        http_response_code(404);
        jsonOut(['success' => false, 'message' => 'Acción no reconocida.']);
}