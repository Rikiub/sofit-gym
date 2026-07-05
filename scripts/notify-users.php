<?php

use App\Services\Auth\UserRol;
use App\Services\Logging\BitacoraLogger;
use App\Services\NotificacionService;
use Psr\Container\ContainerInterface;

/** @var ContainerInterface */
$container = require 'bootstrap/app.php';

/** @var NotificacionService */
$notif = $container->get(NotificacionService::class);
/** @var BitacoraLogger */
$logger = $container->get(BitacoraLogger::class);

/**
$notif->sendByRol(
    roles: [UserRol::Administrador],
    titulo: "xd",
    contenido: "xd"
);
$logger->info("Limpieza automática de bitácora ejecutada", [
    'dias_retencion' => $dias,
    'modulo' => 'sistema',
    'accion' => 'limpieza_bitacora'
]);
 */
