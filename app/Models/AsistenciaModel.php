<?php

namespace App\Models;

use App\Models\Database;
use PDO;
use Throwable;

class AsistenciaModel extends Database
{
    private const MINUTOS_ANTI_DUPLICADO = 30;
    private const FETCH = PDO::FETCH_ASSOC;

    private ?int    $idAsistencia  = null;
    private ?string $cedulaPersona = null;
    private ?string $fecha         = null;
    private ?string $hora          = null;
    private ?string $tipo          = null;
    private ?string $termino       = null;
    private ?string $fechaInicio   = null;
    private ?string $fechaFin      = null;

    public function __construct()
    {
        parent::__construct();

        if (property_exists($this, 'pdo') && !$this->pdo && method_exists($this, 'getConnect')) {
            $this->pdo = $this->getConnect();
        }
    }

    public function getIdAsistencia(): ?int { return $this->idAsistencia; }
    public function setIdAsistencia(?int $idAsistencia): void { $this->idAsistencia = $idAsistencia; }

    public function getCedulaPersona(): ?string { return $this->cedulaPersona; }
    public function setCedulaPersona(?string $cedulaPersona): void { $this->cedulaPersona = $cedulaPersona; }

    public function getFecha(): ?string { return $this->fecha; }
    public function setFecha(?string $fecha): void { $this->fecha = $fecha; }

    public function getHora(): ?string { return $this->hora; }
    public function setHora(?string $hora): void { $this->hora = $hora; }

    public function getTipo(): ?string { return $this->tipo; }
    public function setTipo(?string $tipo): void { $this->tipo = $tipo; }

    public function getTermino(): ?string { return $this->termino; }
    public function setTermino(?string $termino): void { $this->termino = $termino; }

    public function getFechaInicio(): ?string { return $this->fechaInicio; }
    public function setFechaInicio(?string $fechaInicio): void { $this->fechaInicio = $fechaInicio; }

    public function getFechaFin(): ?string { return $this->fechaFin; }
    public function setFechaFin(?string $fechaFin): void { $this->fechaFin = $fechaFin; }

    public function findCliente(int $id): ?array
    {
        return $this->fetchOne(
            "SELECT id_asistencia, cedula_persona AS cedula_cliente, fecha
             FROM asistencia_gimnasio
             WHERE id_asistencia = ?",
            [$id]
        );
    }

    public function buscarClientes(string $termino): array
    {
        $like = '%' . $termino . '%';

        return $this->fetchAll(
            "SELECT
                p.cedula AS cedula_cliente,
                p.cedula AS cedula_persona,
                CONCAT(p.nombre, ' ', p.apellido) AS nombre,
                p.correo,
                p.telefono,
                MAX(m.fecha_fin) AS fecha_fin
             FROM cliente c
             INNER JOIN persona p ON c.cedula = p.cedula
             INNER JOIN membresia m ON m.cedula_cliente = c.cedula
             WHERE (p.cedula LIKE ?
                 OR p.nombre LIKE ?
                 OR p.apellido LIKE ?
                 OR CONCAT(p.nombre, ' ', p.apellido) LIKE ?
                 OR p.correo LIKE ?
                 OR p.telefono LIKE ?)
               AND m.fecha_fin >= CURDATE()
               AND m.id_estado = 1
             GROUP BY p.cedula, p.nombre, p.apellido, p.correo, p.telefono
             ORDER BY p.nombre ASC
             LIMIT 50",
            [$like, $like, $like, $like, $like, $like]
        );
    }

    public function obtenerEntradasHoy(): array
    {
        return $this->fetchAll(
            "SELECT a.id_asistencia, a.cedula_persona AS `cedula_cliente`, a.fecha,
                    CONCAT(p.nombre, ' ', p.apellido) AS nombre_cliente
             FROM asistencia_gimnasio a
             JOIN cliente c ON a.cedula_persona = c.cedula
             JOIN persona p ON c.cedula = p.cedula
             WHERE DATE(a.fecha) = CURDATE() AND a.tipo = 'Entrada'
             ORDER BY a.fecha DESC"
        );
    }

