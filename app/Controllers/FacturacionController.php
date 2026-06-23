<?php

namespace App\Controllers;

use App\Controllers\BaseController;
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
        if (!isset($_GET['ajax']) || $_GET['ajax'] !== 'buscar_pagos') {
            return;
        }

        $termino = $_GET['termino'] ?? '';
        $resultados = $this->model->buscarPagos($termino);

        return $this->response->json($resultados);
    }

    public function ingresos_mensuales()
    {
        $ingresos = $this->model->obtenerIngresosMesActual();
        return $this->response->json($ingresos);
    }
}
