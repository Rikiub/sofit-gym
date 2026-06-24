<?php

namespace App\Models;

use Exception;

class FacturacionPagosModel extends BaseModel
{
    public function registrarPago(
        string $cedulaCliente,
        float $monto,
        string $metodoPago,
        ?string $comprobanteUrl = null,
        ?int $planTipoId = null,
    ): array {
        $cliente = $this->obtenerCliente($cedulaCliente);
        if (!$cliente) {
            throw new Exception("Cliente no encontrado.");
        }

        $membresiaActual = null;
        if (isset($cliente['id_membresia']) && $cliente['id_membresia'] > 0) {
            $membresiaActual = $this->obtenerMembresiaPorId($cliente['id_membresia']);
        }

        if ($membresiaActual && $membresiaActual['id_tipo']) {
            $tipoMembresiaId = $membresiaActual['id_tipo'];
        } elseif ($planTipoId !== null) {
            $tipoMembresiaId = $planTipoId;
        } else {
            throw new Exception("Debe especificar el tipo de membresía para el primer pago.");
        }

        $duracionDias = match ($tipoMembresiaId) {
            1 => 30,
            2 => 90,
            3 => 365,
            default => 30,
        };
        $fechaPago = date('Y-m-d');
        $nuevaFechaVencimiento = date('Y-m-d', strtotime("+{$duracionDias} days"));

        $this->pdo->beginTransaction();
        try {
            $stmt = $this->pdo->prepare("INSERT INTO membresia (id_tipo, id_estado, fecha_inicio, fecha_fin, cedula_cliente) VALUES (?, 1, ?, ?, ?)");
            $stmt->execute([$tipoMembresiaId, $fechaPago, $nuevaFechaVencimiento, $cedulaCliente]);
            $nuevaId = $this->pdo->lastInsertId();

            if ($membresiaActual && $membresiaActual['id_membresia']) {
                $stmt = $this->pdo->prepare("UPDATE membresia SET id_estado = 2 WHERE id_membresia = ?");
                $stmt->execute([$membresiaActual['id_membresia']]);
            }

            $stmtMetodo = $this->pdo->prepare("SELECT id_metodo FROM metodo_pago WHERE nombre LIKE ? LIMIT 1");
            $stmtMetodo->execute(["%" . $metodoPago . "%"]);
            $idMetodo = $stmtMetodo->fetchColumn() ?: 1;

            $stmt = $this->pdo->prepare("INSERT INTO pago (id_membresia, id_metodo, monto, comprobante_url, estado, fecha_pago) VALUES (?, ?, ?, ?, 'Pagado', ?)");
            $stmt->execute([$nuevaId, $idMetodo, $monto, $comprobanteUrl, $fechaPago]);
            $idPago = $this->pdo->lastInsertId();

            $this->pdo->commit();
            return [
                'exito' => true,
                'nueva_fecha_vencimiento' => $nuevaFechaVencimiento,
                'id_pago' => $idPago,
                'mensaje' => "Pago registrado. Vigencia hasta {$nuevaFechaVencimiento}"
            ];
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw new Exception("Error al registrar pago: " . $e->getMessage());
        }
    }

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
                    m.fecha_fin AS membresia_fecha_fin,
                    fn_dias_restantes(m.fecha_fin) AS dias_restantes,
                    fn_estado_membresia(m.fecha_fin, p.estado) as estado_cliente
                FROM pago p
                JOIN membresia m ON p.id_membresia = m.id_membresia
                LEFT JOIN metodo_pago mp ON p.id_metodo = mp.id_metodo
                JOIN (
                    SELECT m_sub.cedula_cliente, MAX(p_sub.id_pago) AS ultimo_id
                    FROM pago p_sub
                    JOIN membresia m_sub ON p_sub.id_membresia = m_sub.id_membresia
                    GROUP BY m_sub.cedula_cliente
                ) ult ON p.id_pago = ult.ultimo_id
                JOIN cliente c ON m.cedula_cliente = c.cedula
                JOIN persona per ON c.cedula = per.cedula
                ORDER BY p.fecha_pago DESC
                LIMIT 50";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function obtenerIngresosMesActual(): array
    {
        $sql = <<<SQL
                SELECT 
                    COUNT(p.id_pago) AS total_vendidos,
                    SUM(p.monto) AS total_ingresado
                FROM pago p
                JOIN membresia m ON p.id_membresia = m.id_membresia
                WHERE p.estado = 'Pagado'
                    AND YEAR(p.fecha_pago) = YEAR(CURDATE())
                    AND MONTH(p.fecha_pago) = MONTH(CURDATE())
        SQL;

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetch();
    }

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
                    m.fecha_fin AS membresia_fecha_fin,
                    fn_dias_restantes(m.fecha_fin) AS dias_restantes,
                    fn_estado_membresia(m.fecha_fin, p.estado) as estado_cliente
                FROM pago p
                JOIN membresia m ON p.id_membresia = m.id_membresia
                LEFT JOIN metodo_pago mp ON p.id_metodo = mp.id_metodo
                JOIN (
                    SELECT m_sub.cedula_cliente, MAX(p_sub.id_pago) AS ultimo_id
                    FROM pago p_sub
                    JOIN membresia m_sub ON p_sub.id_membresia = m_sub.id_membresia
                    GROUP BY m_sub.cedula_cliente
                ) ult ON p.id_pago = ult.ultimo_id
                JOIN cliente c ON m.cedula_cliente = c.cedula
                JOIN persona per ON c.cedula = per.cedula
                WHERE p.id_pago LIKE ? 
                   OR m.cedula_cliente LIKE ? 
                   OR per.nombre LIKE ? 
                   OR per.apellido LIKE ?
                ORDER BY p.fecha_pago DESC
                LIMIT 50";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$termino, $termino, $termino, $termino]);
        return $stmt->fetchAll();
    }

    public function actualizarPago(
        int $idPago,
        float $monto,
        string $metodoPago,
        string $estado,
        string $fechaPago,
        string $fechaVencimiento,
    ): bool {
        $stmtMetodo = $this->pdo->prepare("SELECT id_metodo FROM metodo_pago WHERE nombre LIKE ? LIMIT 1");
        $stmtMetodo->execute(["%" . $metodoPago . "%"]);
        $idMetodo = $stmtMetodo->fetchColumn() ?: 1;

        $sql = "UPDATE pago SET monto = ?, id_metodo = ?, estado = ?, fecha_pago = ? WHERE id_pago = ?";
        $stmt = $this->pdo->prepare($sql);
        $res = $stmt->execute([$monto, $idMetodo, $estado, $fechaPago, $idPago]);

        if ($res) {
            $stmtMem = $this->pdo->prepare("SELECT id_membresia FROM pago WHERE id_pago = ?");
            $stmtMem->execute([$idPago]);
            $idMembresia = $stmtMem->fetchColumn();

            if ($idMembresia) {
                $stmtUpdateMem = $this->pdo->prepare("UPDATE membresia SET fecha_fin = ? WHERE id_membresia = ?");
                $stmtUpdateMem->execute([$fechaVencimiento, $idMembresia]);
            }
        }

        return $res;
    }

    public function eliminarPago(int $idPago): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM pago WHERE id_pago = ?");
        return $stmt->execute([$idPago]);
    }

    public function obtenerClientesSimples(): array
    {
        $sql = "SELECT c.cedula AS cedula_cliente, p.nombre AS nombre, p.correo, p.telefono 
                FROM cliente c 
                JOIN persona p ON c.cedula = p.cedula 
                ORDER BY p.nombre";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    private function obtenerCliente(string $cedula): ?array
    {
        $stmt = $this->pdo->prepare("
            SELECT 
                cedula AS cedula_cliente,
                (SELECT id_membresia FROM membresia WHERE cedula_cliente = cliente.cedula ORDER BY id_membresia DESC LIMIT 1) AS id_membresia 
            FROM cliente 
            WHERE cedula = ?
        ");
        $stmt->execute([$cedula]);
        return $stmt->fetch() ?: null;
    }

    private function obtenerMembresiaPorId(int $id): ?array
    {
        $stmt = $this->pdo->prepare("SELECT id_membresia, id_tipo, fecha_fin, id_estado FROM membresia WHERE id_membresia = ?");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    // REPORTES

    /**
     * Obtiene el listado de pagos filtrado por período para la generación de reportes.
     * Estructurado de acuerdo a los requerimientos del reporteFinanciero de FPDF.
     *
     * @param string|null $mes Número de mes con formato de dos dígitos (ej. '06' para Junio)
     * @param string|null $anio Año de 4 dígitos (ej. '2026')
     * @return array Listado de pagos con cliente y método de pago unificados
     */
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

        // Filtro por año
        if (!empty($anio)) {
            $where[] = "YEAR(p.fecha_pago) = :anio";
            $params['anio'] = $anio;
        }

        // Filtro por mes
        if (!empty($mes)) {
            $where[] = "MONTH(p.fecha_pago) = :mes";
            $params['mes'] = $mes;
        }

        // Aplicamos cláusula WHERE si existen filtros definidos
        if (!empty($where)) {
            $sql .= " WHERE " . implode(" AND ", $where);
        }

        // Ordenamos por fecha de pago de forma cronológica para el flujo del reporte financiero
        $sql .= " ORDER BY p.fecha_pago ASC, p.id_pago ASC";

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (\PDOException $e) {
            error_log("Error: " . $e->getMessage());
            return [];
        }
    }
}
