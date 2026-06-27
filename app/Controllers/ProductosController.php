<?php

namespace App\Controllers;

use App\Controllers\Controller;
use App\Core\Reportes\reporteInventario;
use App\Core\Reportes\reporteProductosMasVendidos;
use App\Models\ProductosModel;

class ProductosController extends Controller
{
    public function __construct(
        private ProductosModel $model
    ) {}

    /**
     * Muestra la vista principal de productos (Catálogo, Inventario y Ventas)
     */
    public function index()
    {
        $this->protect("productos:ver");

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

        // Renderizado usando el método heredado de Controller
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
        $this->protect("productos:ver");

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
        $this->protect("productos:ver");

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
        $this->protect("productos:crear");

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
            'id_categoria' => !empty($_POST['id_categoria']) ? strip_tags(trim($_POST['id_categoria'])) : null,
            'precio_venta' => floatval($precio),
            'stock_minimo' => isset($_POST['stock_minimo']) ? intval($_POST['stock_minimo']) : 0,
            'stock_actual' => isset($_POST['stock_actual']) ? intval($_POST['stock_actual']) : 0,
            'id_unidad' => !empty($_POST['id_unidad']) ? strip_tags(trim($_POST['id_unidad'])) : 'unidad',
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
        $this->protect("productos:editar");

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
        if (isset($_POST['id_categoria']))
            $datosNuevos['id_categoria'] = strip_tags(trim($_POST['id_categoria']));
        if (isset($_POST['precio_venta']))
            $datosNuevos['precio_venta'] = floatval($_POST['precio_venta']);
        if (isset($_POST['stock_minimo']))
            $datosNuevos['stock_minimo'] = intval($_POST['stock_minimo']);
        if (isset($_POST['stock_actual']))
            $datosNuevos['stock_actual'] = intval($_POST['stock_actual']);
        if (isset($_POST['id_unidad']))
            $datosNuevos['id_unidad'] = strip_tags(trim($_POST['id_unidad']));

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
        $this->protect("productos:eliminar");

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
        $this->protect("productos:editar");

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
        $this->protect("productos:crear");

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            return;
        }

        // Obtener el cuerpo de la petición (JSON)
        $input = json_decode(file_get_contents('php://input'), true);

        $cedulaCliente = !empty($input['cedula']) ? strip_tags(trim($input['cedula'])) : null;
        $metodoPago = !empty($input['metodo_pago']) ? intval($input['metodo_pago'] ?? 0) : 1;
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

    // Reportes

    /**
     * Muestra exclusivamente la interfaz visual del formulario de reportes
     * Invocado mediante ?url=productos&action=vistaReporte
     */
    public function vistaReporte()
    {
        // Renderiza el formulario usando el motor Plates cargando tu nueva vista
        $this->protect("productos:ver");
        echo $this->templates->render('reportes/productos');
        exit;
    }

    /**
     * Genera y descarga el reporte en formato PDF de los productos más vendidos.
     * Guiado de la lógica de negocio y control de flujo de formacionControl.php
     */
    public function generarReporteMasVendidos()
    {
        $this->protect("productos:ver");
        // Soporte para filtros opcionales de rango de fecha desde la URL (?fecha_inicio= & fecha_fin=)
        $fechaInicio = !empty($_GET['fecha_inicio']) ? strip_tags(trim($_GET['fecha_inicio'])) : null;
        $fechaFin    = !empty($_GET['fecha_fin'])    ? strip_tags(trim($_GET['fecha_fin']))    : null;

        // 1. Consultar los datos al Modelo estructurado
        $productosData = $this->model->obtenerProductosMasVendidos($fechaInicio, $fechaFin);

        // 2. Control de flujo adaptado de formacionControl (Verificar si es un array con datos)
        if (is_array($productosData) && count($productosData) > 0) {

            // Instanciar el helper del reporte PDF
            $pdf = new reporteProductosMasVendidos();

            // Establecer metadatos básicos del documento PDF
            $pdf->SetTitle(utf8_decode('Reporte de Productos Más Vendidos - SOFIT GYM'));
            $pdf->SetAuthor('Sistema SOFIT GYM');

            // Invocar el renderizado de la tabla con los parámetros correspondientes
            $pdf->crearReporte($productosData, $fechaInicio, $fechaFin);

            // Enviar los headers HTTP correspondientes e imprimir el flujo binario del PDF en el navegador
            // I: Envía el fichero al navegador de forma limpia para previsualización / descarga
            $pdf->Output('I', 'reporte_productos_mas_vendidos.pdf');
            exit;
        } else {
            // Si es falso o vacío, preparamos la alerta de SweetAlert como en formacionControl
            $_SESSION['mensaje'] = "No se encontraron registros de ventas para generar el reporte de productos.";
            $_SESSION['tipo_mensaje'] = "warning"; // Usado para disparar tus Toasts/Alertas en la vista

            // Redireccionar de vuelta al catálogo/inventario general de productos
            header("Location: ?page=productos");
            exit;
        }
    }

    public function vistaInventario()
    {
        $this->protect("productos:ver");
        // Renderiza el formulario usando el motor Plates cargando tu nueva vista
        echo $this->templates->render('reportes/inventario');
        exit;
    }

    /**
     * Generar reporte PDF del inventario general actual del catálogo de productos
     */
    public function reporteInventario()
    {
        $this->protect("productos:ver");

        // Solicitar al modelo los productos activos con sus uniones de categoría y unidad
        $inventarioData = $this->model->obtenerReporteInventario();

        // Instanciar el helper específico de inventario que creamos
        $pdf = new reporteInventario();

        // Establecer los metadatos obligatorios de FPDF
        $pdf->SetTitle(utf8_decode('Reporte General de Inventario - SOFIT GYM'));
        $pdf->SetAuthor('Sistema SOFIT GYM');

        // Construir el cuerpo de las páginas y la tabla del reporte
        $pdf->crearReporte($inventarioData);

        // Renderizar y forzar la visualización en el navegador de manera limpia
        $pdf->Output('I', 'reporte_general_inventario.pdf');
    }
}
