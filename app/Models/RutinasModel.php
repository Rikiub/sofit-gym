<?php

namespace App\Models;

use PDO;

class RutinasModel extends BaseModel
{
    // =========================================================================
    // CRUD: TABLA `rutina`
    // =========================================================================

    /**
     * Obtener todas las rutinas con el nombre de su dificultad
     * @return array
     */
    public function obtenerTodasLasRutinas(): array
    {
        $sql = "SELECT r.*, d.nombre AS nombre_dificultad
                FROM rutina r
                INNER JOIN tipo_dificultad d ON r.id_dificultad = d.id_dificultad
                ORDER BY r.id_rutina DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener una rutina por su ID primario
     * @param int $id
     * @return array|null
     */
    public function obtenerRutinaPorId(int $id): ?array
    {
        $sql = "SELECT r.*, d.nombre AS nombre_dificultad
                FROM rutina r
                INNER JOIN tipo_dificultad d ON r.id_dificultad = d.id_dificultad
                WHERE r.id_rutina = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        return $res ?: null;
    }

    /**
     * Buscar rutinas por término (nombre o descripción)
     * @param string $termino
     * @return array
     */
    public function buscarRutinas(string $termino): array
    {
        $terminoLike = "%{$termino}%";
        $sql = "SELECT r.*, d.nombre AS nombre_dificultad
                FROM rutina r
                INNER JOIN tipo_dificultad d ON r.id_dificultad = d.id_dificultad
                WHERE r.nombre LIKE ? OR r.descripcion LIKE ?
                ORDER BY r.nombre ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$terminoLike, $terminoLike]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Crear una nueva rutina
     * @param array $datos ['id_dificultad', 'nombre', 'descripcion', 'objetivo', 'duracion_semanas']
     * @return bool
     */
    public function crearRutina(array $datos): bool
    {
        try {
            $this->pdoInsert('rutina', [
                'id_dificultad'    => $datos['id_dificultad'],
                'nombre'           => $datos['nombre'],
                'descripcion'      => $datos['descripcion'] ?? null,
                'objetivo'         => $datos['objetivo'] ?? null,
                'duracion_semanas' => $datos['duracion_semanas'] ?? null
            ]);
            return true;
        } catch (\PDOException $e) {
            // Manejo de errores o logs según requiera el sistema
            return false;
        }
    }

    /**
     * Actualizar una rutina existente
     * @param int $id
     * @param array $datos
     * @return bool
     */
    public function actualizarRutina(int $id, array $datos): bool
    {
        try {
            $columnasAActualizar = [];
            if (isset($datos['id_dificultad'])) $columnasAActualizar['id_dificultad'] = $datos['id_dificultad'];
            if (isset($datos['nombre'])) $columnasAActualizar['nombre'] = $datos['nombre'];
            if (isset($datos['descripcion'])) $columnasAActualizar['descripcion'] = $datos['descripcion'];
            if (isset($datos['objetivo'])) $columnasAActualizar['objetivo'] = $datos['objetivo'];
            if (isset($datos['duracion_semanas'])) $columnasAActualizar['duracion_semanas'] = $datos['duracion_semanas'];

            if (empty($columnasAActualizar)) return false;

            $this->pdoUpdate('rutina', $columnasAActualizar, ['id_rutina' => $id]);
            return true;
        } catch (\PDOException $e) {
            return false;
        }
    }

    /**
     * Eliminar una rutina
     * @param int $id
     * @return bool
     */
    public function eliminarRutina(int $id): bool
    {
        try {
            $filasAfectadas = $this->pdoDelete('rutina', ['id_rutina' => $id]);
            return $filasAfectadas > 0;
        } catch (\PDOException $e) {
            return false;
        }
    }


    // =========================================================================
    // CRUD: TABLA `rutina_asignada`
    // =========================================================================

    /**
     * Obtener todas las rutinas asignadas con nombres de clientes y rutinas
     * @return array
     */
    public function obtenerTodasLasAsignaciones(): array
    {
        $sql = "SELECT ra.*, 
                       CONCAT(p.nombre, ' ', p.apellido) AS nombre_cliente,
                       r.nombre AS nombre_rutina,
                       d.nombre AS nombre_dificultad
                FROM rutina_asignada ra
                INNER JOIN cliente c ON ra.cedula_cliente = c.cedula_cliente
                INNER JOIN persona p ON c.cedula_cliente = p.cedula_persona
                INNER JOIN rutina r ON ra.id_rutina = r.id_rutina
                LEFT JOIN tipo_dificultad d ON r.id_dificultad = d.id_dificultad
                ORDER BY ra.fecha_asignacion DESC, ra.id_asignacion DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener una asignación específica de rutina por su ID primario
     * @param int $idAsignacion
     * @return array|null
     */
    public function obtenerAsignacionPorId(int $idAsignacion): ?array
    {
        $sql = "SELECT ra.*, 
                       CONCAT(p.nombre, ' ', p.apellido) AS nombre_cliente,
                       r.nombre AS nombre_rutina
                FROM rutina_asignada ra
                INNER JOIN cliente c ON ra.cedula_cliente = c.cedula_cliente
                INNER JOIN persona p ON c.cedula_cliente = p.cedula_persona
                INNER JOIN rutina r ON ra.id_rutina = r.id_rutina
                WHERE ra.id_asignacion = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$idAsignacion]);
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        return $res ?: null;
    }

