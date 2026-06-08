<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ProductosModel;

class ProductosController extends BaseController
{
    public function __construct(
        private ProductosModel $model
    ) {}

    /**
     * Muestra la vista principal de productos (Catálogo, Inventario y Ventas)
     */
    public function index()
    {
        // Soporte para término de búsqueda en URL (?buscar=)
        $termino = $_GET['buscar'] ?? null;

        // Obtener productos activos y aquellos que se encuentran bajo el stock de alerta mínimo
        $productos = $this->model->obtenerTodos($termino);
        $bajoStock = $this->model->obtenerBajoStock();
        $clientes = $this->model->obtenerClientes();

        // Obtener mensajes de sesión temporales (Toasts/Alertas)
        $mensaje = $_SESSION['mensaje'] ?? '';
        $tipoMensaje = $_SESSION['tipo_mensaje'] ?? '';
        unset($_SESSION['mensaje'], $_SESSION['tipo_mensaje']);

        // Renderizado usando el método heredado de BaseController
        echo $this->templates->render('productos', [
            'productos' => $productos,
            'bajoStock' => $bajoStock,
            'clientes'  => $clientes,
            'mensaje' => $mensaje,
            'tipoMensaje' => $tipoMensaje,
            'termino' => $termino
        ]);
    }

    /**
     * Endpoint API AJAX para buscar productos dinámicamente
     */
    public function buscarAjax()
    {
        if (!isset($_GET['ajax']) || $_GET['ajax'] !== 'buscar_productos') {
            http_response_code(400);
            echo json_encode(['error' => 'Solicitud inválida']);
            return;
        }

        $termino = $_GET['termino'] ?? '';
        $resultados = $this->model->obtenerTodos($termino);

        header('Content-Type: application/json');
        echo json_encode($resultados);
        exit;
    }

    /**
     * Endpoint API AJAX para obtener clientes activos
     */
    public function obtenerClientesAjax()
    {
        $clientes = $this->model->obtenerClientes();
        header('Content-Type: application/json');
        echo json_encode($clientes);
        exit;
    }

