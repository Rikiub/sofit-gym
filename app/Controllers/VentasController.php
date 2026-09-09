<?php

namespace App\Controllers;

use App\Controllers\Controller;
use App\Models\VentasModel;
use App\Models\BitacoraModel;
use App\Models\ProductoModel;
use App\Services\Reportes\ReporteProductosMasVendidos;

class VentasController extends Controller
{
    public function __construct(
        private $logger = new BitacoraModel(),
        private $model = new VentasModel()
    ) {}

    /**
     * Muestra la vista principal del historial de ventas
     */
    public function index()
    {
        $this->protect("ventas:ver");

        $ventas = $this->model->obtenerVentas();
        $clientes = $this->model->obtenerClientes();
        
        // Instanciamos el modelo de productos para el POS
        $productoModel = new ProductoModel();
        $productos = $productoModel->obtenerTodos();

        // Obtener mensajes de sesión temporales
        $mensaje = $_SESSION['mensaje'] ?? '';
        $tipoMensaje = $_SESSION['tipo_mensaje'] ?? '';
        unset($_SESSION['mensaje'], $_SESSION['tipo_mensaje']);

        echo $this->render('ventas', [
            'ventas' => $ventas,
            'clientes' => $clientes,
            'productos' => $productos,
            'mensaje' => $mensaje,
            'tipoMensaje' => $tipoMensaje
        ]);
    }

    /**
     * Endpoint API AJAX para obtener clientes activos (útil para el POS)
     */
    public function obtenerClientesAjax()
    {
        $this->protect("ventas:ver");

        $clientes = $this->model->obtenerClientes();
        header('Content-Type: application/json');
        echo json_encode($clientes);
        exit;
    }

    /**
     * Registra una nueva venta manual individual (CRUD de venta)
     */
    public function crear()
    {
        $this->protect("ventas:crear");

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Método no permitido']);
            return;
        }

        $datos = [
            'codigo_producto' => $_POST['codigo_producto'] ?? '',
            'cedula_cliente'  => !empty($_POST['cedula_cliente']) ? strip_tags(trim($_POST['cedula_cliente'])) : null,
            'id_metodo'       => !empty($_POST['id_metodo']) ? intval($_POST['id_metodo']) : 1,
            'cantidad_vendida'=> isset($_POST['cantidad_vendida']) ? floatval($_POST['cantidad_vendida']) : 0,
            'monto_total'     => isset($_POST['monto_total']) ? floatval($_POST['monto_total']) : 0,
        ];

        if (empty($datos['codigo_producto']) || $datos['cantidad_vendida'] <= 0) {
            echo json_encode(['success' => false, 'message' => 'Código de producto y una cantidad válida son requeridos.']);
            return;
        }

        $exito = $this->model->crearVenta($datos);

        $this->logger->log("Venta manual creada para el producto '{codigo}'", [
            "modulo" => "ventas",
            "accion" => "crear",
            "codigo" => $datos['codigo_producto']
        ]);