    public function buscarEntradas(string $termino): array
    {
        $like = '%' . $termino . '%';

        return $this->fetchAll(
            "SELECT a.id_asistencia, a.cedula_persona AS `cedula_cliente`, a.fecha,
                    CONCAT(p.nombre, ' ', p.apellido) AS nombre_cliente
             FROM asistencia_gimnasio a
             JOIN cliente c ON a.cedula_persona = c.cedula
             JOIN persona p ON c.cedula = p.cedula
             WHERE DATE(a.fecha) = CURDATE() AND a.tipo = 'Entrada'
               AND (TIME(a.fecha) LIKE ?
                 OR a.cedula_persona LIKE ?
                 OR p.nombre LIKE ?
                 OR p.apellido LIKE ?)
             ORDER BY a.fecha DESC",
            [$like, $like, $like, $like]
        );
    }

    public function obtenerEntradasPorFecha(string $fecha): array
    {
        return $this->fetchAll(
            "SELECT a.id_asistencia, a.cedula_persona AS `cedula_cliente`, a.fecha,
                    CONCAT(p.nombre, ' ', p.apellido) AS nombre_cliente
             FROM asistencia_gimnasio a
             JOIN cliente c ON a.cedula_persona = c.cedula
             JOIN persona p ON c.cedula = p.cedula
             WHERE DATE(a.fecha) = ? AND a.tipo = 'Entrada'
             ORDER BY a.fecha ASC",
            [$fecha]
        );
    }

    public function obtenerOcupacionPorFranjas(string $fecha): array
    {
        $franjas = [
            ['nombre' => 'Mañana (6am - 12pm)',     'inicio' => '06:00:00', 'fin' => '12:00:00'],
            ['nombre' => 'Tarde (12pm - 3pm)',      'inicio' => '12:00:00', 'fin' => '15:00:00'],
            ['nombre' => 'Media Tarde (3pm - 6pm)', 'inicio' => '15:00:00', 'fin' => '18:00:00'],
            ['nombre' => 'Noche (6pm - 10pm)',      'inicio' => '18:00:00', 'fin' => '22:00:00'],
        ];

        $resultado = [];

        try {
            $stmt = $this->prepareOrFail(
                "SELECT COUNT(*) AS total
                 FROM asistencia_gimnasio
                 WHERE DATE(fecha) = ?
                   AND TIME(fecha) >= ?
                   AND TIME(fecha) <  ?
                   AND tipo = 'Entrada'"
            );

            foreach ($franjas as $franja) {
                $stmt->execute([$fecha, $franja['inicio'], $franja['fin']]);
                $resultado[] = [
                    'franja' => $franja['nombre'],
                    'total'  => (int) $stmt->fetchColumn(),
                    'inicio' => $franja['inicio'],
                    'fin'    => $franja['fin'],
                ];
            }
        } catch (Throwable $e) {
            error_log(static::class . '::' . __FUNCTION__ . ' → ' . $e->getMessage());
        }

        return $resultado;
    }

    public function obtenerTotalesPorRango(string $fechaInicio, string $fechaFin): array
    {
        return $this->fetchAll(
            "CALL sp_obtener_totales_asistencias_por_rango(?, ?)",
            [$fechaInicio, $fechaFin]
        );
    }

    public function obtenerAsistenciasParaReporte(?string $fechaInicio = null, ?string $fechaFin = null): array
    {
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

        if (!empty($fechaInicio) && !empty($fechaFin)) {
            $sql .= " AND DATE(a.fecha) BETWEEN ? AND ?";
            $params[] = $fechaInicio;
            $params[] = $fechaFin;
        }

        $sql .= " ORDER BY a.fecha DESC";

        return $this->fetchAll($sql, $params);
    }

