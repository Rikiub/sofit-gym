<?php

namespace App\Models;

use PDO;
use PDOException;

class ProductosModel extends BaseModel
{
    private string $tabla = 'producto';

    /**
     * Obtener todos los productos activos de la base de datos
     * Permite opcionalmente buscar por un término (código, nombre o categoría)
     *
     * @param string|null $termino Término de búsqueda opcional
     * @return array Listado de productos
     */
    public function obtenerTodos(?string $termino = null): array
    {
        try {
            if (!empty($termino)) {
                $sql = "SELECT * FROM {$this->tabla} 
                        WHERE activo = 1 
                        AND (codigo_producto LIKE :termino 
                             OR nombre LIKE :termino 
                             OR categoria LIKE :termino)
                        ORDER BY nombre ASC";
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute(['termino' => "%{$termino}%"]);
            } else {
                $sql = "SELECT * FROM {$this->tabla} WHERE activo = 1 ORDER BY nombre ASC";
                $stmt = $this->pdo->query($sql);
            }
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en ProductosModel::obtenerTodos: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtener un producto específico por su código primario
     *
     * @param string $codigo Código único del producto
     * @return array|null Datos del producto o null si no existe
     */
    public function obtenerPorCodigo(string $codigo): ?array
    {
        try {
            $sql = "SELECT * FROM {$this->tabla} WHERE codigo_producto = ? LIMIT 1";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$codigo]);
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultado ?: null;
        } catch (PDOException $e) {
            error_log("Error en ProductosModel::obtenerPorCodigo: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Insertar un nuevo producto utilizando pdoInsert de la clase abstracta
     *
     * @param array $datos Estructura asociativa con los campos del producto
     * @return bool True si se completó con éxito
     */
    public function crear(array $datos): bool
    {
        try {
            $nuevoProducto = [
                'codigo_producto' => $datos['codigo_producto'],
                'nombre'          => $datos['nombre'],
                'categoria'       => $datos['categoria'] ?? null,
                'precio_venta'    => $datos['precio_venta'],
                'stock_minimo'    => $datos['stock_minimo'] ?? 0,
                'stock_actual'    => $datos['stock_actual'] ?? 0,
                'unidad_medida'   => $datos['unidad_medida'] ?? 'unidad',
                'activo'          => $datos['activo'] ?? 1
            ];

            $this->pdoInsert($this->tabla, $nuevoProducto);
            return true;
        } catch (PDOException $e) {
            error_log("Error en ProductosModel::crear: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Actualizar los datos de un producto utilizando pdoUpdate
     *
     * @param string $codigo Código del producto a editar
     * @param array $datos Campos modificados a actualizar
     * @return bool True si al menos una fila fue modificada o la consulta fue exitosa
     */
    public function actualizar(string $codigo, array $datos): bool
    {
        try {
            unset($datos['codigo_producto']);
            $this->pdoUpdate($this->tabla, $datos, ['codigo_producto' => $codigo]);
            return true;
        } catch (PDOException $e) {
            error_log("Error en ProductosModel::actualizar: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Eliminación de producto (Por defecto, lógica/soft delete para preservar integridad referencial)
     *
     * @param string $codigo Código del producto
     * @param bool $fisico Definir si se borra permanentemente de la base de datos
     * @return bool True si la operación se realizó de manera correcta
     */
    public function eliminar(string $codigo, bool $fisico = false): bool
    {
        try {
            if ($fisico) {
                $filasAfectadas = $this->pdoDelete($this->tabla, ['codigo_producto' => $codigo]);
                return $filasAfectadas > 0;
            } else {
                return $this->actualizar($codigo, ['activo' => 0]);
            }
        } catch (PDOException $e) {
            error_log("Error en ProductosModel::eliminar: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Actualizar únicamente el inventario/stock de un producto específico
     *
     * @param string $codigo Código del producto
     * @param int $cantidad Cantidad física a descontar o añadir (positivo o negativo)
     * @return bool True si el inventario se actualizó correctamente
     */
    public function actualizarStock(string $codigo, int $cantidad): bool
    {
        try {
            $sql = "UPDATE {$this->tabla} 
                    SET stock_actual = stock_actual + ? 
                    WHERE codigo_producto = ? AND (stock_actual + ?) >= 0";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([$cantidad, $codigo, $cantidad]);
        } catch (PDOException $e) {
            error_log("Error en ProductosModel::actualizarStock: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtener listado de productos que se encuentren por debajo de su stock mínimo
     *
     * @return array Productos en alerta de reposición
     */
    public function obtenerBajoStock(): array
    {
        try {
            $sql = "SELECT * FROM {$this->tabla} 
                    WHERE activo = 1 
                    AND stock_actual <= stock_minimo 
                    ORDER BY stock_actual ASC";
            $stmt = $this->pdo->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en ProductosModel::obtenerBajoStock: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtener todos los clientes activos del gimnasio para asociarlos a una venta
     *
     * @return array Listado de clientes con su cédula y nombre completo
     */
    public function obtenerClientes(): array
    {
        try {
            $sql = "SELECT c.cedula_cliente, p.nombre, p.apellido 
                    FROM cliente c 
                    INNER JOIN persona p ON c.cedula_cliente = p.cedula_persona 
                    WHERE p.activo = 1 
                    ORDER BY p.nombre ASC, p.apellido ASC";
            $stmt = $this->pdo->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en ProductosModel::obtenerClientes: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Registrar la venta de uno o múltiples productos en una transacción segura.
     * Realiza verificaciones rigurosas de existencia y de stock suficiente.
     *
     * @param string|null $cedulaCliente Cédula del cliente (puede ser null)
     * @param string $metodoPago Método de pago ('Efectivo', 'Transferencia', etc.)
     * @param array $items Listado de productos comprados conteniendo ['codigo', 'cantidad']
     * @return array Arreglo con el resultado de la operación, estado y detalles de factura
     */
    public function registrarVentaMultiplesProductos(?string $cedulaCliente, string $metodoPago, array $items): array
    {
        if (empty($items)) {
            return ['success' => false, 'message' => 'No se han especificado productos para la venta.'];
        }

        try {
            // Iniciar transacción de base de datos
            $this->pdo->beginTransaction();

            $detallesVenta = [];
            $montoTotalVenta = 0;

            // Verificar si el cliente existe (en caso de que se envíe una cédula)
            if (!empty($cedulaCliente)) {
                $sqlCliente = "SELECT COUNT(*) FROM cliente WHERE cedula_cliente = ?";
                $stmtCliente = $this->pdo->prepare($sqlCliente);
                $stmtCliente->execute([$cedulaCliente]);
                if ($stmtCliente->fetchColumn() == 0) {
                    $this->pdo->rollBack();
                    return ['success' => false, 'message' => "El cliente con cédula '{$cedulaCliente}' no está registrado."];
                }
            } else {
                $cedulaCliente = null; // Guardar NULL en DB si no se define
            }

            // Primer ciclo: Validar que todos los productos existan y tengan stock suficiente
            foreach ($items as $item) {
                $codigo = $item['codigo'];
                $cantidad = floatval($item['cantidad']);

                if ($cantidad <= 0) {
                    $this->pdo->rollBack();
                    return ['success' => false, 'message' => 'La cantidad a vender debe ser mayor que cero.'];
                }

                // Obtener datos actuales del producto directamente de la DB
                $sqlProd = "SELECT nombre, precio_venta, stock_actual, activo FROM {$this->tabla} WHERE codigo_producto = ? LIMIT 1";
                $stmtProd = $this->pdo->prepare($sqlProd);
                $stmtProd->execute([$codigo]);
                $prod = $stmtProd->fetch(PDO::FETCH_ASSOC);

                if (!$prod || $prod['activo'] == 0) {
                    $this->pdo->rollBack();
                    return ['success' => false, 'message' => "El producto con código '{$codigo}' no existe o está inactivo."];
                }

                if ($prod['stock_actual'] < $cantidad) {
                    $this->pdo->rollBack();
                    return [
                        'success' => false, 
                        'message' => "Stock insuficiente para '{$prod['nombre']}'. Stock actual: {$prod['stock_actual']}, solicitado: {$cantidad}."
                    ];
                }

                $montoItem = $prod['precio_venta'] * $cantidad;
                $montoTotalVenta += $montoItem;

                $detallesVenta[] = [
                    'codigo_producto' => $codigo,
                    'nombre' => $prod['nombre'],
                    'precio_unitario' => floatval($prod['precio_venta']),
                    'cantidad_vendida' => $cantidad,
                    'monto_total' => $montoItem
                ];
            }

            // Segundo ciclo: Registrar cada venta en 'venta_producto'
            // El trigger 'tr_descontar_stock_venta' se encargará automáticamente de actualizar el stock
            $sqlInsert = "INSERT INTO venta_producto (codigo_producto, cedula_cliente, cantidad_vendida, monto_total, metodo_pago, fecha) 
                          VALUES (:codigo, :cedula, :cantidad, :monto, :metodo, NOW())";
            $stmtInsert = $this->pdo->prepare($sqlInsert);

            $idsVenta = [];
            foreach ($detallesVenta as &$detalle) {
                $stmtInsert->execute([
                    'codigo'   => $detalle['codigo_producto'],
                    'cedula'   => $cedulaCliente,
                    'cantidad' => $detalle['cantidad_vendida'],
                    'monto'    => $detalle['monto_total'],
                    'metodo'   => $metodoPago
                ]);
                $idsVenta[] = $this->pdo->lastInsertId();
            }

            // Confirmar transacción
            $this->pdo->commit();

            return [
                'success' => true,
                'message' => '✅ Venta registrada y procesada con éxito.',
                'comprobante' => [
                    'nro_transacciones' => $idsVenta,
                    'cedula_cliente' => $cedulaCliente,
                    'metodo_pago' => $metodoPago,
                    'fecha' => date('Y-m-d H:i:s'),
                    'items' => $detallesVenta,
                    'total' => $montoTotalVenta
                ]
            ];

        } catch (PDOException $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            error_log("Error en ProductosModel::registrarVentaMultiplesProductos: " . $e->getMessage());
            return ['success' => false, 'message' => '❌ Error de base de datos al registrar la venta. Contacte soporte.'];
        }
    }
}