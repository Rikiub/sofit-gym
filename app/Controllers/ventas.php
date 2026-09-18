<?php

namespace App\Controllers;

use App\Core\Route;
use App\Models\VentasModel;
use App\Models\ProductoModel;
use App\Services\Reportes\ReporteProductosMasVendidos;

$model = new VentasModel();

switch (Route::action()) {
    /**
     * Muestra la vista principal del historial de ventas
     */
    case "index":
        Route::protect("ventas:ver");

        $ventas = $model->obtenerVentas();
        $clientes = $model->obtenerClientes();

        // Instanciamos el modelo de productos para el POS
        $productoModel = new ProductoModel();
        $productos = $productoModel->obtenerTodos();

        // Obtener mensajes de sesión temporales
        $mensaje = $_SESSION['mensaje'] ?? '';
        $tipoMensaje = $_SESSION['tipo_mensaje'] ?? '';
        unset($_SESSION['mensaje'], $_SESSION['tipo_mensaje']);

        echo Route::render('ventas', [
            'ventas' => $ventas,
            'clientes' => $clientes,
            'productos' => $productos,
            'mensaje' => $mensaje,
            'tipoMensaje' => $tipoMensaje
        ]);
        exit;

    /**
     * Endpoint API AJAX para obtener clientes activos (útil para el POS)
     */
    case "obtenerClientesAjax":
        Route::protect("ventas:ver");

        $clientes = $model->obtenerClientes();
        header('Content-Type: application/json');
        echo json_encode($clientes);
        exit;

    /**
     * Registra una nueva venta manual individual (CRUD de venta)
     */
    case "crear":
        Route::protect("ventas:crear");

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Método no permitido']);
            return;
        }

        $datos = [
            'codigo_producto' => $_POST['codigo_producto'] ?? '',
            'cedula_cliente'  => !empty($_POST['cedula_cliente']) ? strip_tags(trim($_POST['cedula_cliente'])) : null,
            'id_metodo'       => !empty($_POST['id_metodo']) ? intval($_POST['id_metodo']) : 1,
            'cantidad_vendida' => isset($_POST['cantidad_vendida']) ? floatval($_POST['cantidad_vendida']) : 0,
            'monto_total'     => isset($_POST['monto_total']) ? floatval($_POST['monto_total']) : 0,
        ];

        if (empty($datos['codigo_producto']) || $datos['cantidad_vendida'] <= 0) {
            echo json_encode(['success' => false, 'message' => 'Código de producto y una cantidad válida son requeridos.']);
            return;
        }

        $exito = $model->crearVenta($datos);

        header('Content-Type: application/json');
        if ($exito) {
            echo json_encode(['success' => true, 'message' => '✅ Registro de venta creado exitosamente.']);
        } else {
            echo json_encode(['success' => false, 'message' => '❌ Error al registrar la venta manual.']);
        }
        exit;

    /**
     * Edita un registro de venta existente (ej. corrección de método de pago o montos)
     */
    case "editar":
        Route::protect("ventas:editar");

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

        $exito = $model->actualizarVenta($idVenta, $datosNuevos);

        header('Content-Type: application/json');
        if ($exito) {
            echo json_encode(['success' => true, 'message' => '✅ Venta actualizada exitosamente.']);
        } else {
            echo json_encode(['success' => false, 'message' => '❌ Error al intentar actualizar la venta.']);
        }
        exit;

    /**
     * Elimina un registro de venta
     */
    case "eliminar":
        Route::protect("ventas:eliminar");

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

        $exito = $model->eliminarVenta($idVenta);

        header('Content-Type: application/json');
        if ($exito) {
            echo json_encode(['success' => true, 'message' => '🗑️ Registro de venta eliminado correctamente.']);
        } else {
            echo json_encode(['success' => false, 'message' => '❌ No se pudo completar la eliminación de la venta.']);
        }
        exit;

    /**
     * Registra una nueva transacción (Punto de Venta) procesando múltiples productos
     */
    case "registrarVenta":
        Route::protect("ventas:crear");

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
        $resultado = $model->registrarVentaMultiplesProductos($cedulaCliente, $metodoPago, $items);

        header('Content-Type: application/json');
        echo json_encode($resultado);
        exit;

    /**
     * Endpoint API AJAX para obtener los productos que nunca han sido vendidos
     */
    case "obtenerProductosSinVenderAjax":
        Route::protect("ventas:ver"); // Validación de permisos

        // Llamamos a la función del modelo
        $productos = $model->obtenerProductosSinVender();

        // Retornamos los datos en formato JSON para poder consumirlos desde JS
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'data' => $productos
        ]);
        exit;

    // ==========================================
    // REPORTES
    // ==========================================

    /**
     * Muestra exclusivamente la interfaz visual del formulario de reportes de ventas
     */
    case "vistaReporte":
        Route::protect("ventas:ver");
        echo Route::render('reportes/productos');
        exit;

    /**
     * Genera y descarga el reporte en formato PDF de los productos más vendidos.
     */
    case "generarReporteMasVendidos":
        Route::protect("ventas:ver");

        // Soporte para filtros opcionales de rango de fecha desde la URL
        $fechaInicio = !empty($_GET['fecha_inicio']) ? strip_tags(trim($_GET['fecha_inicio'])) : null;
        $fechaFin    = !empty($_GET['fecha_fin'])    ? strip_tags(trim($_GET['fecha_fin']))    : null;

        // Consultar los datos al Modelo estructurado de Ventas
        $productosData = $model->obtenerProductosMasVendidos($fechaInicio, $fechaFin);

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
