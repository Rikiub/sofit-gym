<?php

namespace App\Models;

use PDO;
use PDOException;

class VentasModel extends Model
{
    private string $tabla = 'venta_producto';

    // =========================================================
    // CRUD DE VENTAS
    // =========================================================

    /**
     * Obtener todo el historial de ventas registradas.
     */
    public function obtenerVentas(): array
    {
        try {
            // CORRECCIÓN: Se cambió "m.nombre AS metodo_pago" por "m.nombre AS nombre_metodo"
            // para coincidir con la variable que espera ventas.php
            $sql = "SELECT vp.*, p.nombre AS nombre_producto, 
                           per.nombre AS nombre_cliente, per.apellido AS apellido_cliente,
                           m.nombre AS nombre_metodo
                    FROM {$this->tabla} vp
                    INNER JOIN producto p ON vp.codigo_producto = p.codigo_producto
                    LEFT JOIN persona per ON vp.cedula_cliente = per.cedula
                    LEFT JOIN metodo_pago m ON vp.id_metodo = m.id_metodo
                    ORDER BY vp.id_venta DESC"; 
            $stmt = $this->db->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en VentasModel::obtenerVentas: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtener el detalle de una venta específica por su ID.
     */
    public function obtenerVentaPorId(int $idVenta): ?array
    {
        try {
            // CORRECCIÓN: También se actualiza aquí el alias a nombre_metodo
            $sql = "SELECT vp.*, p.nombre AS nombre_producto, p.precio_venta,
                           per.nombre AS nombre_cliente, per.apellido AS apellido_cliente,
                           m.nombre AS nombre_metodo
                    FROM {$this->tabla} vp
                    INNER JOIN producto p ON vp.codigo_producto = p.codigo_producto
                    LEFT JOIN persona per ON vp.cedula_cliente = per.cedula
                    LEFT JOIN metodo_pago m ON vp.id_metodo = m.id_metodo
                    WHERE vp.id_venta = ? LIMIT 1";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$idVenta]);
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultado ?: null;
        } catch (PDOException $e) {
            error_log("Error en VentasModel::obtenerVentaPorId: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Crear un registro individual de venta de forma manual.
     */
    public function crearVenta(array $datos): bool
    {
        try {
            $this->db->dbInsert($this->tabla, $datos);
            return true;
        } catch (PDOException $e) {
            error_log("Error en VentasModel::crearVenta: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Actualizar los datos de una venta existente (ej. corregir método de pago).
     */
    public function actualizarVenta(int $idVenta, array $datos): bool
    {
        try {
            $this->db->dbUpdate($this->tabla, $datos, ['id_venta' => $idVenta]);
            return true;
        } catch (PDOException $e) {
            error_log("Error en VentasModel::actualizarVenta: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Eliminar un registro de venta.
     */
    public function eliminarVenta(int $idVenta): bool
    {
        try {
            $resultado = $this->db->dbDelete($this->tabla, ['id_venta' => $idVenta]);
            // En lugar de verificar si es > 0, simplemente verificamos que la operación no haya fallado
            return $resultado !== false;
        } catch (PDOException $e) {
            error_log("Error en VentasModel::eliminarVenta: " . $e->getMessage());
            return false;
        }
    }

    // =========================================================
    // PROCESOS Y TRANSACCIONES
    // =========================================================

    /**
     * Obtener todos los clientes activos del gimnasio usando las columnas de cédula reales
     */
    public function obtenerClientes(): array
    {
        try {
            // CORRECCIÓN: Se cambió "c.cedula" por "c.cedula AS cedula_cliente"
            // Esto elimina los Warnings de array index undefined en ventas.php
            $sql = "SELECT c.cedula AS cedula_cliente, p.nombre, p.apellido 
                    FROM cliente c 
                    INNER JOIN persona p ON c.cedula = p.cedula 
                    WHERE p.activo = 1 
                    ORDER BY p.nombre ASC, p.apellido ASC";
            $stmt = $this->db->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en VentasModel::obtenerClientes: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Registrar la venta de uno o múltiples productos en una transacción segura.
     */
    public function registrarVentaMultiplesProductos(?string $cedulaCliente, ?int $idMetodo, array $items): array
    {
        if (empty($items)) {
            return ['success' => false, 'message' => 'No se han especificado productos para la venta.'];
        }

        try {
            $this->db->beginTransaction();

            $detallesVenta = [];
            $montoTotalVenta = 0;

            if (!empty($cedulaCliente)) {
                $sqlCliente = "SELECT COUNT(*) FROM cliente WHERE cedula = ?";
                $stmtCliente = $this->db->prepare($sqlCliente);
                $stmtCliente->execute([$cedulaCliente]);
                if ($stmtCliente->fetchColumn() == 0) {
                    $this->db->rollBack();
                    return ['success' => false, 'message' => "El cliente con cédula '{$cedulaCliente}' no está registrado."];
                }
            } else {
                $cedulaCliente = null;
            }

            if (!empty($idMetodo)) {
                $sqlMetodo = "SELECT COUNT(*) FROM metodo_pago WHERE id_metodo = ?";
                $stmtMetodo = $this->db->prepare($sqlMetodo);
                $stmtMetodo->execute([$idMetodo]);
                if ($stmtMetodo->fetchColumn() == 0) {
                    $this->db->rollBack();
                    return ['success' => false, 'message' => "El método de pago especificado no es válido."];
                }
            } else {
                $idMetodo = null;
            }

            foreach ($items as $item) {
                $codigo = $item['codigo'];
                $cantidad = floatval($item['cantidad']);

                if ($cantidad <= 0) {
                    $this->db->rollBack();
                    return ['success' => false, 'message' => 'La cantidad a vender debe ser mayor que cero.'];
                }

                $sqlProd = "SELECT nombre, precio_venta, stock_actual, activo FROM producto WHERE codigo_producto = ? LIMIT 1";
                $stmtProd = $this->db->prepare($sqlProd);
                $stmtProd->execute([$codigo]);
                $prod = $stmtProd->fetch(PDO::FETCH_ASSOC);

                if (!$prod || $prod['activo'] == 0) {
                    $this->db->rollBack();
                    return ['success' => false, 'message' => "El producto con código '{$codigo}' no existe o está inactivo."];
                }

                if ($prod['stock_actual'] < $cantidad) {
                    $this->db->rollBack();
                    return [
                        'success' => false,
                        'message' => "Stock insuficiente para '{$prod['nombre']}'. Inventario actual: {$prod['stock_actual']}, solicitado: {$cantidad}."
                    ];
                }

                $montoItem = $prod['precio_venta'] * $cantidad;
                $montoTotalVenta += $montoItem;

                $detallesVenta[] = [
                    'codigo_producto'  => $codigo,
                    'nombre'           => $prod['nombre'],
                    'precio_unitario'  => floatval($prod['precio_venta']),
                    'cantidad_vendida' => $cantidad,
                    'monto_total'      => $montoItem
                ];
            }

            $sqlInsert = "INSERT INTO {$this->tabla} (id_metodo, codigo_producto, cedula_cliente, cantidad_vendida, monto_total) 
                          VALUES (:id_metodo, :codigo, :cedula, :cantidad, :monto)";
            $stmtInsert = $this->db->prepare($sqlInsert);

            $idsVenta = [];
            foreach ($detallesVenta as &$detalle) {
                $stmtInsert->execute([
                    'id_metodo' => $idMetodo,
                    'codigo'    => $detalle['codigo_producto'],
                    'cedula'    => $cedulaCliente,
                    'cantidad'  => $detalle['cantidad_vendida'],
                    'monto'     => $detalle['monto_total']
                ]);
                $idsVenta[] = $this->db->lastInsertId();
            }

            $this->db->commit();

            return [
                'success' => true,
                'message' => '✅ Venta registrada y procesada con éxito.',
                'comprobante' => [
                    'nro_transacciones' => $idsVenta,
                    'cedula_cliente'    => $cedulaCliente,
                    'id_metodo'         => $idMetodo,
                    'fecha'             => date('Y-m-d H:i:s'),
                    'items'             => $detallesVenta,
                    'total'             => $montoTotalVenta
                ]
            ];
        } catch (PDOException $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            error_log("Error en VentasModel::registrarVentaMultiplesProductos: " . $e->getMessage());
            return ['success' => false, 'message' => '❌ Error de base de datos al procesar la venta. Contacte soporte técnico.'];
        }
    }

    // =========================================================
    // REPORTES DE VENTAS
    // =========================================================

    /**
     * Obtiene los productos más vendidos para el módulo de reportes.
     */
    public function obtenerProductosMasVendidos(?string $fechaInicio = null, ?string $fechaFin = null): array
    {
        $sql = "SELECT 
                    vp.codigo_producto, 
                    p.nombre AS nombre_producto, 
                    SUM(vp.cantidad_vendida) AS total_vendido, 
                    AVG(vp.monto_total / vp.cantidad_vendida) AS precio_unitario_promedio,
                    SUM(vp.monto_total) AS ingreso_total
                FROM {$this->tabla} vp
                INNER JOIN producto p ON vp.codigo_producto = p.codigo_producto";

        $where = [];
        $params = [];

        if (!empty($fechaInicio) && !empty($fechaFin)) {
            $where[] = "vp.fecha BETWEEN :fechaInicio AND :fechaFin";
            $params['fechaInicio'] = $fechaInicio . " 00:00:00";
            $params['fechaFin'] = $fechaFin . " 23:59:59";
        }

        if (!empty($where)) {
            $sql .= " WHERE " . implode(" AND ", $where);
        }

        $sql .= " GROUP BY vp.codigo_producto, p.nombre
                  ORDER BY total_vendido DESC";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en VentasModel::obtenerProductosMasVendidos: " . $e->getMessage());
            return [];
        }
    }

    /**
     * ¿Qué productos nunca han sido vendidos?
     */
    public function obtenerProductosSinVender(): array
    {
        try {
            $sql = "SELECT codigo_producto, nombre, stock_actual 
                    FROM producto 
                    WHERE activo = 1 AND codigo_producto NOT IN (
                        SELECT DISTINCT codigo_producto FROM {$this->tabla}
                    )";
            $stmt = $this->db->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en VentasModel::obtenerProductosSinVender: " . $e->getMessage());
            return [];
        }
    }
}