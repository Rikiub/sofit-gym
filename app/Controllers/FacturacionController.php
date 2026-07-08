<?php

namespace App\Controllers;

use App\Controllers\Controller;
use App\Core\Http\Response;
use App\Core\Reportes\ReporteFinanciero;
use App\Models\FacturacionModel;
use Exception;

class FacturacionController extends Controller
{
    public function __construct(
        private FacturacionModel $model,
    ) {}

    public function index()
    {
        $this->protect("facturacion:ver");
        unset($_SESSION['mensaje'], $_SESSION['tipo_mensaje']);

        return $this->render('facturacion', [
            'clientes' => $this->model->obtenerClientesSimples(),
            'pagos' => $this->model->obtenerTodosPagos(),
            'activeTab' => $_GET['tab'] ?? 'tab-pagos',
            'mensaje' => $_SESSION['mensaje'] ?? '',
            'tipoMensaje' => $_SESSION['tipo_mensaje'] ?? '',
        ]);
    }

    public function registrar()
    {
        $this->protect("facturacion:crear");

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Response::redirect([
                'page' => 'facturacion',
                'action' => 'index',
                'tab' => 'tab-lista',
            ]);
        }

        $cedula = $_POST['cedula'] ?? '';
        $monto = floatval($_POST['monto'] ?? 0);
        $metodo = $_POST['metodo_pago'] ?? 'Efectivo';
        $comprobante = $_POST['comprobante_url'] ?? null;
        $planTipo = !empty($_POST['plan_tipo']) ? intval($_POST['plan_tipo']) : null;

        try {
            $res = $this->model->registrarPago($cedula, $monto, $metodo, $comprobante, $planTipo);
            $_SESSION['mensaje'] = '✅ ' . $res['mensaje'];
            $_SESSION['tipo_mensaje'] = 'success';

            $this->logger->info("Pago registrado para cliente '{cedula}'", [
                'cedula'        => $cedula,
                'id_pago'       => $res['id_pago'] ?? null,
                'monto'         => $monto,
                'metodo'        => $metodo,
                'nueva_fecha_fin' => $res['nueva_fecha_vencimiento'] ?? null,
                'datos_nuevos'  => $res,
            ]);
        } catch (Exception $e) {
            $_SESSION['mensaje'] = '❌ Error: ' . $e->getMessage();
            $_SESSION['tipo_mensaje'] = 'danger';
        }

        Response::redirect([
            'page' => 'facturacion',
            'action' => 'index',
            'tab' => 'tab-lista',
        ]);
    }

    public function editar()
    {
        $this->protect("facturacion:editar");

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Response::redirect([
                'page' => 'facturacion',
                'action' => 'index',
                'tab' => 'tab-lista',
            ]);
            exit;
        }

        $idPago = intval($_POST['id_pago']);
        $monto = floatval($_POST['monto']);
        $metodo = $_POST['metodo_pago'];
        $estado = $_POST['estado'];
        $fechaPago = $_POST['fecha_pago'];
        $fechaVencimiento = $_POST['fecha_vencimiento'];

        // Obtener los datos previos del pago
        $old = $this->obtenerPagoPorId($idPago);

        try {
            $success = $this->model->actualizarPago($idPago, $monto, $metodo, $estado, $fechaPago, $fechaVencimiento);
            if ($success) {
                $_SESSION['mensaje'] = '✅ Pago actualizado correctamente.';
                $_SESSION['tipo_mensaje'] = 'success';

                // Obtener los datos nuevos después de la actualización
                $new = $this->obtenerPagoPorId($idPago);
                $this->logger->info("Pago '{id_pago}' actualizado", [
                    'id_pago'       => $idPago,
                    'datos_previos' => $old,
                    'datos_nuevos'  => $new,
                ]);
            } else {
                $_SESSION['mensaje'] = '❌ No se pudo actualizar.';
                $_SESSION['tipo_mensaje'] = 'danger';
            }
        } catch (Exception $e) {
            $_SESSION['mensaje'] = '❌ Error: ' . $e->getMessage();
            $_SESSION['tipo_mensaje'] = 'danger';
        }

        Response::redirect([
            'page' => 'facturacion',
            'action' => 'index',
            'tab' => 'tab-lista',
        ]);
    }

    public function eliminar()
    {
        $this->protect("facturacion:eliminar");

        if (!isset($_GET['eliminar_pago'])) {
            Response::redirect([
                'page' => 'facturacion',
                'action' => 'index',
                'tab' => 'tab-lista',
            ]);
        }

        $idPago = intval($_GET['eliminar_pago']);
        $old = $this->obtenerPagoPorId($idPago);

        try {
            $success = $this->model->eliminarPago($idPago);
            if ($success) {
                $_SESSION['mensaje'] = '🗑️ Pago eliminado correctamente.';
                $_SESSION['tipo_mensaje'] = 'warning';

                $this->logger->info("Pago '{id_pago}' eliminado", [
                    'id_pago'       => $idPago,
                    'datos_previos' => $old,
                ]);
            } else {
                $_SESSION['mensaje'] = '❌ No se pudo eliminar.';
                $_SESSION['tipo_mensaje'] = 'danger';
            }
        } catch (Exception $e) {
            $_SESSION['mensaje'] = '❌ Error: ' . $e->getMessage();
            $_SESSION['tipo_mensaje'] = 'danger';
        }

        Response::redirect([
            'page' => 'facturacion',
            'action' => 'index',
            'tab' => 'tab-lista',
        ]);
    }

    public function resumen_semana(): string
    {
        $resultados = $this->model->obtenerResumenFinancieroSemanal();
        return $this->json($resultados);
    }

    public function buscar_ajax()
    {
        $this->protect("facturacion:ver");

        if (!isset($_GET['ajax']) || $_GET['ajax'] !== 'buscar_pagos') {
            return;
        }

        $termino = $_GET['termino'] ?? '';
        $resultados = $this->model->buscarPagos($termino);

        return $this->json($resultados);
    }

    public function ingresos_mensuales()
    {
        $this->protect("facturacion:ver");

        $ingresos = $this->model->obtenerIngresosMesActual();
        return $this->json($ingresos);
    }

    /**
     * Helper para obtener los datos de un pago por su ID.
     */
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

    /**
     * Genera el reporte financiero en PDF para los pagos registrados.
     * Soporta filtrado por mes y año a través de parámetros GET.
     */
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