        header('Content-Type: application/json');
        if ($exito) {
            echo json_encode(['success' => true, 'message' => '✅ Registro de venta creado exitosamente.']);
        } else {
            echo json_encode(['success' => false, 'message' => '❌ Error al registrar la venta manual.']);
        }
        exit;
    }

    /**
     * Edita un registro de venta existente (ej. corrección de método de pago o montos)
     */
    public function editar()
    {
        $this->protect("ventas:editar");

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Método no permitido']);
            return;
        }

        $idVenta = isset($_POST['id_venta']) ? intval($_POST['id_venta']) : 0;
        if ($idVenta === 0) {
            echo json_encode(['success' => false, 'message' => 'ID de venta faltante o inválido.']);
            return;
        }

        $datosNuevos = [];
        if (isset($_POST['id_metodo'])) $datosNuevos['id_metodo'] = intval($_POST['id_metodo']);
        if (isset($_POST['cedula_cliente'])) $datosNuevos['cedula_cliente'] = strip_tags(trim($_POST['cedula_cliente']));
        if (isset($_POST['cantidad_vendida'])) $datosNuevos['cantidad_vendida'] = floatval($_POST['cantidad_vendida']);
        if (isset($_POST['monto_total'])) $datosNuevos['monto_total'] = floatval($_POST['monto_total']);
        // Consideración: Si se edita la cantidad o producto, el stock no se ajusta automáticamente aquí, a menos que uses Triggers en BD.

        $exito = $this->model->actualizarVenta($idVenta, $datosNuevos);

        $this->logger->log("Venta ID '{id}' actualizada", [
            "modulo" => "ventas",
            "accion" => "editar",
            "id" => $idVenta,
        ]);

        header('Content-Type: application/json');
        if ($exito) {
            echo json_encode(['success' => true, 'message' => '✅ Venta actualizada exitosamente.']);
        } else {
            echo json_encode(['success' => false, 'message' => '❌ Error al intentar actualizar la venta.']);
        }
        exit;
    }

    /**
     * Elimina un registro de venta
     */
    public function eliminar()
    {
        $this->protect("ventas:eliminar");

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Método no permitido']);
            return;
        }

        $idVenta = isset($_POST['id_venta']) ? intval($_POST['id_venta']) : 0;
        if ($idVenta === 0) {
            echo json_encode(['success' => false, 'message' => 'ID de venta no especificado.']);
            return;
        }

        $exito = $this->model->eliminarVenta($idVenta);
        
        $this->logger->log("Venta ID '{id}' eliminada", [
            "modulo" => "ventas",
            "accion" => "eliminar",
            "id" => $idVenta,
        ]);

        header('Content-Type: application/json');
        if ($exito) {
            echo json_encode(['success' => true, 'message' => '🗑️ Registro de venta eliminado correctamente.']);
        } else {
            echo json_encode(['success' => false, 'message' => '❌ No se pudo completar la eliminación de la venta.']);
        }
        exit;
    }

    /**
     * Registra una nueva transacción (Punto de Venta) procesando múltiples productos
     */
    public function registrarVenta()
    {
        $this->protect("ventas:crear");

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            return;
        }

        // Obtener el cuerpo de la petición (JSON) enviado desde el frontend del POS
        $input = json_decode(file_get_contents('php://input'), true);

        $cedulaCliente = !empty($input['cedula']) ? strip_tags(trim($input['cedula'])) : null;
        $metodoPago = !empty($input['metodo_pago']) ? intval($input['metodo_pago'] ?? 0) : 1;
        $items = $input['productos'] ?? [];

        if (empty($items)) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => '⚠️ Debe agregar al menos un producto al carrito de compras.']);
            return;
        }

        // Procesar en el modelo bajo una sola transacción segura
        $resultado = $this->model->registrarVentaMultiplesProductos($cedulaCliente, $metodoPago, $items);
        
        if ($resultado['success']) {
            $this->logger->log("Transacción de venta múltiple registrada. Cliente '{cedula_cliente}'", [
                "modulo" => "ventas",
                "accion" => "registrar_venta_pos",
                "cedula_cliente" => $cedulaCliente,
                "metodoPago" => $metodoPago,
                "cantidad_productos" => count($items),
            ]);
        }

        header('Content-Type: application/json');
        echo json_encode($resultado);
        exit;
    }

    /**
     * Endpoint API AJAX para obtener los productos que nunca han sido vendidos
     */
    public function obtenerProductosSinVenderAjax()
    {
        $this->protect("ventas:ver"); // Validación de permisos

        // Llamamos a la función del modelo
        $productos = $this->model->obtenerProductosSinVender();

        // Retornamos los datos en formato JSON para poder consumirlos desde JS
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'data' => $productos
        ]);
        exit;
    }

    // ==========================================
    // REPORTES
    // ==========================================

    /**
     * Muestra exclusivamente la interfaz visual del formulario de reportes de ventas
     */
    public function vistaReporte()
    {
        $this->protect("ventas:ver");
        echo $this->render('reportes/productos');
        exit;
    }

    /**
     * Genera y descarga el reporte en formato PDF de los productos más vendidos.
     */
    public function generarReporteMasVendidos()
    {
        $this->protect("ventas:ver");
        
        // Soporte para filtros opcionales de rango de fecha desde la URL
        $fechaInicio = !empty($_GET['fecha_inicio']) ? strip_tags(trim($_GET['fecha_inicio'])) : null;
        $fechaFin    = !empty($_GET['fecha_fin'])    ? strip_tags(trim($_GET['fecha_fin']))    : null;

        // Consultar los datos al Modelo estructurado de Ventas
        $productosData = $this->model->obtenerProductosMasVendidos($fechaInicio, $fechaFin);

        if (is_array($productosData) && count($productosData) > 0) {
            $pdf = new ReporteProductosMasVendidos();
            $pdf->SetTitle(utf8_decode('Reporte de Productos Más Vendidos - SOFIT GYM'));
            $pdf->SetAuthor('Sistema SOFIT GYM');
            
            $pdf->crearReporte($productosData, $fechaInicio, $fechaFin);
            $pdf->Output('I', 'reporte_productos_mas_vendidos.pdf');
            exit;
        } else {
            $_SESSION['mensaje'] = "No se encontraron registros de ventas para generar el reporte de productos.";
            $_SESSION['tipo_mensaje'] = "warning"; 

            // Redireccionar a la vista de historial de ventas o reportes
            header("Location: ?page=ventas");
            exit;
        }
    }
}