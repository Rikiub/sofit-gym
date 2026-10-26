<?php

namespace App\Models;

use App\Models\Database;
use PDO;
use Throwable;

class FacturacionModel
{
    use Database;

    private ?int    $idPago           = null;
    private ?string $cedulaCliente    = null;
    private ?float  $monto            = null;
    private ?string $metodoPago       = null;
    private ?int    $planTipoId       = null;
    private ?string $estado           = null;
    private ?string $fechaPago        = null;
    private ?string $fechaVencimiento = null;
    private ?string $mes              = null;
    private ?string $anio             = null;
    private ?int    $dias             = null;
    private ?string $termino          = null;

    public function getIdPago(): ?int
    {
        return $this->idPago;
    }
    public function setIdPago(?int $idPago): void
    {
        $this->idPago = $idPago;
    }

    public function getCedulaCliente(): ?string
    {
        return $this->cedulaCliente;
    }
    public function setCedulaCliente(?string $cedulaCliente): void
    {
        $this->cedulaCliente = $cedulaCliente;
    }

    public function getMonto(): ?float
    {
        return $this->monto;
    }
    public function setMonto(?float $monto): void
    {
        $this->monto = $monto;
    }

    public function getMetodoPago(): ?string
    {
        return $this->metodoPago;
    }
    public function setMetodoPago(?string $metodoPago): void
    {
        $this->metodoPago = $metodoPago;
    }

    public function getPlanTipoId(): ?int
    {
        return $this->planTipoId;
    }
    public function setPlanTipoId(?int $planTipoId): void
    {
        $this->planTipoId = $planTipoId;
    }

    public function getEstado(): ?string
    {
        return $this->estado;
    }
    public function setEstado(?string $estado): void
    {
        $this->estado = $estado;
    }

    public function getFechaPago(): ?string
    {
        return $this->fechaPago;
    }
    public function setFechaPago(?string $fechaPago): void
    {
        $this->fechaPago = $fechaPago;
    }

    public function getFechaVencimiento(): ?string
    {
        return $this->fechaVencimiento;
    }
    public function setFechaVencimiento(?string $fechaVencimiento): void
    {
        $this->fechaVencimiento = $fechaVencimiento;
    }

    public function getMes(): ?string
    {
        return $this->mes;
    }
    public function setMes(?string $mes): void
    {
        $this->mes = $mes;
    }

    public function getAnio(): ?string
    {
        return $this->anio;
    }
    public function setAnio(?string $anio): void
    {
        $this->anio = $anio;
    }

    public function getDias(): ?int
    {
        return $this->dias;
    }
    public function setDias(?int $dias): void
    {
        $this->dias = $dias;
    }

    public function getTermino(): ?string
    {
        return $this->termino;
    }
    public function setTermino(?string $termino): void
    {
        $this->termino = $termino;
    }

    public function registrarPago(
        string $cedulaCliente,
        float $monto,
        string $metodoPago,
        ?int $planTipoId = null
    ): array {
        $this->setCedulaCliente($cedulaCliente);
        $this->setMonto($monto);
        $this->setMetodoPago($metodoPago);
        $this->setPlanTipoId($planTipoId);
        return $this->processRegistrarPago();
    }

