<?php

use App\Models\BitacoraModel;
use App\Services\Logging\BitacoraLogger;

require 'bootstrap/app.php';

// CONFIG
$diasRetencion = 30;

// LOGIC
$biracoraModel = new BitacoraModel();
$logger = new BitacoraLogger();

try {
    $biracoraModel->limpiarRegistros($diasRetencion);
    $logger->info("Limpieza automática de registros en la bitacora con más de {dias_retencion} dias", [
        'dias_retencion' => $diasRetencion,
        'modulo' => 'sistema',
        'accion' => 'limpieza_bitacora'
    ]);
} catch (Exception $e) {
    $logger->error("Error al limpiar bitácora", [
        'error' => $e->getMessage(),
        'modulo' => 'sistema',
        'accion' => 'limpieza_bitacora'
    ]);
}