    public function tieneEntradaReciente(string $cedula): ?array
    {
        $limite = date('Y-m-d H:i:s', strtotime('-' . self::MINUTOS_ANTI_DUPLICADO . ' minutes'));

        return $this->fetchOne(
            "SELECT id_asistencia, fecha
             FROM asistencia_gimnasio
             WHERE cedula_persona = ?
               AND tipo = 'Entrada'
               AND fecha >= ?
             ORDER BY fecha DESC
             LIMIT 1",
            [$cedula, $limite]
        );
    }

    public function obtenerHistorialAsistencias(?string $cedula = null, ?string $fechaInicio = null, ?string $fechaFin = null, ?string $termino = null): array
    {
        $sql = "SELECT a.id_asistencia,
                       a.cedula_persona AS cedula_cliente,
                       a.fecha,
                       CONCAT(p.nombre, ' ', p.apellido) AS nombre_cliente
                FROM asistencia_gimnasio a
                INNER JOIN cliente c ON a.cedula_persona = c.cedula
                INNER JOIN persona p ON c.cedula = p.cedula
                WHERE a.tipo = 'Entrada'";

        $params = [];

        if (!empty($cedula)) {
            $sql .= " AND a.cedula_persona = ?";
            $params[] = $cedula;
        }

        if (!empty($termino)) {
            $like = '%' . $termino . '%';
            $sql .= " AND (a.cedula_persona LIKE ?
                        OR p.nombre LIKE ?
                        OR p.apellido LIKE ?
                        OR CONCAT(p.nombre, ' ', p.apellido) LIKE ?)";
            $params[] = $like;
            $params[] = $like;
            $params[] = $like;
            $params[] = $like;
        }

        if (!empty($fechaInicio)) {
            $sql .= " AND DATE(a.fecha) >= ?";
            $params[] = $fechaInicio;
        }

        if (!empty($fechaFin)) {
            $sql .= " AND DATE(a.fecha) <= ?";
            $params[] = $fechaFin;
        }

        $sql .= " ORDER BY a.fecha DESC LIMIT 500";