    private function processRegistrarPago(): array
    {
        $cliente = $this->findClienteByCedula();
        if (!$cliente) {
            throw new \RuntimeException("Cliente no encontrado.");
        }

        if (($this->getMonto() ?? 0) <= 0) {
            throw new \RuntimeException("El monto debe ser mayor a cero.");
        }

        try {
            $membresiaActual = null;
            if (isset($cliente['id_membresia']) && $cliente['id_membresia'] > 0) {
                $membresiaActual = $this->findMembresiaById((int) $cliente['id_membresia']);
            }

            $planTipoId = $this->getPlanTipoId();
            if ($planTipoId !== null && $planTipoId > 0) {
                $tipoMembresiaId = $planTipoId;
            } elseif ($membresiaActual && !empty($membresiaActual['id_tipo'])) {
                $tipoMembresiaId = $membresiaActual['id_tipo'];
            } else {
                $tipoMembresiaId = 1;
            }

            // Obtener precio y duración del plan
            $stmt = $this->getConnection()->prepare("SELECT monto, duracion_dias FROM tipo_membresia WHERE id_tipo = ?");
            $stmt->execute([$tipoMembresiaId]);
            $planData = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
            $precioPlan   = (float) ($planData['monto'] ?? 0);
            $duracionDias = (int)   ($planData['duracion_dias'] ?? 30);

            $fechaPago = date('Y-m-d');

            $reutilizar = false;
            if ($membresiaActual) {
                $mismoPlan    = (int) $membresiaActual['id_tipo'] === (int) $tipoMembresiaId;
                $estadoActual = (int) ($membresiaActual['id_estado'] ?? 0);
                $fechaFin     = $membresiaActual['fecha_fin'] ?? null;
                $vigente      = $fechaFin && $fechaFin >= $fechaPago;

                if ($mismoPlan) {
                    if ($estadoActual === 3) {
                        // Cliente moroso pagando el mismo plan → abonar a la deuda
                        $reutilizar = true;
                    } elseif ($estadoActual === 1 && $vigente) {
                        // Cliente activo vigente pagando el mismo plan → abono
                        $reutilizar = true;
                    }
                }
            }

            $this->getConnection()->beginTransaction();

            if ($reutilizar) {
                $idMembresia           = (int) $membresiaActual['id_membresia'];
                $nuevaFechaVencimiento = $membresiaActual['fecha_fin'];
                $fechaInicioMembresia  = $membresiaActual['fecha_inicio'];
            } else {
                $nuevaFechaVencimiento = date('Y-m-d', strtotime("+{$duracionDias} days"));
                $fechaInicioMembresia  = $fechaPago;

                $stmt = $this->getConnection()->prepare(
                    "INSERT INTO membresia (id_tipo, id_estado, fecha_inicio, fecha_fin, cedula_cliente)
                     VALUES (?, 1, ?, ?, ?)"
                );
                $stmt->execute([$tipoMembresiaId, $fechaPago, $nuevaFechaVencimiento, $this->getCedulaCliente()]);
                $idMembresia = $this->getConnection()->lastInsertId();

                if ($membresiaActual && !empty($membresiaActual['id_membresia'])) {
                    $stmt = $this->getConnection()->prepare("UPDATE membresia SET id_estado = 2 WHERE id_membresia = ?");
                    $stmt->execute([$membresiaActual['id_membresia']]);
                }
            }

            // Insertar pago
            $idMetodo = $this->resolveMetodoPagoId();
            $stmt = $this->getConnection()->prepare(
                "INSERT INTO pago (id_metodo, cedula_cliente, monto, estado, fecha_pago)
                 VALUES (?, ?, ?, 'Pagado', ?)"
            );
            $stmt->execute([$idMetodo, $this->getCedulaCliente(), $this->getMonto(), $fechaPago]);
            $idPago = $this->getConnection()->lastInsertId();

            // Sumar TODOS los pagos desde que inició esta membresía
            $stmt = $this->getConnection()->prepare(
                "SELECT COALESCE(SUM(monto), 0) FROM pago
                 WHERE cedula_cliente = ? AND fecha_pago >= ?"
            );
            $stmt->execute([$this->getCedulaCliente(), $fechaInicioMembresia]);
            $totalPagado = (float) $stmt->fetchColumn();

            // Determinar estado final
            $estadoFinal = ($totalPagado >= $precioPlan) ? 1 : 3;
            $stmt = $this->getConnection()->prepare("UPDATE membresia SET id_estado = ? WHERE id_membresia = ?");
            $stmt->execute([$estadoFinal, $idMembresia]);

            $this->getConnection()->commit();

            $deuda = max(0, $precioPlan - $totalPagado);
            $mensaje = ($estadoFinal === 3)
                ? "Pago parcial registrado. Cliente MOROSO. Debe: $" . number_format($deuda, 2)
                : "Pago registrado. Total: $" . number_format($totalPagado, 2) . " / $" . number_format($precioPlan, 2);

            return [
                'exito'                   => true,
                'nueva_fecha_vencimiento' => $nuevaFechaVencimiento,
                'id_pago'                 => $idPago,
                'estado_cliente'          => $estadoFinal === 3 ? 'Moroso' : 'Activo',
                'monto_pagado'            => $this->getMonto(),
                'total_pagado'            => $totalPagado,
                'precio_plan'             => $precioPlan,
                'deuda'                   => $deuda,
                'mensaje'                 => $mensaje,
            ];
        } catch (Throwable $e) {
            if ($this->getConnection()->inTransaction()) {
                $this->getConnection()->rollBack();
            }
            error_log(static::class . "::processRegistrarPago → " . $e->getMessage());
            throw new \RuntimeException("Error al registrar pago: " . $e->getMessage());
        }
    }

