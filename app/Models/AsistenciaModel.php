<?php

namespace App\Models;

class AsistenciaModel extends BaseModel
{
    /**
     * Buscar clientes por cédula o nombre (solo primer nombre)
     */
    public function buscarClientes(string $termino): array
    {
        $termino = "%{$termino}%";
        $sql = "SELECT 
                    p.cedula AS cedula_persona,
                    p.nombre AS nombre,
                    p.correo,
                    p.telefono,
                    m.fecha_fin
                FROM cliente c
                INNER JOIN persona p ON c.cedula = p.cedula
                INNER JOIN membresia m ON m.cedula_cliente = c.cedula
                WHERE (p.cedula LIKE ? OR p.nombre LIKE ?)
                  AND m.fecha_fin >= CURDATE()
                  AND m.id_estado = 1
                ORDER BY p.nombre
                LIMIT 50";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$termino, $termino]);
        return $stmt->fetchAll();
    }

    /**
     * Registrar entrada (usa NOW() de MySQL para evitar desfase horario)
     */
    public function registrarEntrada(string $cedula, ?string $hora = null): array
    {
        // Verificar cliente y membresía activa
        $stmt = $this->pdo->prepare("SELECT p.cedula AS cedula_persona, CONCAT(p.nombre, ' ', p.apellido) as nombre
                                    FROM persona p
                                    JOIN cliente c ON p.cedula = c.cedula
                                    JOIN membresia m ON m.cedula_cliente = c.cedula
                                    WHERE p.cedula = ? AND m.fecha_fin >= CURDATE() AND m.id_estado = 1");
        $stmt->execute([$cedula]);
        $cliente = $stmt->fetch();
        if (!$cliente) {
            return ['success' => false, 'message' => 'Cliente no encontrado o membresía inactiva/vencida.'];
        }

        if ($hora) {
            // Hora personalizada: se combina con la fecha actual de MySQL
            $sql = "INSERT INTO asistencia_gimnasio (cedula_persona, fecha, tipo) 
                    VALUES (?, CONCAT(CURDATE(), ' ', ?), 'Entrada')";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$cedula, $hora]);
            $fechaHora = date('Y-m-d') . ' ' . $hora; // solo para respuesta
        } else {
            // Usar NOW() de MySQL
            $sql = "INSERT INTO asistencia_gimnasio (cedula_persona, fecha, tipo) 
                    VALUES (?, NOW(), 'Entrada')";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$cedula]);
            // Obtener la fecha real insertada (con zona horaria de MySQL)
            $fechaHora = $this->pdo->lastInsertId() ? date('Y-m-d H:i:s') : '';
        }
        $id = $this->pdo->lastInsertId();

        return [
            'success' => true,
            'id' => $id,
            'fecha' => $fechaHora,
            'cedula' => $cedula,
            'nombre' => $cliente['nombre']
        ];
    }

    /**
     * Obtener todas las entradas de hoy (con nombre completo)
     */
    public function obtenerEntradasHoy(): array
    {
        $sql = "SELECT a.id_asistencia, a.cedula_persona AS `cedula_cliente`, a.fecha,
                       CONCAT(p.nombre, ' ', p.apellido) AS nombre_cliente
                FROM asistencia_gimnasio a
                JOIN cliente c ON a.cedula_persona = c.cedula
                JOIN persona p ON c.cedula = p.cedula
                WHERE DATE(a.fecha) = CURDATE() AND a.tipo = 'Entrada'
                ORDER BY a.fecha DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Buscar entradas en la tabla (por hora, cédula o nombre)
     */
    public function buscarEntradas(string $termino): array
    {
        $termino = "%{$termino}%";
        $sql = "SELECT a.id_asistencia, a.cedula_persona AS `cedula_cliente`, a.fecha,
                       CONCAT(p.nombre, ' ', p.apellido) AS nombre_cliente
                FROM asistencia_gimnasio a
                JOIN cliente c ON a.cedula_persona = c.cedula
                JOIN persona p ON c.cedula = p.cedula
                WHERE DATE(a.fecha) = CURDATE() AND a.tipo = 'Entrada'
                  AND (TIME(a.fecha) LIKE ? OR a.cedula_persona LIKE ? OR p.nombre LIKE ? OR p.apellido LIKE ?)
                ORDER BY a.fecha DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$termino, $termino, $termino, $termino]);
        return $stmt->fetchAll();
    }

    /**
     * Actualizar hora de una entrada
     */
    public function actualizarEntrada(int $id, string $nuevaHora): bool
    {
        $fecha = date('Y-m-d') . ' ' . $nuevaHora;
        $stmt = $this->pdo->prepare("UPDATE asistencia_gimnasio SET fecha = ? WHERE id_asistencia = ?");
        return $stmt->execute([$fecha, $id]);
    }

    /**
     * Eliminar una entrada
     */
    public function eliminarEntrada(int $id): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM asistencia_gimnasio WHERE id_asistencia = ?");
        return $stmt->execute([$id]);
    }

    // ========== MÉTRICAS DE OCUPACIÓN ==========
    public function obtenerOcupacionPorFranjas(string $fecha): array
    {
        $franjas = [
            ['nombre' => 'Mañana (6am - 12pm)', 'inicio' => '06:00:00', 'fin' => '12:00:00'],
            ['nombre' => 'Tarde (12pm - 3pm)',   'inicio' => '12:00:00', 'fin' => '15:00:00'],
            ['nombre' => 'Media Tarde (3pm - 6pm)', 'inicio' => '15:00:00', 'fin' => '18:00:00'],
            ['nombre' => 'Noche (6pm - 10pm)',   'inicio' => '18:00:00', 'fin' => '22:00:00'],
        ];
        $resultado = [];
        foreach ($franjas as $franja) {
            $sql = "SELECT COUNT(*) as total
                    FROM asistencia_gimnasio
                    WHERE DATE(fecha) = ?
                      AND TIME(fecha) >= ?
                      AND TIME(fecha) < ?
                      AND tipo = 'Entrada'";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$fecha, $franja['inicio'], $franja['fin']]);
            $total = (int)$stmt->fetchColumn();
            $resultado[] = [
                'franja' => $franja['nombre'],
                'total'  => $total,
                'inicio' => $franja['inicio'],
                'fin'    => $franja['fin']
            ];
        }
        return $resultado;
    }

    public function obtenerEntradasPorFecha(string $fecha): array
    {
        $sql = "SELECT a.id_asistencia, a.cedula_persona AS `cedula_cliente`, a.fecha,
                       CONCAT(p.nombre, ' ', p.apellido) AS nombre_cliente
                FROM asistencia_gimnasio a
                JOIN cliente c ON a.cedula_persona = c.cedula
                JOIN persona p ON c.cedula = p.cedula
                WHERE DATE(a.fecha) = ? AND a.tipo = 'Entrada'
                ORDER BY a.fecha ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$fecha]);
        return $stmt->fetchAll();
    }

    public function obtenerTotalesPorRango(string $fechaInicio, string $fechaFin): array
    {
        $sql = "SELECT DATE(a.fecha) AS dia, COUNT(*) AS total_asistencias
            FROM asistencia_gimnasio a
            WHERE DATE(a.fecha) BETWEEN ? AND ? AND a.tipo = 'Entrada'
            GROUP BY DATE(a.fecha)
            ORDER BY dia ASC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$fechaInicio, $fechaFin]);
        return $stmt->fetchAll();
    }
}
