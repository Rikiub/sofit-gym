<?php

namespace App\Models;

use PDO;
use PDOException;

class ProductoModel extends Model
{
    private string $tabla = 'producto';

    /**
     * Obtener todos los productos activos de la base de datos.
     */
    public function obtenerTodos(?string $termino = null): array
    {
        try {
            if (!empty($termino)) {
                $sql = "SELECT p.*, c.nombre AS nombre_categoria, u.nombre AS nombre_unidad 
                        FROM {$this->tabla} p
                        LEFT JOIN categoria_producto c ON p.id_categoria = c.id_categoria
                        LEFT JOIN unidad_medida u ON p.id_unidad = u.id_unidad
                        WHERE p.activo = 1
                        AND (p.codigo_producto LIKE :termino 
                             OR p.nombre LIKE :termino 
                             OR c.nombre LIKE :termino)
                        ORDER BY p.nombre ASC";
                $stmt = $this->db->prepare($sql);
                $stmt->execute(['termino' => "%{$termino}%"]);
            } else {
                $sql = "SELECT p.*, c.nombre AS nombre_categoria, u.nombre AS nombre_unidad 
                        FROM {$this->tabla} p 
                        LEFT JOIN categoria_producto c ON p.id_categoria = c.id_categoria
                        LEFT JOIN unidad_medida u ON p.id_unidad = u.id_unidad
                        WHERE p.activo = 1 
                        ORDER BY p.nombre ASC";
                $stmt = $this->db->query($sql);
            }
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en ProductoModel::obtenerTodos: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtener un producto específico por su código.
     */
    public function obtenerPorCodigo(string $codigo): ?array
    {
        try {
            $sql = "SELECT p.*, c.nombre AS nombre_categoria, u.nombre AS nombre_unidad 
                    FROM {$this->tabla} p
                    LEFT JOIN categoria_producto c ON p.id_categoria = c.id_categoria
                    LEFT JOIN unidad_medida u ON p.id_unidad = u.id_unidad
                    WHERE p.codigo_producto = ? LIMIT 1";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$codigo]);
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultado ?: null;
        } catch (PDOException $e) {
            error_log("Error en ProductoModel::obtenerPorCodigo: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Insertar un nuevo producto.
     */
    public function crear(array $datos): bool
    {
        try {
            $nuevoProducto = [
                'codigo_producto' => $datos['codigo_producto'],
                'id_categoria'    => !empty($datos['id_categoria']) ? intval($datos['id_categoria']) : null,
                'id_unidad'       => !empty($datos['id_unidad']) ? intval($datos['id_unidad']) : null,
                'nombre'          => $datos['nombre'],
                'precio_venta'    => floatval($datos['precio_venta']),
                'stock_minimo'    => isset($datos['stock_minimo']) ? intval($datos['stock_minimo']) : 0,
                'stock_actual'    => isset($datos['stock_actual']) ? intval($datos['stock_actual']) : 0,
                'activo'          => isset($datos['activo']) ? intval($datos['activo']) : 1,
                'imagen'          => $datos['imagen'] ?? null // NUEVO: Guardar la ruta de la imagen
            ];

            $this->db->dbInsert($this->tabla, $nuevoProducto);
            return true;
        } catch (PDOException $e) {
            error_log("Error en ProductoModel::crear: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Actualizar los datos de un producto.
     */
    public function actualizar(string $codigo, array $datos): bool
    {
        try {
            unset($datos['codigo_producto']); // Seguridad: No alterar la clave primaria
            $this->db->dbUpdate($this->tabla, $datos, ['codigo_producto' => $codigo]);
            return true;
        } catch (PDOException $e) {
            error_log("Error en ProductoModel::actualizar: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Eliminar producto (Lógico o físico).
     */
    public function eliminar(string $codigo, bool $fisico = false): bool
    {
        try {
            if ($fisico) {
                $filasAfectadas = $this->db->dbDelete($this->tabla, ['codigo_producto' => $codigo]);
                return $filasAfectadas > 0;
            } else {
                return $this->actualizar($codigo, ['activo' => 0]);
            }
        } catch (PDOException $e) {
            error_log("Error en ProductoModel::eliminar: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Actualizar únicamente el inventario/stock de un producto.
     */
    public function actualizarStock(string $codigo, int $cantidad): bool
    {
        try {
            $sql = "UPDATE {$this->tabla} 
                    SET stock_actual = stock_actual + ? 
                    WHERE codigo_producto = ? AND (stock_actual + ?) >= 0";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$cantidad, $codigo, $cantidad]);
        } catch (PDOException $e) {
            error_log("Error en ProductoModel::actualizarStock: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Aumento del 10% en el precio de los suplementos (Transacción).
     */
    public function aumentarPrecioSuplementos(): array
    {
        try {
            $this->db->beginTransaction();
            
            $sql = "UPDATE {$this->tabla} SET precio_venta = precio_venta * 1.10 WHERE id_categoria = 1";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            
            $this->db->commit();
            return ['success' => true, 'message' => 'Precios de suplementos actualizados correctamente.'];
        } catch (PDOException $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            error_log("Error en ProductoModel::aumentarPrecioSuplementos: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error al intentar actualizar los precios.'];
        }
    }

    /**
     * Obtener listado de productos con stock por debajo del mínimo (Auxiliar de inventario)
     */
    public function obtenerBajoStock(): array
    {
        try {
            $sql = "SELECT p.*, c.nombre AS nombre_categoria, u.nombre AS nombre_unidad 
                    FROM {$this->tabla} p 
                    LEFT JOIN categoria_producto c ON p.id_categoria = c.id_categoria
                    LEFT JOIN unidad_medida u ON p.id_unidad = u.id_unidad
                    WHERE p.activo = 1 
                    AND p.stock_actual <= p.stock_minimo 
                    ORDER BY p.stock_actual ASC";
            $stmt = $this->db->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en ProductoModel::obtenerBajoStock: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtener los datos para el reporte de inventario general.
     */
    public function obtenerReporteInventario(): array
    {
        return $this->obtenerTodos();
    }
}