    public function obtenerTodosPagos(): array
    {
        return $this->fetchPagosConJoins("");
    }

    public function buscarPagos(string $termino): array
    {
        $this->setTermino($termino);
        return $this->buscarPagosByTermino();
    }

    private function buscarPagosByTermino(): array
    {
        $termino = $this->getTermino() ?? '';
        if ($termino === '') {
            return $this->obtenerTodosPagos();
        }

        $like = "%{$termino}%";

        return $this->fetchAll(
            "SELECT
                p.id_pago,
                p.cedula_cliente,
                CONCAT(per.nombre, ' ', per.apellido) AS nombre_cliente,
                p.monto,
                mp.nombre AS metodo_pago,
                p.estado AS estado_pago,
                p.fecha_pago,
                m.fecha_fin AS fecha_vencimiento,
                fn_dias_restantes(m.fecha_fin) AS dias_restantes,
                fn_estado_membresia(m.fecha_fin, p.estado) AS estado_cliente
             FROM pago p
             LEFT JOIN cliente c ON c.cedula = p.cedula_cliente
             LEFT JOIN persona per ON per.cedula = c.cedula
             LEFT JOIN metodo_pago mp ON p.id_metodo = mp.id_metodo
             LEFT JOIN membresia m ON m.id_membresia = (
                SELECT m2.id_membresia FROM membresia m2
                WHERE m2.cedula_cliente = p.cedula_cliente
                ORDER BY m2.fecha_inicio DESC, m2.id_membresia DESC LIMIT 1
             )
             WHERE p.id_pago LIKE ?
                OR p.cedula_cliente LIKE ?
                OR per.nombre LIKE ?
                OR per.apellido LIKE ?
             ORDER BY p.id_pago DESC",
            [$like, $like, $like, $like]
        );
    }

    private function fetchPagosConJoins(string $where): array
    {
        return $this->fetchAll(
            "SELECT
                p.id_pago,
                p.cedula_cliente,
                CONCAT(per.nombre, ' ', per.apellido) AS nombre_cliente,
                p.monto,
                mp.nombre AS metodo_pago,
                p.estado AS estado_pago,
                p.fecha_pago,
                m.fecha_fin AS fecha_vencimiento,
                fn_dias_restantes(m.fecha_fin) AS dias_restantes,
                fn_estado_membresia(m.fecha_fin, p.estado) AS estado_cliente
             FROM pago p
             LEFT JOIN cliente c ON c.cedula = p.cedula_cliente
             LEFT JOIN persona per ON per.cedula = c.cedula
             LEFT JOIN metodo_pago mp ON p.id_metodo = mp.id_metodo
             LEFT JOIN membresia m ON m.id_membresia = (
                SELECT m2.id_membresia FROM membresia m2
                WHERE m2.cedula_cliente = p.cedula_cliente
                ORDER BY m2.fecha_inicio DESC, m2.id_membresia DESC LIMIT 1
             )
             {$where}
             ORDER BY p.id_pago DESC"
        );
    }

