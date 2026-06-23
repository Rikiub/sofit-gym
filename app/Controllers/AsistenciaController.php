<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\AsistenciaModel;

class AsistenciaController extends BaseController
{
    public function __construct(
        private AsistenciaModel $model
    ) {}

    public function index()
    {
        $this->protect("asistencia:ver");

        $fechaSeleccionada = $_GET['fecha'] ?? date('Y-m-d');
        unset($_SESSION['mensaje'], $_SESSION['tipo_mensaje']);

        return $this->templates->render('asistencia', [
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
        $ok = $this->model->actualizarEntrada($id, $nuevaHora);
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
        $ok = $this->model->eliminarEntrada($id);
        echo json_encode(['success' => $ok]);
    }
}
