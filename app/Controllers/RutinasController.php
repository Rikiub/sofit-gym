<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\RutinasModel;

class RutinasController extends BaseController
{
    public function __construct(
        private RutinasModel $model
    ) {}

    /**
     * Vista principal: Gestión de Rutinas Base
     * Acceso: ?page=rutinas
     */
    public function index()
    {
        $this->protect("rutinas:ver");

        // Limpiamos mensajes de sesión previos
        unset($_SESSION['mensaje'], $_SESSION['tipo_mensaje']);

        return $this->templates->render('rutinas', [
            'rutinas' => $this->model->obtenerTodasLasRutinas(),
            'dificultades' => $this->model->obtenerDificultades(),
            'mensaje' => $_SESSION['mensaje'] ?? '',
            'tipoMensaje' => $_SESSION['tipo_mensaje'] ?? '',
        ]);
    }

    /**
     * Vista secundaria: Asignación de Rutinas a Clientes
     * Acceso: ?page=rutinas&action=asignadas
     */
    public function asignadas()
    {
        $this->protect("rutinas:ver");

        // Limpiamos mensajes de sesión previos
        unset($_SESSION['mensaje'], $_SESSION['tipo_mensaje']);

        // Renderizamos la vista 'rutinasAsignadas' enviándole las asignaciones y las rutinas bases cargadas
        return $this->templates->render('rutinas_asignadas', [
            'asignaciones' => $this->model->obtenerTodasLasAsignaciones(),
            'rutinas' => $this->model->obtenerTodasLasRutinas(),
            'mensaje' => $_SESSION['mensaje'] ?? '',
            'tipoMensaje' => $_SESSION['tipo_mensaje'] ?? '',
        ]);
    }

    // =========================================================================
    // CRUD AJAX: TABLA `rutina`
    // =========================================================================

    /**
     * Buscar rutinas por coincidencia de término (AJAX)
     */
    public function buscar_rutinas_ajax()
    {
        $this->protect("rutinas:ver");

        if (!isset($_GET['ajax']) || $_GET['ajax'] !== 'buscar_rutinas')
            return;
        $termino = $_GET['termino'] ?? '';
        $resultados = $this->model->buscarRutinas($termino);
        header('Content-Type: application/json');
        echo json_encode($resultados);
        exit;
    }

    /**
     * Registrar una nueva rutina (AJAX - POST)
     */
    public function registrar_rutina()
    {
        $this->protect("rutinas:ver");

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Método no permitido']);
            return;
        }

        $idDificultad = intval($_POST['id_dificultad'] ?? 0);
        $nombre = trim($_POST['nombre'] ?? '');
        $descripcion = trim($_POST['descripcion'] ?? '');
        $objetivo = trim($_POST['objetivo'] ?? '');
        $duracionSemanas = !empty($_POST['duracion_semanas']) ? intval($_POST['duracion_semanas']) : null;

        if (empty($nombre) || $idDificultad <= 0) {
            echo json_encode(['success' => false, 'message' => 'El nombre y la dificultad son obligatorios.']);
            return;
        }

        $datos = [
            'id_dificultad' => $idDificultad,
            'nombre' => $nombre,
            'descripcion' => $descripcion,
            'objetivo' => $objetivo,
            'duracion_semanas' => $duracionSemanas
        ];