    public function obtenerEstadoClientes(?string $termino = null, ?string $fechaDesde = null, ?string $fechaHasta = null): array
    {
        $sql = "SELECT
                    c.cedula,
                    CONCAT(p.nombre, ' ', p.apellido) AS nombre,
                    p.telefono,
                    p.correo,
                    m.fecha_inicio,
                    m.fecha_fin,
                    m.id_estado,
                    tm.nombre AS tipo_membresia,
                    DATEDIFF(m.fecha_fin, CURDATE()) AS dias_restantes,
                    (SELECT MAX(p2.fecha_pago) FROM pago p2 WHERE p2.cedula_cliente = c.cedula) AS ultima_fecha_pago,
                    CASE
                        WHEN m.id_membresia IS NULL THEN 'Sin membresía'
                        WHEN m.id_estado = 3 THEN 'Moroso'
                        WHEN m.id_estado = 2 THEN 'Vencido'
                        WHEN m.fecha_fin < CURDATE() THEN 'Vencido'
                        WHEN DATEDIFF(m.fecha_fin, CURDATE()) <= 7 THEN 'Próximo a vencer'
                        ELSE 'Activo'
                    END AS estado_cliente
                FROM cliente c
                JOIN persona p ON c.cedula = p.cedula
                LEFT JOIN membresia m ON m.id_membresia = (
                    SELECT m2.id_membresia FROM membresia m2
                    WHERE m2.cedula_cliente = c.cedula
                    ORDER BY m2.fecha_inicio DESC, m2.id_membresia DESC LIMIT 1
                )
                LEFT JOIN tipo_membresia tm ON tm.id_tipo = m.id_tipo
                WHERE 1=1";

        $params = [];

        if (!empty($termino)) {
            $like = '%' . $termino . '%';
            $sql .= " AND (c.cedula LIKE ? OR p.nombre LIKE ? OR p.apellido LIKE ?)";
            $params[] = $like;
            $params[] = $like;
            $params[] = $like;
        }

        if (!empty($fechaDesde)) {
            $sql .= " AND DATE(m.fecha_fin) >= ?";
            $params[] = $fechaDesde;
        }

        if (!empty($fechaHasta)) {
            $sql .= " AND DATE(m.fecha_fin) <= ?";
            $params[] = $fechaHasta;
        }

        $sql .= " ORDER BY
                    CASE
                        WHEN m.id_estado = 1
                             AND m.fecha_fin >= CURDATE()
                             AND DATEDIFF(m.fecha_fin, CURDATE()) > 7 THEN 1
                        WHEN m.id_estado = 1
                             AND m.fecha_fin >= CURDATE()
                             AND DATEDIFF(m.fecha_fin, CURDATE()) <= 7 THEN 2
                        WHEN m.id_estado = 2
                             OR (m.fecha_fin IS NOT NULL AND m.fecha_fin < CURDATE()) THEN 3
                        WHEN m.id_estado = 3 THEN 4
                        ELSE 5
                    END ASC,
                    m.fecha_fin ASC,
                    p.nombre ASC";

        $clientes = $this->fetchAll($sql, $params);

        $conteos = [
            'Activo'           => 0,
            'Próximo a vencer' => 0,
            'Vencido'          => 0,
            'Moroso'           => 0,
            'Sin membresía'    => 0,
            'total'            => count($clientes),
        ];

        foreach ($clientes as $c) {
            if (isset($conteos[$c['estado_cliente']])) {
                $conteos[$c['estado_cliente']]++;
            }
        }

        return [
            'conteos'  => $conteos,
            'clientes' => $clientes,
        ];
    }

