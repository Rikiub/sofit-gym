<?php

use App\Models\BitacoraModel;
use App\Models\Level;

require 'bootstrap/app.php';

// CONFIG
$diasRetencion = 30;

// LOGIC
$biracora = new BitacoraModel();

try {
    $biracora->limpiarRegistros($diasRetencion);
    $biracora->log("Limpieza automática de registros en la bitacora con más de {dias_retencion} dias", [
        'dias_retencion' => $diasRetencion,
        'modulo' => 'sistema',
        'accion' => 'limpieza_bitacora'
    ]);
} catch (Exception $e) {
    $biracora->log("Error al limpiar bitácora", [
        'error' => $e->getMessage(),
        'modulo' => 'sistema',
        'accion' => 'limpieza_bitacora'
    ], Level::ERROR);
}