        $ok = $this->model->crearRutina($datos);
        echo json_encode(['success' => $ok, 'message' => $ok ? 'Rutina creada correctamente.' : 'Error al registrar rutina en la base de datos.']);
    }

    /**
     * Editar una rutina existente (AJAX - POST)
     */
    public function editar_rutina()
    {
        $this->protect("rutinas:editar");

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Método no permitido']);
            return;
        }

        $id = intval($_POST['id_rutina'] ?? 0);
        if ($id <= 0) {
            echo json_encode(['success' => false, 'message' => 'ID de rutina inválido.']);
            return;
        }

        $datos = [];
        if (isset($_POST['id_dificultad']))
            $datos['id_dificultad'] = intval($_POST['id_dificultad']);
        if (isset($_POST['nombre']))
            $datos['nombre'] = trim($_POST['nombre']);
        if (isset($_POST['descripcion']))
            $datos['descripcion'] = trim($_POST['descripcion']);
        if (isset($_POST['objetivo']))
            $datos['objetivo'] = trim($_POST['objetivo']);
        if (isset($_POST['duracion_semanas'])) {
            $datos['duracion_semanas'] = !empty($_POST['duracion_semanas']) ? intval($_POST['duracion_semanas']) : null;
        }

        if (isset($datos['nombre']) && empty($datos['nombre'])) {
            echo json_encode(['success' => false, 'message' => 'El nombre de la rutina no puede estar vacío.']);
            return;
        }

        $ok = $this->model->actualizarRutina($id, $datos);
        echo json_encode(['success' => $ok, 'message' => $ok ? 'Rutina actualizada correctamente.' : 'No se realizaron cambios o error al actualizar.']);
    }

    /**
     * Eliminar una rutina (AJAX - POST)
     */
    public function eliminar_rutina()
    {
        $this->protect("rutinas:eliminar");

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Método no permitido']);
            return;
        }

        $id = intval($_POST['id_rutina'] ?? 0);
        if ($id <= 0) {
            echo json_encode(['success' => false, 'message' => 'ID de rutina inválido.']);
            return;
        }

        $ok = $this->model->eliminarRutina($id);
        echo json_encode(['success' => $ok, 'message' => $ok ? 'Rutina eliminada correctamente.' : 'Error al eliminar. Verifique que no esté asignada a un cliente.']);
    }

    // =========================================================================
    // CRUD AJAX: TABLA `rutina_asignada`
    // =========================================================================

    /**
     * Asignar rutina a un cliente (AJAX - POST)
     */
    public function asignar_rutina()
    {
        $this->protect("rutinas:crear");

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Método no permitido']);
            return;
        }

        $cedula = trim($_POST['cedula_cliente'] ?? '');
        $idRutina = intval($_POST['id_rutina'] ?? 0);
        $fechaAsignacion = !empty($_POST['fecha_asignacion']) ? $_POST['fecha_asignacion'] : date('Y-m-d');
        $fechaInicio = !empty($_POST['fecha_inicio']) ? $_POST['fecha_inicio'] : null;
        $fechaFin = !empty($_POST['fecha_fin']) ? $_POST['fecha_fin'] : null;
        $estado = $_POST['estado'] ?? 'Activa';
        $progreso = floatval($_POST['progreso'] ?? 0.0);

        if (empty($cedula) || $idRutina <= 0) {
            echo json_encode(['success' => false, 'message' => 'Debe seleccionar un cliente y una rutina.']);
            return;
        }

        $datos = [
            'cedula_cliente' => $cedula,
            'id_rutina' => $idRutina,
            'fecha_asignacion' => $fechaAsignacion,
            'fecha_inicio' => $fechaInicio,
            'fecha_fin' => $fechaFin,
            'estado' => $estado,
            'progreso' => $progreso
        ];

        $ok = $this->model->asignarRutina($datos);
        echo json_encode(['success' => $ok, 'message' => $ok ? 'Rutina asignada exitosamente.' : 'Error al realizar la asignación.']);
    }

    /**
     * Editar asignación de rutina (AJAX - POST)
     */
    public function editar_asignacion()
    {
        $this->protect("rutinas:editar");

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Método no permitido']);
            return;
        }

        $idAsignacion = intval($_POST['id_asignacion'] ?? 0);
        if ($idAsignacion <= 0) {
            echo json_encode(['success' => false, 'message' => 'ID de asignación inválido.']);
            return;
        }

        $datos = [];
        if (isset($_POST['cedula_cliente']))
            $datos['cedula_cliente'] = trim($_POST['cedula_cliente']);
        if (isset($_POST['id_rutina']))
            $datos['id_rutina'] = intval($_POST['id_rutina']);
        if (isset($_POST['fecha_asignacion']))
            $datos['fecha_asignacion'] = $_POST['fecha_asignacion'];
        if (isset($_POST['fecha_inicio']))
            $datos['fecha_inicio'] = !empty($_POST['fecha_inicio']) ? $_POST['fecha_inicio'] : null;
        if (isset($_POST['fecha_fin']))
            $datos['fecha_fin'] = !empty($_POST['fecha_fin']) ? $_POST['fecha_fin'] : null;
        if (isset($_POST['estado']))
            $datos['estado'] = $_POST['estado'];
        if (isset($_POST['progreso']))
            $datos['progreso'] = floatval($_POST['progreso']);

        $ok = $this->model->actualizarAsignacion($idAsignacion, $datos);
        echo json_encode(['success' => $ok, 'message' => $ok ? 'Asignación modificada correctamente.' : 'No se realizaron cambios o error de base de datos.']);
    }

    /**
     * Eliminar asignación de rutina (AJAX - POST)
     */
    public function eliminar_asignacion()
    {
        $this->protect("rutinas:eliminar");

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Método no permitido']);
            return;
        }

        $idAsignacion = intval($_POST['id_asignacion'] ?? 0);
        if ($idAsignacion <= 0) {
            echo json_encode(['success' => false, 'message' => 'ID de asignación inválido.']);
            return;
        }

        $ok = $this->model->eliminarAsignacion($idAsignacion);
        echo json_encode(['success' => $ok, 'message' => $ok ? 'Asignación eliminada correctamente.' : 'Error al eliminar la asignación.']);
    }

    /**
     * Obtener asignaciones de un cliente específico en JSON (AJAX)
     */
    public function buscar_asignaciones_cliente_ajax()
    {
        $this->protect("rutinas:ver");

        $cedula = $_GET['cedula_cliente'] ?? '';
        if (empty($cedula)) {
            echo json_encode([]);
            exit;
        }
        $resultados = $this->model->obtenerAsignacionesPorCliente($cedula);
        header('Content-Type: application/json');
        echo json_encode($resultados);
        exit;
    }
}
