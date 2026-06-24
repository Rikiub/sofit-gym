<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Helpers\Reportes\reporteFinanciero;
use App\Helpers\Response;
use App\Models\FacturacionPagosModel;
use Exception;

class FacturacionController extends BaseController
{
    public function __construct(
        private Response $response,
        private FacturacionPagosModel $model,
    ) {}

    public function index()
    {
        $this->protect("facturacion:ver");
        unset($_SESSION['mensaje'], $_SESSION['tipo_mensaje']);

        return $this->templates->render('facturacion', [
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
            $this->response->redirect([
                'page' => 'facturacion',
                'action' => 'index',
                'tab' => 'tab-lista',
            ]);
            exit;
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
        } catch (Exception $e) {
            $_SESSION['mensaje'] = '❌ Error: ' . $e->getMessage();
            $_SESSION['tipo_mensaje'] = 'danger';
        }

        $this->response->redirect([
            'page' => 'facturacion',
            'action' => 'index',
            'tab' => 'tab-lista',
        ]);
    }

    public function editar()
    {
        $this->protect("facturacion:editar");

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->response->redirect([
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

        try {
            if ($this->model->actualizarPago($idPago, $monto, $metodo, $estado, $fechaPago, $fechaVencimiento)) {
                $_SESSION['mensaje'] = '✅ Pago actualizado correctamente.';
                $_SESSION['tipo_mensaje'] = 'success';
            } else {
                $_SESSION['mensaje'] = '❌ No se pudo actualizar.';
                $_SESSION['tipo_mensaje'] = 'danger';
            }
        } catch (Exception $e) {
            $_SESSION['mensaje'] = '❌ Error: ' . $e->getMessage();
            $_SESSION['tipo_mensaje'] = 'danger';
        }

        $this->response->redirect([
            'page' => 'facturacion',
            'action' => 'index',
            'tab' => 'tab-lista',
        ]);
    }

    public function eliminar()
    {
        $this->protect("facturacion:eliminar");

        if (!isset($_GET['eliminar_pago'])) {
            $this->response->redirect([
                'page' => 'facturacion',
                'action' => 'index',
                'tab' => 'tab-lista',
            ]);
            exit;
        }

        $idPago = intval($_GET['eliminar_pago']);
        try {
            if ($this->model->eliminarPago($idPago)) {
                $_SESSION['mensaje'] = '🗑️ Pago eliminado correctamente.';
                $_SESSION['tipo_mensaje'] = 'warning';
            } else {
                $_SESSION['mensaje'] = '❌ No se pudo eliminar.';
                $_SESSION['tipo_mensaje'] = 'danger';
            }
        } catch (Exception $e) {
            $_SESSION['mensaje'] = '❌ Error: ' . $e->getMessage();
            $_SESSION['tipo_mensaje'] = 'danger';
        }

        $this->response->redirect([
            'page' => 'facturacion',
            'action' => 'index',
            'tab' => 'tab-lista',
        ]);
    }

    public function buscar_ajax()
    {
        $this->protect("facturacion:ver");

        if (!isset($_GET['ajax']) || $_GET['ajax'] !== 'buscar_pagos') {
            return;
        }

        $termino = $_GET['termino'] ?? '';
        $resultados = $this->model->buscarPagos($termino);

        return $this->response->json($resultados);
    }

    public function ingresos_mensuales()
    {
        $this->protect("facturacion:ver");

        $ingresos = $this->model->obtenerIngresosMesActual();
        return $this->response->json($ingresos);
    }

    // REPORTES
    public function reporteVista()
    {
        $this->protect("facturacion:ver");
        return $this->templates->render('reportes/facturacion');
    }

    /**
     * Genera el reporte financiero en PDF para los pagos registrados.
     * Soporta filtrado por mes y año a través de parámetros GET.
     */
    public function reporte()
    {
        $this->protect("facturacion:ver");

        // Capturar los parámetros de filtro del reporte desde la URL
        $mes = $_GET['mes'] ?? null;
        $anio = $_GET['anio'] ?? null;

        // Si ambos parámetros están vacíos, por defecto generamos el reporte del mes y año actual
        if (empty($mes) && empty($anio)) {
            $mes = date('m');
            $anio = date('Y');
        }

        // 1. Obtener la data filtrada desde el modelo de pagos
        $pagosData = $this->model->obtenerPagosPorPeriodo($mes, $anio);

        // Instanciar el helper del reporte financiero FPDF
        $pdf = new reporteFinanciero();

        // Establecer metadatos básicos del documento PDF
        $pdf->SetTitle(utf8_decode('Reporte Financiero - SOFIT GYM'));
        $pdf->SetAuthor('Sistema SOFIT GYM');

        // Determinar si el reporte es mensual o anual para la cabecera
        $tipoReporte = (!empty($mes)) ? 'MENSUAL' : 'ANUAL';
        $pdf->setPeriodo($mes, $anio, $tipoReporte);

        // Invocar el renderizado de la tabla con los pagos correspondientes
        $pdf->generar($pagosData);

        // Enviar los headers HTTP correspondientes e imprimir el flujo binario del PDF en el navegador
        // I: Envía el fichero al navegador de forma limpia para previsualización / descarga
        $nombreArchivo = 'reporte_financiero_' . ($mes ? $mes . '_' : '') . $anio . '.pdf';
        $pdf->Output('I', $nombreArchivo);
    }
}