    /**
     * Obtener el historial de rutinas asignadas a un cliente específico
     * @param string $cedulaCliente
     * @return array
     */
    public function obtenerAsignacionesPorCliente(string $cedulaCliente): array
    {
        $sql = "SELECT ra.*, r.nombre AS nombre_rutina, d.nombre AS nombre_dificultad
                FROM rutina_asignada ra
                INNER JOIN rutina r ON ra.id_rutina = r.id_rutina
                INNER JOIN tipo_dificultad d ON r.id_dificultad = d.id_dificultad
                WHERE ra.cedula_cliente = ?
                ORDER BY ra.fecha_asignacion DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$cedulaCliente]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Asignar una rutina a un cliente
     * @param array $datos ['cedula_cliente', 'id_rutina', 'fecha_asignacion', 'fecha_inicio', 'fecha_fin', 'estado', 'progreso']
     * @return bool
     */
    public function asignarRutina(array $datos): bool
    {
        try {
            // Validaciones básicas de integridad de cliente y rutina previo a la inserción
            $checkCliente = $this->pdo->prepare("SELECT 1 FROM cliente WHERE cedula_cliente = ?");
            $checkCliente->execute([$datos['cedula_cliente']]);
            if (!$checkCliente->fetch()) {
                return false; // El cliente no existe o está inactivo
            }

            $checkRutina = $this->pdo->prepare("SELECT 1 FROM rutina WHERE id_rutina = ?");
            $checkRutina->execute([$datos['id_rutina']]);
            if (!$checkRutina->fetch()) {
                return false; // La rutina no existe
            }

            $this->pdoInsert('rutina_asignada', [
                'cedula_cliente'   => $datos['cedula_cliente'],
                'id_rutina'        => $datos['id_rutina'],
                'fecha_asignacion' => $datos['fecha_asignacion'] ?? date('Y-m-d'),
                'fecha_inicio'     => $datos['fecha_inicio'] ?? null,
                'fecha_fin'        => $datos['fecha_fin'] ?? null,
                'estado'           => $datos['estado'] ?? 'Activa',
                'progreso'         => $datos['progreso'] ?? 0.00
            ]);
            return true;
        } catch (\PDOException $e) {
            return false;
        }
    }

    /**
     * Actualizar una asignación de rutina (estado, progreso, fechas, etc.)
     * @param int $idAsignacion
     * @param array $datos
     * @return bool
     */
    public function actualizarAsignacion(int $idAsignacion, array $datos): bool
    {
        try {
            $columnasAActualizar = [];
            if (isset($datos['cedula_cliente'])) $columnasAActualizar['cedula_cliente'] = $datos['cedula_cliente'];
            if (isset($datos['id_rutina'])) $columnasAActualizar['id_rutina'] = $datos['id_rutina'];
            if (isset($datos['fecha_asignacion'])) $columnasAActualizar['fecha_asignacion'] = $datos['fecha_asignacion'];
            if (isset($datos['fecha_inicio'])) $columnasAActualizar['fecha_inicio'] = $datos['fecha_inicio'];
            if (isset($datos['fecha_fin'])) $columnasAActualizar['fecha_fin'] = $datos['fecha_fin'];
            if (isset($datos['estado'])) $columnasAActualizar['estado'] = $datos['estado'];
            if (isset($datos['progreso'])) $columnasAActualizar['progreso'] = $datos['progreso'];

            if (empty($columnasAActualizar)) return false;

            $this->pdoUpdate('rutina_asignada', $columnasAActualizar, ['id_asignacion' => $idAsignacion]);
            return true;
        } catch (\PDOException $e) {
            return false;
        }
    }

    /**
     * Eliminar la asignación de una rutina
     * @param int $idAsignacion
     * @return bool
     */
    public function eliminarAsignacion(int $idAsignacion): bool
    {
        try {
            $filasAfectadas = $this->pdoDelete('rutina_asignada', ['id_asignacion' => $idAsignacion]);
            return $filasAfectadas > 0;
        } catch (\PDOException $e) {
            return false;
        }
    }

    // =========================================================================
    // MÉTODOS AUXILIARES / CATÁLOGOS
    // =========================================================================

    /**
     * Obtener listado de dificultades para poblar inputs de tipo select
     * @return array
     */
    public function obtenerDificultades(): array
    {
        $stmt = $this->pdo->prepare("SELECT id_dificultad, nombre FROM tipo_dificultad ORDER BY id_dificultad");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
