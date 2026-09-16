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
     * BC: incluye alias `monto` además de `monto_pago`.
     */
    public function obtenerVentas(): array
    {
        try {
            $sql = "SELECT 
                        vp.id_venta,
                        vp.id_pago,
                        vp.codigo_producto,
                        vp.cantidad_vendida,
                        p.nombre AS nombre_producto,
                        p.precio_venta,
                        (p.precio_venta * vp.cantidad_vendida) AS monto_total,
                        pg.id_metodo,
                        pg.cedula_cliente,
                        pg.monto AS monto_pago,
                        pg.monto AS monto,                 -- BC: alias viejo
                        pg.comprobante_url,
                        pg.estado,
                        pg.fecha_pago,
                        pg.fecha_pago AS fecha,            -- BC: alias viejo
                        per.nombre AS nombre_cliente,
                        per.apellido AS apellido_cliente,
                        mp.nombre AS nombre_metodo
                    FROM {$this->tabla} vp
                    INNER JOIN pago pg ON pg.id_pago = vp.id_pago
                    INNER JOIN producto p ON vp.codigo_producto = p.codigo_producto
                    LEFT JOIN persona per ON pg.cedula_cliente = per.cedula
                    LEFT JOIN metodo_pago mp ON pg.id_metodo = mp.id_metodo
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
     * BC: mismos alias que obtenerVentas().
     */
    public function obtenerVentaPorId(int $idVenta): ?array
    {
        try {
            $sql = "SELECT 
                        vp.id_venta,
                        vp.id_pago,
                        vp.codigo_producto,
                        vp.cantidad_vendida,
                        p.nombre AS nombre_producto,
                        p.precio_venta,
                        (p.precio_venta * vp.cantidad_vendida) AS monto_total,
                        pg.id_metodo,
                        pg.cedula_cliente,
                        pg.monto AS monto_pago,
                        pg.monto AS monto,                 -- BC
                        pg.comprobante_url,
                        pg.estado,
                        pg.fecha_pago,
                        pg.fecha_pago AS fecha,            -- BC
                        per.nombre AS nombre_cliente,
                        per.apellido AS apellido_cliente,
                        mp.nombre AS nombre_metodo
                    FROM {$this->tabla} vp
                    INNER JOIN pago pg ON pg.id_pago = vp.id_pago
                    INNER JOIN producto p ON vp.codigo_producto = p.codigo_producto
                    LEFT JOIN persona per ON pg.cedula_cliente = per.cedula
                    LEFT JOIN metodo_pago mp ON pg.id_metodo = mp.id_metodo
                    WHERE vp.id_venta = ? 
                    LIMIT 1";

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
     * Crear un registro individual de venta.
     *
     * BC: acepta dos formatos de payload:
     *   A) Legacy (esquema viejo de venta_producto):
     *      ['id_metodo'=>.., 'cedula_cliente'=>.., 'codigo_producto'=>..,
     *       'cantidad_vendida'=>.., 'monto_total'=>..]
     *      → se delega a registrarVentaMultiplesProductos() que crea el pago.
     *
     *   B) Nuevo (con id_pago ya existente):
     *      ['id_pago'=>.., 'codigo_producto'=>.., 'cantidad_vendida'=>..]
     *      → inserta directo en venta_producto.
     */
    public function crearVenta(array $datos): bool
    {
        // --- Caso A: payload legacy con id_metodo ---
        if (isset($datos['id_metodo']) && isset($datos['codigo_producto'])) {
            $cantidad = $datos['cantidad_vendida'] ?? $datos['cantidad'] ?? 1;

            $resultado = $this->registrarVentaMultiplesProductos(
                $datos['cedula_cliente'] ?? null,
                (int) $datos['id_metodo'],
                [[
                    'codigo'   => $datos['codigo_producto'],
                    'cantidad' => $cantidad
                ]]
            );
            return $resultado['success'];
        }

        // --- Caso B: ya viene id_pago ---
        try {
            // BC: aceptar 'cantidad' como alias de 'cantidad_vendida'
            if (isset($datos['cantidad']) && !isset($datos['cantidad_vendida'])) {
                $datos['cantidad_vendida'] = $datos['cantidad'];
            }

            // Filtrar solo columnas válidas de venta_producto
            $permitidos = ['id_pago', 'codigo_producto', 'cantidad_vendida'];
            $limpio = array_intersect_key($datos, array_flip($permitidos));

            $this->db->dbInsert($this->tabla, $limpio);
            return true;
        } catch (PDOException $e) {
            error_log("Error en VentasModel::crearVenta: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Actualizar los datos de una venta existente.
     * Acepta nombres viejos y nuevos y los enruta a la tabla correcta.
     */
    public function actualizarVenta(int $idVenta, array $datos): bool
    {
        try {
            // Obtener id_pago asociado
            $stmt = $this->db->prepare("SELECT id_pago FROM {$this->tabla} WHERE id_venta = ?");
            $stmt->execute([$idVenta]);
            $idPago = $stmt->fetchColumn();

            if (!$idPago) {
                return false;
            }

            // BC: alias de columnas legacy
            if (isset($datos['cantidad']) && !isset($datos['cantidad_vendida'])) {
                $datos['cantidad_vendida'] = $datos['cantidad'];
            }
            if (isset($datos['monto_total']) && !isset($datos['monto'])) {
                $datos['monto'] = $datos['monto_total'];
            }
            if (isset($datos['monto_pago']) && !isset($datos['monto'])) {
                $datos['monto'] = $datos['monto_pago'];
            }

            // --- Campos de venta_producto ---
            $ventaFields = array_intersect_key($datos, array_flip([
                'id_pago',
                'codigo_producto',
                'cantidad_vendida'
            ]));

            if (!empty($ventaFields)) {
                $this->db->dbUpdate($this->tabla, $ventaFields, ['id_venta' => $idVenta]);
            }

            // --- Campos de pago ---
            $pagoFields = array_intersect_key($datos, array_flip([
                'id_metodo',
                'cedula_cliente',
                'monto',
                'comprobante_url',
                'estado',
                'fecha_pago'
            ]));

            if (!empty($pagoFields)) {
                $this->db->dbUpdate('pago', $pagoFields, ['id_pago' => $idPago]);
            }

            return true;
        } catch (PDOException $e) {
            error_log("Error en VentasModel::actualizarVenta: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Eliminar un registro de venta.
     *
     * BC: por defecto NO toca `pago` (comportamiento antiguo).
     * Si se pasa $eliminarPagoAsociado = true y no quedan más ventas
     * apuntando a ese pago, también se elimina el pago.
     */
    public function eliminarVenta(int $idVenta, bool $eliminarPagoAsociado = false): bool
    {
        try {
            // Recuperar id_pago antes de borrar
            $stmt = $this->db->prepare("SELECT id_pago FROM {$this->tabla} WHERE id_venta = ?");
            $stmt->execute([$idVenta]);
            $idPago = $stmt->fetchColumn();

            $resultado = $this->db->dbDelete($this->tabla, ['id_venta' => $idVenta]);
            if ($resultado === false) {
                return false;
            }

            // Limpieza opcional del pago huérfano
            if ($eliminarPagoAsociado && $idPago) {
                $stmt = $this->db->prepare("SELECT COUNT(*) FROM {$this->tabla} WHERE id_pago = ?");
                $stmt->execute([$idPago]);
                if ((int) $stmt->fetchColumn() === 0) {
                    $this->db->dbDelete('pago', ['id_pago' => $idPago]);
                }
            }

            return true;
        } catch (PDOException $e) {
            error_log("Error en VentasModel::eliminarVenta: " . $e->getMessage());
            return false;
        }
    }

    // =========================================================
    // PROCESOS Y TRANSACCIONES
    // =========================================================

    /**
     * Obtener todos los clientes activos del gimnasio.
     */
    public function obtenerClientes(): array
    {
        try {
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
     * Inserta primero en `pago` y luego en `venta_producto`.
     *
     * Firma y estructura de retorno IDÉNTICAS a la versión anterior.
     */
    public function registrarVentaMultiplesProductos(?string $cedulaCliente, ?int $idMetodo, array $items): array
    {
        if (empty($items)) {
            return ['success' => false, 'message' => 'No se han especificado productos para la venta.'];
        }

        if (empty($idMetodo)) {
            return ['success' => false, 'message' => 'El método de pago es obligatorio.'];
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

            $sqlMetodo = "SELECT COUNT(*) FROM metodo_pago WHERE id_metodo = ?";
            $stmtMetodo = $this->db->prepare($sqlMetodo);
            $stmtMetodo->execute([$idMetodo]);
            if ($stmtMetodo->fetchColumn() == 0) {
                $this->db->rollBack();
                return ['success' => false, 'message' => "El método de pago especificado no es válido."];
            }

            foreach ($items as $item) {
                $codigo   = $item['codigo'];
                $cantidad = floatval($item['cantidad']);

                if ($cantidad <= 0) {
                    $this->db->rollBack();
                    return ['success' => false, 'message' => 'La cantidad a vender debe ser mayor que cero.'];
                }

                $sqlProd = "SELECT nombre, precio_venta, stock_actual, activo 
                            FROM producto 
                            WHERE codigo_producto = ? 
                            LIMIT 1";
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

            // 1. Insertar en pago
            $sqlPago = "INSERT INTO pago (id_metodo, cedula_cliente, monto, estado, fecha_pago) 
                        VALUES (:id_metodo, :cedula, :monto, 'Pagado', CURDATE())";
            $stmtPago = $this->db->prepare($sqlPago);
            $stmtPago->execute([
                'id_metodo' => $idMetodo,
                'cedula'    => $cedulaCliente,
                'monto'     => $montoTotalVenta
            ]);

            $idPago = $this->db->lastInsertId();

            // 2. Insertar cada producto en venta_producto
            $sqlInsert = "INSERT INTO {$this->tabla} (id_pago, codigo_producto, cantidad_vendida) 
                          VALUES (:id_pago, :codigo, :cantidad)";
            $stmtInsert = $this->db->prepare($sqlInsert);

            $idsVenta = [];
            foreach ($detallesVenta as $detalle) {
                $stmtInsert->execute([
                    'id_pago'  => $idPago,
                    'codigo'   => $detalle['codigo_producto'],
                    'cantidad' => $detalle['cantidad_vendida']
                ]);
                $idsVenta[] = $this->db->lastInsertId();
            }

            $this->db->commit();

            return [
                'success' => true,
                'message' => '✅ Venta registrada y procesada con éxito.',
                'comprobante' => [
                    'id_pago'           => $idPago,
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
     * Obtiene los productos más vendidos.
     * BC: si por alguna razón se pasara una fecha antigua, la lógica
     *     sigue funcionando contra `pg.fecha_pago` (antes `vp.fecha` no existía).
     */
    public function obtenerProductosMasVendidos(?string $fechaInicio = null, ?string $fechaFin = null): array
    {
        $sql = "SELECT 
                    vp.codigo_producto, 
                    p.nombre AS nombre_producto, 
                    SUM(vp.cantidad_vendida) AS total_vendido, 
                    AVG(p.precio_venta) AS precio_unitario_promedio,
                    SUM(vp.cantidad_vendida * p.precio_venta) AS ingreso_total
                FROM {$this->tabla} vp
                INNER JOIN producto p ON vp.codigo_producto = p.codigo_producto
                INNER JOIN pago pg ON pg.id_pago = vp.id_pago";

        $where = [];
        $params = [];

        if (!empty($fechaInicio) && !empty($fechaFin)) {
            $where[] = "pg.fecha_pago BETWEEN :fechaInicio AND :fechaFin";
            $params['fechaInicio'] = $fechaInicio;
            $params['fechaFin']    = $fechaFin;
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