    public function marcarClienteMoroso(string $cedula): bool
    {
        if ($cedula === '') return false;

        try {
            $stmt = $this->getConnection()->prepare(
                "UPDATE membresia
                 SET id_estado = 3
                 WHERE id_membresia = (
                    SELECT id_membresia FROM (
                        SELECT id_membresia FROM membresia
                        WHERE cedula_cliente = ?
                        ORDER BY fecha_inicio DESC, id_membresia DESC
                        LIMIT 1
                    ) AS tmp
                 )"
            );
            return $stmt->execute([$cedula]);
        } catch (Throwable $e) {
            error_log(static::class . "::marcarClienteMoroso → " . $e->getMessage());
            return false;
        }
    }

    public function reactivarCliente(string $cedula): bool
    {
        if ($cedula === '') return false;

        try {
            $stmt = $this->getConnection()->prepare(
                "UPDATE membresia
                 SET id_estado = 1
                 WHERE id_membresia = (
                    SELECT id_membresia FROM (
                        SELECT id_membresia FROM membresia
                        WHERE cedula_cliente = ?
                        ORDER BY fecha_inicio DESC, id_membresia DESC
                        LIMIT 1
                    ) AS tmp
                 )"
            );
            return $stmt->execute([$cedula]);
        } catch (Throwable $e) {
            error_log(static::class . "::reactivarCliente → " . $e->getMessage());
            return false;
        }
    }

    public function actualizarPago(
        int $idPago,
        float $monto,
        string $metodoPago,
        string $estado,
        string $fechaPago,
        string $fechaVencimiento
    ): bool {
        if ($idPago <= 0) return false;

        $this->setIdPago($idPago);
        $this->setMonto($monto);
        $this->setMetodoPago($metodoPago);
        $this->setEstado($estado);
        $this->setFechaPago($fechaPago);
        $this->setFechaVencimiento($fechaVencimiento);
        return $this->updatePagoById();
    }

    private function updatePagoById(): bool
    {
        try {
            $idMetodo = $this->resolveMetodoPagoId();

            $stmt = $this->getConnection()->prepare(
                "UPDATE pago SET monto = ?, id_metodo = ?, estado = ?, fecha_pago = ? WHERE id_pago = ?"
            );
            $res = $stmt->execute([
                $this->getMonto(),
                $idMetodo,
                $this->getEstado(),
                $this->getFechaPago(),
                $this->getIdPago()
            ]);

            if (!$res) return false;

            $stmtCli = $this->getConnection()->prepare("SELECT cedula_cliente FROM pago WHERE id_pago = ?");
            $stmtCli->execute([$this->getIdPago()]);
            $cedulaCliente = $stmtCli->fetchColumn();

            if ($cedulaCliente) {
                $stmtMem = $this->getConnection()->prepare(
                    "SELECT id_membresia FROM membresia
                     WHERE cedula_cliente = ?
                     ORDER BY fecha_inicio DESC, id_membresia DESC LIMIT 1"
                );
                $stmtMem->execute([$cedulaCliente]);
                $idMembresia = $stmtMem->fetchColumn();

                if ($idMembresia) {
                    $stmtUpd = $this->getConnection()->prepare("UPDATE membresia SET fecha_fin = ? WHERE id_membresia = ?");
                    $stmtUpd->execute([$this->getFechaVencimiento(), $idMembresia]);
                }
            }

            return true;
        } catch (Throwable $e) {
            error_log(static::class . "::updatePagoById → " . $e->getMessage());
            return false;
        }
    }

    public function eliminarPago(int $idPago): bool
    {
        if ($idPago <= 0) return false;

        $this->setIdPago($idPago);
        return $this->deletePagoById();
    }

