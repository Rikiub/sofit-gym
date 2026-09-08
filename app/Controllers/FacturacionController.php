<?php

namespace App\Controllers;

use App\Controllers\Controller;
use App\Core\Http\Response;
use App\Models\FacturacionModel;
use App\Models\BitacoraModel;
use App\Services\Reportes\ReporteFinanciero;
use Exception;

class FacturacionController extends Controller
{
    public function __construct(
        private $logger = new BitacoraModel(),
        private $model = new FacturacionModel(),
    ) {}

    public function index()
    {
        $this->protect("facturacion:ver");

        // Recuperar mensajes de sesión y luego limpiarlos
        $mensaje = $_SESSION['mensaje'] ?? '';
        $tipoMensaje = $_SESSION['tipo_mensaje'] ?? '';
        unset($_SESSION['mensaje'], $_SESSION['tipo_mensaje']);

        try {
            $pagos = $this->model->obtenerTodosPagos();
            $clientes = $this->model->obtenerClientesSimples();
            $tiposMembresia = $this->model->obtenerTiposMembresia();
        } catch (Exception $e) {
            $pagos = [];
            $clientes = [];
            $tiposMembresia = [];
            $mensaje = '❌ Error al cargar datos: ' . $e->getMessage();
            $tipoMensaje = 'danger';
        }

        return $this->render('facturacion', [
            'clientes' => $clientes,
            'pagos' => $pagos,
            'tiposMembresia' => $tiposMembresia,
            'activeTab' => $_GET['tab'] ?? 'tab-pagos',
            'mensaje' => $mensaje,
            'tipoMensaje' => $tipoMensaje,
        ]);
    }

    public function registrar()
    {
        $this->protect("facturacion:crear");

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
            $res = $this->model->registrarPago($cedula, $monto, $metodo, $planTipo);
            $_SESSION['mensaje'] = '✅ ' . $res['mensaje'];
            $_SESSION['tipo_mensaje'] = 'success';

            $this->logger->log("Pago registrado", [
                'modulo' => 'facturacion',
                'accion' => 'crear',
                'cedula' => $cedula,
                'id_pago' => $res['id_pago'] ?? null,
                'monto' => $monto,
                'metodo' => $metodo,
                'nueva_fecha_fin' => $res['nueva_fecha_vencimiento'] ?? null,
            ]);
        } catch (Exception $e) {
            $_SESSION['mensaje'] = '❌ Error al registrar: ' . $e->getMessage();
            $_SESSION['tipo_mensaje'] = 'danger';
        }

        Response::redirect(['page' => 'facturacion']);
    }

    public function editar()
    {
        $this->protect("facturacion:editar");

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

        $old = $this->obtenerPagoPorId($idPago);

        try {
            $success = $this->model->actualizarPago($idPago, $monto, $metodo, $estado, $fechaPago, $fechaVencimiento);
            if ($success) {
                $_SESSION['mensaje'] = '✅ Pago actualizado correctamente.';
                $_SESSION['tipo_mensaje'] = 'success';
                $new = $this->obtenerPagoPorId($idPago);
                $this->logger->log("Pago actualizado", [
                    'modulo' => 'facturacion',
                    'accion' => 'editar',
                    'id_pago' => $idPago,
                    'datos_previos' => $old,
                    'datos_nuevos' => $new,
                ]);
            } else {
                $_SESSION['mensaje'] = '❌ No se pudo actualizar el pago.';
                $_SESSION['tipo_mensaje'] = 'danger';
            }
        } catch (Exception $e) {
            $_SESSION['mensaje'] = '❌ Error: ' . $e->getMessage();
            $_SESSION['tipo_mensaje'] = 'danger';
        }

        Response::redirect(['page' => 'facturacion']);
    }

    public function eliminar()
    {
        $this->protect("facturacion:eliminar");

        if (!isset($_GET['eliminar_pago'])) {
            Response::redirect(['page' => 'facturacion']);
            return;
        }

        $idPago = intval($_GET['eliminar_pago']);
        $old = $this->obtenerPagoPorId($idPago);

        try {
            $success = $this->model->eliminarPago($idPago);
            if ($success) {
                $_SESSION['mensaje'] = '🗑️ Pago eliminado correctamente.';
                $_SESSION['tipo_mensaje'] = 'warning';
                $this->logger->log("Pago eliminado", [
                    'modulo' => 'facturacion',
                    'accion' => 'eliminar',
                    'id_pago' => $idPago,
                    'datos_previos' => $old,
                ]);
            } else {
                $_SESSION['mensaje'] = '❌ No se pudo eliminar el pago.';
                $_SESSION['tipo_mensaje'] = 'danger';
            }
        } catch (Exception $e) {
            $_SESSION['mensaje'] = '❌ Error: ' . $e->getMessage();
            $_SESSION['tipo_mensaje'] = 'danger';
        }

        Response::redirect(['page' => 'facturacion']);
    }

    public function buscar_ajax()
    {
        $this->protect("facturacion:ver");

        if (!isset($_GET['ajax']) || $_GET['ajax'] !== 'buscar_pagos') {
            return;
        }

        $termino = $_GET['termino'] ?? '';
        $resultados = $this->model->buscarPagos($termino);
        return Response::json($resultados);
    }

    public function ingresos_mensuales()
    {
        $this->protect("facturacion:ver");
        $ingresos = $this->model->obtenerIngresosMesActual();
        return Response::json($ingresos);
    }

    public function resumen_semana(): string
    {
        $resultados = $this->model->obtenerResumenFinancieroSemanal();
        return Response::json($resultados);
    }

    private function obtenerPagoPorId(int $idPago): ?array
    {
        $resultados = $this->model->buscarPagos((string)$idPago);
        foreach ($resultados as $pago) {
            if ((int)$pago['id_pago'] === $idPago) {
                return $pago;
            }
        }
        return null;
    }

    // REPORTES
    public function reporteVista()
    {
        $this->protect("facturacion:ver");
        return $this->render('reportes/facturacion');
    }

    public function reporte()
    {
        $this->protect("facturacion:ver");

        $mes = $_GET['mes'] ?? null;
        $anio = $_GET['anio'] ?? null;

        if (empty($mes) && empty($anio)) {
            $mes = date('m');
            $anio = date('Y');
        }

        $pagosData = $this->model->obtenerPagosPorPeriodo($mes, $anio);

        $pdf = new ReporteFinanciero();
        $pdf->SetTitle(utf8_decode('Reporte Financiero - SOFIT GYM'));
        $pdf->SetAuthor('Sistema SOFIT GYM');

        $tipoReporte = (!empty($mes)) ? 'MENSUAL' : 'ANUAL';
        $pdf->setPeriodo($mes, $anio, $tipoReporte);
        $pdf->generar($pagosData);

        $nombreArchivo = 'reporte_financiero_' . ($mes ? $mes . '_' : '') . $anio . '.pdf';
        $pdf->Output('I', $nombreArchivo);
    }
}
