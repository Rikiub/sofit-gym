<?php

use App\Models\BitacoraModel;
use App\Services\Logging\BitacoraLogger;
use Psr\Container\ContainerInterface;

// CONFIG
$diasRetencion = 30;

/** @var ContainerInterface */
$container = require 'bootstrap/app.php';

/** @var BitacoraModel */
$biracoraModel = $container->get(BitacoraModel::class);
/** @var BitacoraLogger */
$logger = $container->get(BitacoraLogger::class);

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