    /**
     * Registra un nuevo producto en el gimnasio
     */
    public function crear()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Método no permitido']);
            return;
        }

        $codigo = $_POST['codigo_producto'] ?? '';
        $nombre = $_POST['nombre'] ?? '';
        $precio = $_POST['precio_venta'] ?? null;

        if (empty($codigo) || empty($nombre) || $precio === null) {
            echo json_encode(['success' => false, 'message' => 'Código de producto, nombre y precio de venta son requeridos.']);
            return;
        }

        $datos = [
            'codigo_producto' => strip_tags(trim($codigo)),
            'nombre' => strip_tags(trim($nombre)),
            'categoria' => !empty($_POST['categoria']) ? strip_tags(trim($_POST['categoria'])) : null,
            'precio_venta' => floatval($precio),
            'stock_minimo' => isset($_POST['stock_minimo']) ? intval($_POST['stock_minimo']) : 0,
            'stock_actual' => isset($_POST['stock_actual']) ? intval($_POST['stock_actual']) : 0,
            'unidad_medida' => !empty($_POST['unidad_medida']) ? strip_tags(trim($_POST['unidad_medida'])) : 'unidad',
            'activo' => 1
        ];

        $exito = $this->model->crear($datos);

        header('Content-Type: application/json');
        if ($exito) {
            echo json_encode(['success' => true, 'message' => '✅ Producto registrado exitosamente.']);
        } else {
            echo json_encode(['success' => false, 'message' => '❌ Error al registrar producto. Código duplicado.']);
        }
        exit;
    }

    /**
     * Edita o modifica un producto existente
     */
    public function editar()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Método no permitido']);
            return;
        }

        $codigo = $_POST['codigo_producto'] ?? '';
        if (empty($codigo)) {
            echo json_encode(['success' => false, 'message' => 'Código de producto faltante para la edición.']);
            return;
        }

        $datosNuevos = [];
        if (isset($_POST['nombre']))
            $datosNuevos['nombre'] = strip_tags(trim($_POST['nombre']));
        if (isset($_POST['categoria']))
            $datosNuevos['categoria'] = strip_tags(trim($_POST['categoria']));
        if (isset($_POST['precio_venta']))
            $datosNuevos['precio_venta'] = floatval($_POST['precio_venta']);
        if (isset($_POST['stock_minimo']))
            $datosNuevos['stock_minimo'] = intval($_POST['stock_minimo']);
        if (isset($_POST['stock_actual']))
            $datosNuevos['stock_actual'] = intval($_POST['stock_actual']);
        if (isset($_POST['unidad_medida']))
            $datosNuevos['unidad_medida'] = strip_tags(trim($_POST['unidad_medida']));

        $exito = $this->model->actualizar($codigo, $datosNuevos);

        header('Content-Type: application/json');
        if ($exito) {
            echo json_encode(['success' => true, 'message' => '✅ Producto actualizado exitosamente.']);
        } else {
            echo json_encode(['success' => false, 'message' => '❌ Error al intentar actualizar el producto.']);
        }
        exit;
    }

    /**
     * Elimina un producto de la base de datos (lógica o físicamente)
     */
    public function eliminar()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Método no permitido']);
            return;
        }

        $codigo = $_POST['codigo_producto'] ?? '';
        $borradoFisico = isset($_POST['fisico']) && filter_var($_POST['fisico'], FILTER_VALIDATE_BOOLEAN);

        if (empty($codigo)) {
            echo json_encode(['success' => false, 'message' => 'Código de producto no especificado.']);
            return;
        }

        $exito = $this->model->eliminar($codigo, $borradoFisico);

        header('Content-Type: application/json');
        if ($exito) {
            echo json_encode(['success' => true, 'message' => '🗑️ Producto eliminado correctamente.']);
        } else {
            echo json_encode(['success' => false, 'message' => '❌ No se pudo completar la eliminación del producto.']);
        }
        exit;
    }

    /**
     * Actualiza o modifica la cantidad física en stock (Entrada/Salida de Inventario)
     */
    public function actualizarStock()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Método no permitido']);
            return;
        }

        $codigo = $_POST['codigo_producto'] ?? '';
        $cantidad = isset($_POST['cantidad']) ? intval($_POST['cantidad']) : 0;

        if (empty($codigo) || $cantidad === 0) {
            echo json_encode(['success' => false, 'message' => 'Datos insuficientes o variación de cantidad en cero.']);
            return;
        }

        $exito = $this->model->actualizarStock($codigo, $cantidad);

        header('Content-Type: application/json');
        if ($exito) {
            echo json_encode(['success' => true, 'message' => '📦 Inventario actualizado con éxito.']);
        } else {
            echo json_encode(['success' => false, 'message' => '❌ El stock resultante no puede ser menor que cero.']);
        }
        exit;
    }

    /**
     * Registra una nueva transacción de venta de uno o más productos
     */
    public function registrarVenta()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            return;
        }

        // Obtener el cuerpo de la petición (JSON)
        $input = json_decode(file_get_contents('php://input'), true);

        $cedulaCliente = !empty($input['cedula_cliente']) ? strip_tags(trim($input['cedula_cliente'])) : null;
        $metodoPago = !empty($input['metodo_pago']) ? strip_tags(trim($input['metodo_pago'])) : 'Efectivo';
        $items = $input['productos'] ?? [];

        if (empty($items)) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => '⚠️ Debe agregar al menos un producto a la lista de venta.']);
            return;
        }

        // Procesar en el modelo bajo una sola transacción segura
        $resultado = $this->model->registrarVentaMultiplesProductos($cedulaCliente, $metodoPago, $items);

        header('Content-Type: application/json');
        echo json_encode($resultado);
        exit;
    }
}