    private function deletePagoById(): bool
    {
        try {
            $stmt = $this->getConnection()->prepare("DELETE FROM pago WHERE id_pago = ?");
            return $stmt->execute([$this->getIdPago()]);
        } catch (Throwable $e) {
            error_log(static::class . "::deletePagoById → " . $e->getMessage());
            return false;
        }
    }

    public function obtenerClientesSimples(): array
    {
        return $this->fetchAll(
            "SELECT c.cedula AS cedula_cliente, p.nombre AS nombre, p.correo, p.telefono
             FROM cliente c JOIN persona p ON c.cedula = p.cedula
             ORDER BY p.nombre"
        );
    }

    public function obtenerTiposMembresia(): array
    {
        return $this->fetchAll(
            "SELECT id_tipo, nombre, monto, duracion_dias FROM tipo_membresia ORDER BY id_tipo"
        );
    }

    public function obtenerIngresosMesActual(): array
    {
        $row = $this->fetchOne(
            "SELECT
                COUNT(p.id_pago) AS total_vendidos,
                COALESCE(SUM(p.monto), 0) AS total_ingresado
             FROM pago p
             WHERE p.estado = 'Pagado'
               AND YEAR(p.fecha_pago) = YEAR(CURDATE())
               AND MONTH(p.fecha_pago) = MONTH(CURDATE())
               AND p.cedula_cliente IS NOT NULL
               AND p.id_pago NOT IN (SELECT DISTINCT id_pago FROM venta_producto)"
        );

        return $row ?: ['total_vendidos' => 0, 'total_ingresado' => 0];
    }

    public function obtenerPagosPorPeriodo(?string $mes = null, ?string $anio = null): array
    {
        $this->setMes($mes);
        $this->setAnio($anio);

        $sql = "SELECT
                    p.fecha_pago,
                    p.cedula_cliente,
                    CONCAT(per.nombre, ' ', per.apellido) AS nombre_cliente,
                    mp.nombre AS metodo_pago,
                    p.monto
                FROM pago p
                LEFT JOIN cliente c ON c.cedula = p.cedula_cliente
                LEFT JOIN persona per ON per.cedula = c.cedula
                LEFT JOIN metodo_pago mp ON p.id_metodo = mp.id_metodo";

        $where  = [];
        $params = [];

        if (!empty($this->getAnio())) {
            $where[] = "YEAR(p.fecha_pago) = ?";
            $params[] = $this->getAnio();
        }
        if (!empty($this->getMes())) {
            $where[] = "MONTH(p.fecha_pago) = ?";
            $params[] = $this->getMes();
        }
        if (!empty($where)) {
            $sql .= " WHERE " . implode(" AND ", $where);
        }
        $sql .= " ORDER BY p.fecha_pago ASC, p.id_pago ASC";

        return $this->fetchAll($sql, $params);
    }

    public function obtenerClientesPorVencer(int $dias): array
    {
        return $this->fetchAll(
            "SELECT p.cedula, CONCAT(p.nombre, ' ', p.apellido) AS nombre, m.fecha_fin
             FROM cliente c
             JOIN persona p ON c.cedula = p.cedula
             JOIN membresia m ON m.cedula_cliente = c.cedula
             WHERE m.id_estado = 1
               AND m.fecha_fin BETWEEN CURDATE() + INTERVAL 1 DAY AND CURDATE() + INTERVAL ? DAY
             ORDER BY m.fecha_fin ASC",
            [$dias]
        );
    }

    public function obtenerPagosAtrasados(): array
    {
        return $this->fetchAll(
            "SELECT p.cedula, CONCAT(p.nombre, ' ', p.apellido) AS nombre, m.fecha_fin
             FROM cliente c
             JOIN persona p ON c.cedula = p.cedula
             JOIN membresia m ON m.cedula_cliente = c.cedula
             WHERE m.id_estado IN (2, 3)
               AND m.fecha_fin < CURDATE()
             ORDER BY m.fecha_fin ASC"
        );
    }

