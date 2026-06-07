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
            // Manejo de errores consistente
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
            // Valores por defecto seguros que mapean con la definición SQL
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
            // Filtrar campos para evitar la alteración accidental de la clave primaria
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
                // Borrado físico usando pdoDelete de BaseModel
                $filasAfectadas = $this->pdoDelete($this->tabla, ['codigo_producto' => $codigo]);
                return $filasAfectadas > 0;
            } else {
                // Borrado lógico (recomendado por claves foráneas en venta_producto)
                return $this->actualizar($codigo, ['activo' => 0]);
            }
        } catch (PDOException $e) {
            error_log("Error en ProductosModel::eliminar: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Actualizar únicamente el inventario/stock de un producto específico
     * Útil tras realizar ventas en venta_producto
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
}
