<?php

namespace App\Models;

class AsistenciaModel extends Model
{
    /**
     * Obtener una entrada de asistencia por su ID
     * @param int $id
     * @return array|null
     */
    public function findCliente(int $id): ?array
    {
        $sql = "SELECT id_asistencia, cedula_persona as cedula, fecha 
            FROM asistencia_gimnasio 
            WHERE id_asistencia = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

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
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$termino, $termino]);
        return $stmt->fetchAll();
    }

    /**
     * Registrar entrada
     */
    public function registrarEntrada(string $cedula, ?string $hora = null): array
    {
        $stmt = $this->db->prepare(
            <<<SQL
                CALL sp_registrar_entrada_cliente(?, ?, @ok, @msg, @id, @fecha)
            SQL
        );
        $stmt->execute([$cedula, $hora]);

        $stmt = $this->db->query(
            <<<SQL
                SELECT
                    @ok as exito,
                    @msg as mensaje,
                    @id as id,
                    @fecha as fecha
            SQL
        );
        $asistencia = $stmt->fetch();

        // Verificar cliente y membresía activa
        $stmt = $this->db->prepare("SELECT p.cedula AS cedula_persona, CONCAT(p.nombre, ' ', p.apellido) as nombre
                                    FROM persona p
                                    JOIN cliente c ON p.cedula = c.cedula
                                    JOIN membresia m ON m.cedula_cliente = c.cedula
                                    WHERE p.cedula = ? AND m.fecha_fin >= CURDATE() AND m.id_estado = 1");
        $stmt->execute([$cedula]);
        $cliente = $stmt->fetch();
        if (!$cliente) {
            return ['success' => false, 'message' => 'Cliente no encontrado o membresía inactiva/vencida.'];
        }

        return [
            'success' => true,
            'id' => $asistencia["id"],
            'fecha' => $asistencia["fecha"],
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
        $stmt = $this->db->prepare($sql);
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
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$termino, $termino, $termino, $termino]);
        return $stmt->fetchAll();
    }

    /**
     * Actualizar hora de una entrada
     */
    public function actualizarEntrada(int $id, string $nuevaHora): bool
    {
        $fecha = date('Y-m-d') . ' ' . $nuevaHora;
        $stmt = $this->db->prepare("UPDATE asistencia_gimnasio SET fecha = ? WHERE id_asistencia = ?");
        return $stmt->execute([$fecha, $id]);
    }

    /**
     * Eliminar una entrada
     */
    public function eliminarEntrada(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM asistencia_gimnasio WHERE id_asistencia = ?");
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
            $stmt = $this->db->prepare($sql);
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
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$fecha]);
        return $stmt->fetchAll();
    }

    public function obtenerTotalesPorRango(string $fechaInicio, string $fechaFin): array
    {
        $stmt = $this->db->prepare("CALL sp_obtener_totales_asistencias_por_rango(?, ?)");
        $stmt->execute([$fechaInicio, $fechaFin]);
        return $stmt->fetchAll();
    }

    // Reportes

    /**
     * Obtener el listado de asistencias filtrado por un rango de fechas
     * diseñado específicamente para el reporte PDF.
     * * @param string|null $fechaInicio Fecha inicial (YYYY-MM-DD)
     * @param string|null $fechaFin Fecha final (YYYY-MM-DD)
     * @return array Listado de asistencias con datos de persona concatenados
     */
    public function obtenerAsistenciasParaReporte(?string $fechaInicio = null, ?string $fechaFin = null): array
    {
        // 1. Estructura base de la consulta SQL (Hace JOIN con cliente y persona para traer los nombres)
        $sql = "SELECT 
                    a.id_asistencia, 
                    a.cedula_persona AS `cedula_cliente`, 
                    a.fecha,
                    CONCAT(p.nombre, ' ', p.apellido) AS nombre_cliente
                FROM asistencia_gimnasio a
                INNER JOIN cliente c ON a.cedula_persona = c.cedula
                INNER JOIN persona p ON c.cedula = p.cedula
                WHERE a.tipo = 'Entrada'";

        $params = [];

        // 2. Si se pasan ambas fechas, aplicamos el filtro de rango al campo DATE(fecha)
        if (!empty($fechaInicio) && !empty($fechaFin)) {
            $sql .= " AND DATE(a.fecha) BETWEEN ? AND ?";
            $params[] = $fechaInicio;
            $params[] = $fechaFin;
        }

        // 3. Ordenamos cronológicamente de forma descendente (más recientes primero)
        $sql .= " ORDER BY a.fecha DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }
}