        return $this->fetchAll($sql, $params);
    }

    public function obtenerResumenAsistencias(?string $cedula = null): array
    {
        $sql = "SELECT
                    COUNT(*) AS total,
                    SUM(CASE WHEN DATE(fecha) = CURDATE() THEN 1 ELSE 0 END) AS hoy,
                    SUM(CASE WHEN YEARWEEK(fecha, 1) = YEARWEEK(CURDATE(), 1) THEN 1 ELSE 0 END) AS semana,
                    SUM(CASE WHEN MONTH(fecha) = MONTH(CURDATE()) AND YEAR(fecha) = YEAR(CURDATE()) THEN 1 ELSE 0 END) AS mes,
                    MAX(fecha) AS ultima_visita
                 FROM asistencia_gimnasio
                 WHERE tipo = 'Entrada'";

        $params = [];

        if (!empty($cedula)) {
            $sql .= " AND cedula_persona = ?";
            $params[] = $cedula;
        }

        $row = $this->fetchOne($sql, $params);

        return $row ?: [
            'total'         => 0,
            'hoy'           => 0,
            'semana'        => 0,
            'mes'           => 0,
            'ultima_visita' => null,
        ];
    }

    public function registrarEntrada(string $cedula, ?string $hora = null): array
    {
        $cliente = $this->fetchOne(
            "SELECT p.cedula AS cedula_cliente,
                    CONCAT(p.nombre, ' ', p.apellido) AS nombre
             FROM persona p
             INNER JOIN cliente c ON p.cedula = c.cedula
             INNER JOIN membresia m ON m.cedula_cliente = c.cedula
             WHERE p.cedula = ?
               AND m.fecha_fin >= CURDATE()
               AND m.id_estado = 1
             LIMIT 1",
            [$cedula]
        );

        if (!$cliente) {
            return [
                'success' => false,
                'message' => 'Cliente no encontrado o membresía inactiva/vencida.',
            ];
        }

        $reciente = $this->tieneEntradaReciente($cedula);
        if ($reciente) {
            return [
                'success'       => false,
                'duplicado'     => true,
                'id_asistencia' => (int) $reciente['id_asistencia'],
                'ultima_hora'   => $reciente['fecha'],
                'message'       => sprintf(
                    '%s ya tiene una entrada registrada a las %s (últimos %d min).',
                    $cliente['nombre'],
                    date('H:i:s', strtotime($reciente['fecha'])),
                    self::MINUTOS_ANTI_DUPLICADO
                ),
            ];
        }

        try {
            $stmt = $this->prepareOrFail(
                "CALL sp_registrar_entrada_cliente(?, ?, @ok, @msg, @id, @fecha)"
            );
            $stmt->execute([$cedula, $hora]);
            $stmt->closeCursor();

            $res = $this->pdo->query(
                "SELECT @ok AS exito, @msg AS mensaje, @id AS id, @fecha AS fecha"
            );
            if (!$res) {
                throw new \RuntimeException('No se pudo leer la salida del stored procedure.');
            }
            $asistencia = $res->fetch(self::FETCH) ?: [];
            $res->closeCursor();

            if (isset($asistencia['exito']) && (int) $asistencia['exito'] === 0) {
                return [
                    'success' => false,
                    'message' => $asistencia['mensaje'] ?? 'No se pudo registrar la entrada.',
                ];
            }

            return [
                'success' => true,
                'id'      => $asistencia['id']    ?? null,
                'fecha'   => $asistencia['fecha'] ?? date('Y-m-d H:i:s'),
                'cedula'  => $cedula,
                'nombre'  => $cliente['nombre'],
            ];
        } catch (Throwable $e) {
            error_log(static::class . '::' . __FUNCTION__ . ' → ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error en la base de datos: ' . $e->getMessage(),
            ];
        }
    }

    public function actualizarEntrada(int $id, string $nuevaHora): bool
    {
        $fechaCompleta = date('Y-m-d') . ' ' . $nuevaHora;

        return $this->execute(
            "UPDATE asistencia_gimnasio SET fecha = ? WHERE id_asistencia = ?",
            [$fechaCompleta, $id]
        );
    }

    public function eliminarEntrada(int $id): bool
    {
        return $this->execute(
            "DELETE FROM asistencia_gimnasio WHERE id_asistencia = ?",
            [$id]
        );
    }

    private function prepareOrFail(string $sql): \PDOStatement
    {
        $stmt = $this->pdo->prepare($sql);
        if (!$stmt) {
            $info = $this->pdo->errorInfo();
            throw new \RuntimeException('Error al preparar la consulta: ' . ($info[2] ?? 'desconocido'));
        }
        return $stmt;
    }

    private function fetchAll(string $sql, array $params = []): array
    {
        try {
            $stmt = $this->prepareOrFail($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(self::FETCH) ?: [];
        } catch (Throwable $e) {
            error_log(static::class . '::' . __FUNCTION__ . ' → ' . $e->getMessage());
            return [];
        }
    }

    private function fetchOne(string $sql, array $params = []): ?array
    {
        try {
            $stmt = $this->prepareOrFail($sql);
            $stmt->execute($params);
            $row = $stmt->fetch(self::FETCH);
            return $row ?: null;
        } catch (Throwable $e) {
            error_log(static::class . '::' . __FUNCTION__ . ' → ' . $e->getMessage());
            return null;
        }
    }

    private function execute(string $sql, array $params = []): bool
    {
        try {
            $stmt = $this->prepareOrFail($sql);
            return $stmt->execute($params);
        } catch (Throwable $e) {
            error_log(static::class . '::' . __FUNCTION__ . ' → ' . $e->getMessage());
            return false;
        }
    }
}