    public function obtenerNuevosClientes(int $dias): array
    {
        return $this->fetchAll(
            "SELECT p.cedula, CONCAT(p.nombre, ' ', p.apellido) AS nombre, c.fecha_creacion
             FROM cliente c
             JOIN persona p ON c.cedula = p.cedula
             WHERE c.fecha_creacion >= CURDATE() - INTERVAL ? DAY",
            [$dias]
        );
    }

    public function obtenerResumenFinancieroSemanal(): array
    {
        $totalMembresias = (float) $this->fetchColumn(
            "SELECT COALESCE(SUM(monto), 0)
             FROM pago
             WHERE fecha_pago >= CURDATE() - INTERVAL 7 DAY
               AND cedula_cliente IS NOT NULL
               AND id_pago NOT IN (SELECT DISTINCT id_pago FROM venta_producto)"
        );

        $totalVentas = (float) $this->fetchColumn(
            "SELECT COALESCE(SUM(vp.cantidad_vendida * prod.precio_venta), 0)
             FROM venta_producto vp
             INNER JOIN producto prod ON vp.codigo_producto = prod.codigo_producto
             INNER JOIN pago pg ON pg.id_pago = vp.id_pago
             WHERE pg.fecha_pago >= CURDATE() - INTERVAL 7 DAY"
        );

        return [
            'total_membresias' => $totalMembresias,
            'total_ventas'     => $totalVentas,
            'total_general'    => $totalMembresias + $totalVentas,
        ];
    }

    private function findClienteByCedula(): ?array
    {
        return $this->fetchOne(
            "SELECT cedula AS cedula_cliente,
                (SELECT id_membresia FROM membresia
                 WHERE cedula_cliente = cliente.cedula
                 ORDER BY id_membresia DESC LIMIT 1) AS id_membresia
             FROM cliente WHERE cedula = ?",
            [$this->getCedulaCliente()]
        );
    }

    private function findMembresiaById(int $id): ?array
    {
        return $this->fetchOne(
            "SELECT id_membresia, id_tipo, fecha_inicio, fecha_fin, id_estado
             FROM membresia WHERE id_membresia = ?",
            [$id]
        );
    }

    private function resolveMetodoPagoId(): int
    {
        try {
            $stmt = $this->getConnection()->prepare("SELECT id_metodo FROM metodo_pago WHERE nombre LIKE ? LIMIT 1");
            $stmt->execute(["%" . $this->getMetodoPago() . "%"]);
            return (int) ($stmt->fetchColumn() ?: 1);
        } catch (Throwable $e) {
            error_log(static::class . "::resolveMetodoPagoId → " . $e->getMessage());
            return 1;
        }
    }

    private function prepareOrFail(string $sql): \PDOStatement
    {
        $stmt = $this->getConnection()->prepare($sql);
        if (!$stmt) {
            $info = $this->getConnection()->errorInfo();
            throw new \RuntimeException('Error al preparar la consulta: ' . ($info[2] ?? 'desconocido'));
        }
        return $stmt;
    }

    private function fetchAll(string $sql, array $params = []): array
    {
        try {
            $stmt = $this->prepareOrFail($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (Throwable $e) {
            error_log(static::class . '::fetchAll → ' . $e->getMessage());
            return [];
        }
    }

    private function fetchOne(string $sql, array $params = []): ?array
    {
        try {
            $stmt = $this->prepareOrFail($sql);
            $stmt->execute($params);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row ?: null;
        } catch (Throwable $e) {
            error_log(static::class . '::fetchOne → ' . $e->getMessage());
            return null;
        }
    }

    private function fetchColumn(string $sql, array $params = [])
    {
        try {
            $stmt = $this->prepareOrFail($sql);
            $stmt->execute($params);
            return $stmt->fetchColumn();
        } catch (Throwable $e) {
            error_log(static::class . '::fetchColumn → ' . $e->getMessage());
            return 0;
        }
    }
}
