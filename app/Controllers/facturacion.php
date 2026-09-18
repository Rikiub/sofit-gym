<?php

namespace App\Controllers;

use App\Core\Route;
use App\Core\Http\Response;
use App\Models\FacturacionModel;
use App\Services\Reportes\ReporteFinanciero;
use Exception;

$model = new FacturacionModel();

function obtenerPagoPorId(int $idPago): ?array
{
    global $model;
    $resultados = $model->buscarPagos((string)$idPago);
    foreach ($resultados as $pago) {
        if ((int)$pago['id_pago'] === $idPago) {
            return $pago;
        }
    }
    return null;
}

switch (Route::action()) {
    case "index":
        Route::protect("facturacion:ver");

        // Recuperar mensajes de sesión y luego limpiarlos
        $mensaje = $_SESSION['mensaje'] ?? '';
        $tipoMensaje = $_SESSION['tipo_mensaje'] ?? '';
        unset($_SESSION['mensaje'], $_SESSION['tipo_mensaje']);

        try {
            $pagos = $model->obtenerTodosPagos();
            $clientes = $model->obtenerClientesSimples();
            $tiposMembresia = $model->obtenerTiposMembresia();
        } catch (Exception $e) {
            $pagos = [];
            $clientes = [];
            $tiposMembresia = [];
            $mensaje = '❌ Error al cargar datos: ' . $e->getMessage();
            $tipoMensaje = 'danger';
        }

        return Route::render('facturacion', [
            'clientes' => $clientes,
            'pagos' => $pagos,
            'tiposMembresia' => $tiposMembresia,
            'activeTab' => $_GET['tab'] ?? 'tab-pagos',
            'mensaje' => $mensaje,
            'tipoMensaje' => $tipoMensaje,
        ]);

    case "registrar":
        Route::protect("facturacion:crear");

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['mensaje'] = '❌ Método no permitido.';
            $_SESSION['tipo_mensaje'] = 'danger';
            Response::redirect(['page' => 'facturacion']);
            return;
        }

        $cedula = $_POST['cedula'] ?? '';
        $monto = floatval($_POST['monto'] ?? 0);
        $metodo = $_POST['metodo_pago'] ?? 'Efectivo';
        $planTipo = !empty($_POST['plan_tipo']) ? intval($_POST['plan_tipo']) : null;

        if (empty($cedula)) {
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
        } catch (Exception $e) {
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

        $idPago = intval($_POST['id_pago']);
        $monto = floatval($_POST['monto']);
        $metodo = $_POST['metodo_pago'];
        $estado = $_POST['estado'];
        $fechaPago = $_POST['fecha_pago'];
        $fechaVencimiento = $_POST['fecha_vencimiento'];

        $old = obtenerPagoPorId($idPago);

        try {
            $success = $model->actualizarPago($idPago, $monto, $metodo, $estado, $fechaPago, $fechaVencimiento);
            if ($success) {
                $_SESSION['mensaje'] = '✅ Pago actualizado correctamente.';
                $_SESSION['tipo_mensaje'] = 'success';
                $new = obtenerPagoPorId($idPago);
            } else {
                $_SESSION['mensaje'] = '❌ No se pudo actualizar el pago.';
                $_SESSION['tipo_mensaje'] = 'danger';
            }
        } catch (Exception $e) {
            $_SESSION['mensaje'] = '❌ Error: ' . $e->getMessage();
            $_SESSION['tipo_mensaje'] = 'danger';
        }

        Response::redirect(['page' => 'facturacion']);
        exit;

    case "eliminar":
        Route::protect("facturacion:eliminar");

        if (!isset($_GET['eliminar_pago'])) {
            Response::redirect(['page' => 'facturacion']);
            return;
        }

        $idPago = intval($_GET['eliminar_pago']);
        $old = obtenerPagoPorId($idPago);

        try {
            $success = $model->eliminarPago($idPago);
            if ($success) {
                $_SESSION['mensaje'] = '🗑️ Pago eliminado correctamente.';
                $_SESSION['tipo_mensaje'] = 'warning';
            } else {
                $_SESSION['mensaje'] = '❌ No se pudo eliminar el pago.';
                $_SESSION['tipo_mensaje'] = 'danger';
            }
        } catch (Exception $e) {
            $_SESSION['mensaje'] = '❌ Error: ' . $e->getMessage();
            $_SESSION['tipo_mensaje'] = 'danger';
        }

        Response::redirect(['page' => 'facturacion']);
        exit;

    case "buscar_ajax":
        Route::protect("facturacion:ver");

        if (!isset($_GET['ajax']) || $_GET['ajax'] !== 'buscar_pagos') {
            return;
        }

        $termino = $_GET['termino'] ?? '';
        $resultados = $model->buscarPagos($termino);
        return Response::json($resultados);

    case "ingresos_mensuales":
        Route::protect("facturacion:ver");
        $ingresos = $model->obtenerIngresosMesActual();
        return Response::json($ingresos);

    case "resumen_semana":
        $resultados = $model->obtenerResumenFinancieroSemanal();
        return Response::json($resultados);

        // REPORTES
    case "reporteVista":
        Route::protect("facturacion:ver");
        return Route::render('reportes/facturacion');

    case "reporte":
        Route::protect("facturacion:ver");

        $mes = $_GET['mes'] ?? null;
        $anio = $_GET['anio'] ?? null;

        if (empty($mes) && empty($anio)) {
            $mes = date('m');
            $anio = date('Y');
        }

        $pagosData = $model->obtenerPagosPorPeriodo($mes, $anio);

        $pdf = new ReporteFinanciero();
        $pdf->SetTitle(utf8_decode('Reporte Financiero - SOFIT GYM'));
        $pdf->SetAuthor('Sistema SOFIT GYM');

        $tipoReporte = (!empty($mes)) ? 'MENSUAL' : 'ANUAL';
        $pdf->setPeriodo($mes, $anio, $tipoReporte);
        $pdf->generar($pagosData);

        $nombreArchivo = 'reporte_financiero_' . ($mes ? $mes . '_' : '') . $anio . '.pdf';
        $pdf->Output('I', $nombreArchivo);
        exit;
}
