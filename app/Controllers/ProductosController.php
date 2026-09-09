<?php

namespace App\Controllers;

use App\Controllers\Controller;
use App\Models\ProductoModel;
use App\Models\BitacoraModel;
use App\Services\Reportes\ReporteInventario;

class ProductosController extends Controller
{
    public function __construct(
        private $logger = new BitacoraModel(),
        private $model = new ProductoModel()
    ) {}

    /**
     * Muestra la vista principal de productos (Catálogo e Inventario)
     */
    public function index()
    {
        $this->protect("productos:ver");

        // Soporte para término de búsqueda en URL (?buscar=)
        $termino = $_GET['buscar'] ?? null;

        // Obtener productos activos y aquellos que se encuentran bajo el stock de alerta mínimo
        $productos = $this->model->obtenerTodos($termino);
        $bajoStock = $this->model->obtenerBajoStock();

        // Obtener mensajes de sesión temporales (Toasts/Alertas)
        $mensaje = $_SESSION['mensaje'] ?? '';
        $tipoMensaje = $_SESSION['tipo_mensaje'] ?? '';
        unset($_SESSION['mensaje'], $_SESSION['tipo_mensaje']);

        // Renderizado usando el método heredado de Controller
        echo $this->render('productos', [
            'productos' => $productos,
            'bajoStock' => $bajoStock,
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
     * Maneja la subida de la imagen y retorna la ruta
     */
    private function procesarImagen($archivo)
    {
        if (isset($archivo) && $archivo['error'] === UPLOAD_ERR_OK) {
            $permitidos = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
            if (in_array($archivo['type'], $permitidos)) {
                $directorio = 'public/uploads/productos/';
                if (!file_exists($directorio)) {
                    mkdir($directorio, 0777, true);
                }
                
                $extension = pathinfo($archivo['name'], PATHINFO_EXTENSION);
                $nombreArchivo = uniqid('prod_') . '.' . $extension;
                $rutaDestino = $directorio . $nombreArchivo;
                
                if (move_uploaded_file($archivo['tmp_name'], $rutaDestino)) {
                    return $rutaDestino;
                }
            }
        }
        return null;
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

        // Procesar subida de imagen si existe
        $rutaImagen = $this->procesarImagen($_FILES['imagen'] ?? null);

        $datos = [
            'codigo_producto' => strip_tags(trim($codigo)),
            'nombre' => strip_tags(trim($nombre)),
            'id_categoria' => !empty($_POST['id_categoria']) ? strip_tags(trim($_POST['id_categoria'])) : null,
            'precio_venta' => floatval($precio),
            'stock_minimo' => isset($_POST['stock_minimo']) ? intval($_POST['stock_minimo']) : 0,
            'stock_actual' => isset($_POST['stock_actual']) ? intval($_POST['stock_actual']) : 0,
            'id_unidad' => !empty($_POST['id_unidad']) ? strip_tags(trim($_POST['id_unidad'])) : 'unidad',
            'activo' => 1,
            'imagen' => $rutaImagen
        ];
        
        $exito = $this->model->crear($datos);

        $this->logger->log("Producto '{codigo_producto}' creado", [
            "modulo" => "productos",
            "accion" => "crear",
            "codigo_producto" => $datos["codigo_producto"],
        ]);

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

        // Actualizar la imagen si se envía una nueva
        $rutaImagen = $this->procesarImagen($_FILES['imagen'] ?? null);
        if ($rutaImagen) {
            $datosNuevos['imagen'] = $rutaImagen;
        }

        $exito = $this->model->actualizar($codigo, $datosNuevos);

        $this->logger->log("Producto '{codigo_producto}' actualizado", [
            "modulo" => "productos",
            "accion" => "editar",
            "codigo_producto" => $codigo,
        ]);

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
        $this->logger->log("Producto '{codigo_producto}' eliminado", [
            "modulo" => "productos",
            "accion" => "eliminar",
            "codigo_producto" => $codigo,
        ]);

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
        $this->logger->log("Stock del producto '{codigo_producto}' actualizado", [
            "modulo" => "productos",
            "accion" => "actualizar_stock",
            "codigo_producto" => $codigo,
            "cantidad" => $cantidad,
        ]);

        header('Content-Type: application/json');
        if ($exito) {
            echo json_encode(['success' => true, 'message' => '📦 Inventario actualizado con éxito.']);
        } else {
            echo json_encode(['success' => false, 'message' => '❌ El stock resultante no puede ser menor que cero.']);
        }
        exit;
    }

    /**
     * Aumenta el precio de los suplementos (Categoría 1) en un 10%
     */
    public function aumentarPreciosSuplementos()
    {
        $this->protect("productos:editar"); // Validamos permisos

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Método no permitido']);
            return;
        }

        // Llamamos a la función del modelo que retorna un array asociativo
        $resultado = $this->model->aumentarPrecioSuplementos();

        if ($resultado['success']) {
            $this->logger->log("Aumento global de precios en suplementos (10%)", [
                "modulo" => "productos",
                "accion" => "aumentar_precios_suplementos"
            ]);
        }

        header('Content-Type: application/json');
        echo json_encode($resultado);
        exit;
    }

    /**
     * Muestra exclusivamente la interfaz visual del formulario de reportes de inventario
     */
    public function vistaInventario()
    {
        $this->protect("productos:ver");
        echo $this->render('reportes/inventario');
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
        $pdf = new ReporteInventario();

        $pdf->SetTitle(utf8_decode('Reporte General de Inventario - SOFIT GYM'));
        $pdf->SetAuthor('Sistema SOFIT GYM');
        $pdf->crearReporte($inventarioData);
        $pdf->Output('I', 'reporte_general_inventario.pdf');
    }
}