<?php

namespace App\Models;

use Exception;

class FacturacionModel extends Model
{
    // ===== REGISTRAR PAGO =====
    public function registrarPago(
        string $cedulaCliente,
        float $monto,
        string $metodoPago,
        ?int $planTipoId = null,
    ): array {
        // Obtener cliente
        $cliente = $this->obtenerCliente($cedulaCliente);
        if (!$cliente) {
            throw new Exception("Cliente no encontrado.");
        }

        // VALIDACIÓN DEL MONTO
        if ($monto <= 0) {
            throw new Exception("El monto debe ser mayor a cero.");
        }

        // Obtener membresía actual (si existe)
        $membresiaActual = null;
        if (isset($cliente['id_membresia']) && $cliente['id_membresia'] > 0) {
            $membresiaActual = $this->obtenerMembresiaPorId($cliente['id_membresia']);
        }

        // Determinar el tipo de membresía
        if ($planTipoId !== null && $planTipoId > 0) {
            $tipoMembresiaId = $planTipoId;
        } elseif ($membresiaActual && $membresiaActual['id_tipo']) {
            $tipoMembresiaId = $membresiaActual['id_tipo'];
        } else {
            $tipoMembresiaId = 1;
        }

        // Obtener duración en días desde la tabla tipo_membresia
        $stmt = $this->db->prepare("SELECT duracion_dias FROM tipo_membresia WHERE id_tipo = ?");
        $stmt->execute([$tipoMembresiaId]);
        $duracionDias = $stmt->fetchColumn();
        if (!$duracionDias) {
            $duracionDias = 30; // fallback
        }

        $fechaPago = date('Y-m-d');
        $nuevaFechaVencimiento = date('Y-m-d', strtotime("+{$duracionDias} days"));

        $this->db->beginTransaction();
        try {
            // Insertar nueva membresía
            $stmt = $this->db->prepare("INSERT INTO membresia (id_tipo, id_estado, fecha_inicio, fecha_fin, cedula_cliente) VALUES (?, 1, ?, ?, ?)");
            $stmt->execute([$tipoMembresiaId, $fechaPago, $nuevaFechaVencimiento, $cedulaCliente]);
            $nuevaId = $this->db->lastInsertId();

            // Desactivar membresía anterior si existe
            if ($membresiaActual && $membresiaActual['id_membresia']) {
                $stmt = $this->db->prepare("UPDATE membresia SET id_estado = 2 WHERE id_membresia = ?");
                $stmt->execute([$membresiaActual['id_membresia']]);
            }

            // Obtener ID del método de pago
            $stmtMetodo = $this->db->prepare("SELECT id_metodo FROM metodo_pago WHERE nombre LIKE ? LIMIT 1");
            $stmtMetodo->execute(["%" . $metodoPago . "%"]);
            $idMetodo = $stmtMetodo->fetchColumn() ?: 1;

            // Insertar pago
            $stmt = $this->db->prepare("INSERT INTO pago (id_membresia, id_metodo, monto, estado, fecha_pago) VALUES (?, ?, ?, 'Pagado', ?)");
            $stmt->execute([$nuevaId, $idMetodo, $monto, $fechaPago]);
            $idPago = $this->db->lastInsertId();

            $this->db->commit();
            return [
                'exito' => true,
                'nueva_fecha_vencimiento' => $nuevaFechaVencimiento,
                'id_pago' => $idPago,
                'mensaje' => "Pago registrado. Vigencia hasta {$nuevaFechaVencimiento}"
            ];
        } catch (Exception $e) {
            $this->db->rollBack();
            throw new Exception("Error al registrar pago: " . $e->getMessage());
        }
    }

