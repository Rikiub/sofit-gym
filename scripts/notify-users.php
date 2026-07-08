<?php

use App\Services\Auth\UserRol;
use App\Services\NotificacionService;
use App\Models\BitacoraModel;
use App\Models\FacturacionModel;
use App\Models\ProductoModel;
use App\Models\Equipos\EquipoModel;
use App\Models\Equipos\MantenimientoEquipoModel;

require 'bootstrap/app.php';

const CONTEXT = [
    "modulo" => "notificaciones",
    "accion" => "enviar",
];

$notif = new NotificacionService();
$logger = new BitacoraModel();
$facturacionModel = new FacturacionModel();
$productoModel = new ProductoModel();
$equipoModel = new EquipoModel();
$mantenimientoModel = new MantenimientoEquipoModel();

// Día de la semana actual (1 = lunes, 7 = domingo)
$diaSemana = (int) date('N');

// 1. Membresías a punto de vencer (3 días)
$clientes = $facturacionModel->obtenerClientesPorVencer(3);
if ($clientes) {
    $lista = '';
    foreach ($clientes as $c) {
        $lista .= "- {$c['nombre']} ({$c['cedula']}) - vence el {$c['fecha_fin']}\n";
    }

    $notif->sendByRol(
        roles: [UserRol::Administrador, UserRol::Recepcionista],
        titulo: "📅 Membresías por vencer en 3 días",
        contenido: "Los siguientes clientes tienen membresía a punto de vencer:\n\n" . $lista
    );
    $logger->log("Notificación de membresías por vencer enviada", CONTEXT);
}

// 2. Pagos atrasados
$atrasados = $facturacionModel->obtenerPagosAtrasados();
if ($atrasados) {
    $lista = '';
    foreach ($atrasados as $c) {
        $lista .= "- {$c['nombre']} ({$c['cedula']}) - vencido desde {$c['fecha_fin']}\n";
    }

    $notif->sendByRol(
        roles: [UserRol::Administrador, UserRol::Recepcionista],
        titulo: "⚠️ Pagos atrasados",
        contenido: "Clientes con membresía vencida:\n\n" . $lista
    );
    $logger->log("Notificación de pagos atrasados enviada", CONTEXT);
}

// 3. Stock bajo
$bajoStock = $productoModel->obtenerBajoStock();
if ($bajoStock) {
    $lista = '';
    foreach ($bajoStock as $p) {
        $lista .= "- {$p['nombre']} ({$p['codigo_producto']}) - stock: {$p['stock_actual']} (mínimo: {$p['stock_minimo']})\n";
    }

    $notif->sendByRol(
        roles: [UserRol::Administrador],
        titulo: "📦 Stock bajo en productos",
        contenido: "Los siguientes productos tienen stock por debajo del mínimo:\n\n" . $lista
    );
    $logger->log("Notificación de stock bajo enviada", CONTEXT);
}

// 4. Equipos en mantenimiento
$equipos = $equipoModel->getEquiposEnMantenimiento();
if ($equipos) {
    $lista = '';
    foreach ($equipos as $e) {
        $lista .= "- {$e['nombre']} ({$e['codigo_equipo']}) - estado: {$e['estado']}, ubicación: {$e['ubicacion']}\n";
    }

    $notif->sendByRol(
        roles: [UserRol::Administrador],
        titulo: "🔧 Equipos en mantenimiento o fuera de servicio",
        contenido: "Equipos que requieren atención:\n\n" . $lista
    );
    $logger->log("Notificación de equipos en mantenimiento enviada", CONTEXT);
}

// 5. Mantenimientos preventivos programados (próximos 2 días)
$mantenimientos = $mantenimientoModel->getMantenimientosProximos(2);
if ($mantenimientos) {
    $lista = '';
    foreach ($mantenimientos as $m) {
        $lista .= "- {$m['equipo_nombre']} ({$m['codigo_equipo']}) - fecha: {$m['fecha']}, descripción: {$m['descripcion']}\n";
    }

    $notif->sendByRol(
        roles: [UserRol::Administrador],
        titulo: "📅 Mantenimientos preventivos programados",
        contenido: "Los siguientes mantenimientos están programados para los próximos días:\n\n" . $lista
    );
    $logger->log("Notificación de mantenimientos próximos enviada", CONTEXT);
}

// --- Notificaciones semanales (solo se ejecutan los lunes) ---
if ($diaSemana === 1) { // Lunes
    // 6. Nuevos clientes en la última semana
    $nuevos = $facturacionModel->obtenerNuevosClientes(7);
    if (!empty($nuevos)) {
        $lista = '';
        foreach ($nuevos as $c) {
            $lista .= "- {$c['nombre']} ({$c['cedula']}) - registrado el {$c['fecha_creacion']}\n";
        }
        $notif->sendByRol(
            roles: [UserRol::Administrador],
            titulo: "🆕 Nuevos clientes en la última semana",
            contenido: "Se registraron " . count($nuevos) . " nuevos clientes:\n\n" . $lista
        );
        $logger->log("Notificación de nuevos clientes enviada", CONTEXT);
    }

    // 7. Resumen financiero semanal
    $resumen = $facturacionModel->obtenerResumenFinancieroSemanal();
    $notif->sendByRol(
        roles: [UserRol::Administrador],
        titulo: "💰 Resumen financiero semanal",
        contenido: "Ingresos de la última semana:\n\n" .
            "Membresías: $" . number_format($resumen['total_membresias'], 2) . "\n" .
            "Ventas de productos: $" . number_format($resumen['total_ventas'], 2) . "\n" .
            "Total general: $" . number_format($resumen['total_general'], 2)
    );
    $logger->log("Notificación de resumen financiero enviada", CONTEXT);
}

$logger->log("Envio de notificaciones ejecutado correctamente", CONTEXT);