    // ===== OBTENER TODOS LOS PAGOS =====
    public function obtenerTodosPagos(): array
    {
        $sql = "SELECT 
                    p.id_pago, 
                    m.cedula_cliente, 
                    CONCAT(per.nombre, ' ', per.apellido) AS nombre_cliente,
                    p.monto, 
                    mp.nombre AS metodo_pago, 
                    p.estado AS estado_pago,
                    p.fecha_pago, 
                    m.fecha_fin AS fecha_vencimiento,
                    fn_dias_restantes(m.fecha_fin) AS dias_restantes,
                    fn_estado_membresia(m.fecha_fin, p.estado) as estado_cliente
                FROM pago p
                JOIN membresia m ON p.id_membresia = m.id_membresia
                LEFT JOIN metodo_pago mp ON p.id_metodo = mp.id_metodo
                JOIN cliente c ON m.cedula_cliente = c.cedula
                JOIN persona per ON c.cedula = per.cedula
                ORDER BY p.id_pago DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // ===== BUSCAR PAGOS =====
    public function buscarPagos(string $termino): array
    {
        $termino = "%{$termino}%";
        $sql = "SELECT 
                    p.id_pago, 
                    m.cedula_cliente, 
                    CONCAT(per.nombre, ' ', per.apellido) AS nombre_cliente,
                    p.monto, 
                    mp.nombre AS metodo_pago, 
                    p.estado AS estado_pago,
                    p.fecha_pago, 
                    m.fecha_fin AS fecha_vencimiento,
                    fn_dias_restantes(m.fecha_fin) AS dias_restantes,
                    fn_estado_membresia(m.fecha_fin, p.estado) as estado_cliente
                FROM pago p
                JOIN membresia m ON p.id_membresia = m.id_membresia
                LEFT JOIN metodo_pago mp ON p.id_metodo = mp.id_metodo
                JOIN cliente c ON m.cedula_cliente = c.cedula
                JOIN persona per ON c.cedula = per.cedula
                WHERE p.id_pago LIKE ? 
                   OR m.cedula_cliente LIKE ? 
                   OR per.nombre LIKE ? 
                   OR per.apellido LIKE ?
                ORDER BY p.id_pago DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$termino, $termino, $termino, $termino]);
        return $stmt->fetchAll();
    }

    // ===== ACTUALIZAR PAGO =====
    public function actualizarPago(
        int $idPago,
        float $monto,
        string $metodoPago,
        string $estado,
        string $fechaPago,
        string $fechaVencimiento,
    ): bool {
        $stmtMetodo = $this->db->prepare("SELECT id_metodo FROM metodo_pago WHERE nombre LIKE ? LIMIT 1");
        $stmtMetodo->execute(["%" . $metodoPago . "%"]);
        $idMetodo = $stmtMetodo->fetchColumn() ?: 1;

        $sql = "UPDATE pago SET monto = ?, id_metodo = ?, estado = ?, fecha_pago = ? WHERE id_pago = ?";
        $stmt = $this->db->prepare($sql);
        $res = $stmt->execute([$monto, $idMetodo, $estado, $fechaPago, $idPago]);

        if ($res) {
            $stmtMem = $this->db->prepare("SELECT id_membresia FROM pago WHERE id_pago = ?");
            $stmtMem->execute([$idPago]);
            $idMembresia = $stmtMem->fetchColumn();
            if ($idMembresia) {
                $stmtUpdateMem = $this->db->prepare("UPDATE membresia SET fecha_fin = ? WHERE id_membresia = ?");
                $stmtUpdateMem->execute([$fechaVencimiento, $idMembresia]);
            }
        }
        return $res;
    }

    // ===== ELIMINAR PAGO =====
    public function eliminarPago(int $idPago): bool
    {
        $stmt = $this->db->prepare("DELETE FROM pago WHERE id_pago = ?");
        return $stmt->execute([$idPago]);
    }

    // ===== OBTENER CLIENTES SIMPLES =====
    public function obtenerClientesSimples(): array
    {
        $sql = "SELECT c.cedula AS cedula_cliente, p.nombre AS nombre, p.correo, p.telefono 
                FROM cliente c 
                JOIN persona p ON c.cedula = p.cedula 
                ORDER BY p.nombre";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // ===== OBTENER TIPOS DE MEMBRESÍA =====
    public function obtenerTiposMembresia(): array
    {
        $sql = "SELECT id_tipo, nombre, monto, duracion_dias FROM tipo_membresia ORDER BY id_tipo";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // ===== INGRESOS DEL MES ACTUAL =====
    public function obtenerIngresosMesActual(): array
    {
        $sql = "SELECT 
                    COUNT(p.id_pago) AS total_vendidos,
                    SUM(p.monto) AS total_ingresado
                FROM pago p
                JOIN membresia m ON p.id_membresia = m.id_membresia
                WHERE p.estado = 'Pagado'
                    AND YEAR(p.fecha_pago) = YEAR(CURDATE())
                    AND MONTH(p.fecha_pago) = MONTH(CURDATE())";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetch();
    }

    // ===== MÉTODOS PRIVADOS =====
    private function obtenerCliente(string $cedula): ?array
    {
        $stmt = $this->db->prepare("
            SELECT 
                cedula AS cedula_cliente,
                (SELECT id_membresia FROM membresia WHERE cedula_cliente = cliente.cedula ORDER BY id_membresia DESC LIMIT 1) AS id_membresia 
            FROM cliente 
            WHERE cedula = ?");
        $stmt->execute([$cedula]);
        return $stmt->fetch() ?: null;
    }

    private function obtenerMembresiaPorId(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT id_membresia, id_tipo, fecha_fin, id_estado FROM membresia WHERE id_membresia = ?");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    // ===== REPORTES =====
    public function obtenerPagosPorPeriodo(?string $mes = null, ?string $anio = null): array
    {
        $sql = "SELECT 
                    p.fecha_pago,
                    m.cedula_cliente,
                    CONCAT(per.nombre, ' ', per.apellido) AS nombre_cliente,
                    mp.nombre AS metodo_pago,
                    p.monto
                FROM pago p
                INNER JOIN membresia m ON p.id_membresia = m.id_membresia
                INNER JOIN cliente c ON m.cedula_cliente = c.cedula
                INNER JOIN persona per ON c.cedula = per.cedula
                LEFT JOIN metodo_pago mp ON p.id_metodo = mp.id_metodo";
        $where = [];
        $params = [];
        if (!empty($anio)) {
            $where[] = "YEAR(p.fecha_pago) = :anio";
            $params['anio'] = $anio;
        }
        if (!empty($mes)) {
            $where[] = "MONTH(p.fecha_pago) = :mes";
            $params['mes'] = $mes;
        }
        if (!empty($where)) {
            $sql .= " WHERE " . implode(" AND ", $where);
        }
        $sql .= " ORDER BY p.fecha_pago ASC, p.id_pago ASC";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (\PDOException $e) {
            error_log("Error: " . $e->getMessage());
            return [];
        }
    }

    public function obtenerClientesPorVencer(int $dias): array
    {
        $sql = "SELECT p.cedula, CONCAT(p.nombre, ' ', p.apellido) AS nombre, m.fecha_fin
            FROM cliente c
            JOIN persona p ON c.cedula = p.cedula
            JOIN membresia m ON m.cedula_cliente = c.cedula
            WHERE m.id_estado = 1
              AND m.fecha_fin BETWEEN CURDATE() + INTERVAL 1 DAY AND CURDATE() + INTERVAL ? DAY
            ORDER BY m.fecha_fin ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$dias]);
        return $stmt->fetchAll();
    }

    public function obtenerPagosAtrasados(): array
    {
        $sql = "SELECT p.cedula, CONCAT(p.nombre, ' ', p.apellido) AS nombre, m.fecha_fin
            FROM cliente c
            JOIN persona p ON c.cedula = p.cedula
            JOIN membresia m ON m.cedula_cliente = c.cedula
            WHERE m.id_estado IN (2, 3)
              AND m.fecha_fin < CURDATE()
            ORDER BY m.fecha_fin ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function obtenerNuevosClientes(int $dias): array
    {
        $sql = "SELECT p.cedula, CONCAT(p.nombre, ' ', p.apellido) AS nombre, c.fecha_creacion
            FROM cliente c
            JOIN persona p ON c.cedula = p.cedula
            WHERE c.fecha_creacion >= CURDATE() - INTERVAL ? DAY";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$dias]);
        return $stmt->fetchAll();
    }

    public function obtenerResumenFinancieroSemanal(): array
    {
        $sqlPagos = "SELECT COALESCE(SUM(monto), 0) AS total_membresias
                 FROM pago
                 WHERE fecha_pago >= CURDATE() - INTERVAL 7 DAY";
        $stmt = $this->db->query($sqlPagos);
        $totalMembresias = $stmt->fetchColumn();

        $sqlVentas = "SELECT COALESCE(SUM(monto_total), 0) AS total_ventas
                  FROM venta_producto
                  WHERE fecha >= CURDATE() - INTERVAL 7 DAY";
        $stmt = $this->db->query($sqlVentas);
        $totalVentas = $stmt->fetchColumn();

        return [
            'total_membresias' => (float) $totalMembresias,
            'total_ventas'     => (float) $totalVentas,
            'total_general'    => (float) $totalMembresias + (float) $totalVentas
        ];
    }
}