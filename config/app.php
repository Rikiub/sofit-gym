<?php

$fsBase = dirname(__DIR__);
$webBase = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');

$timezone = $_ENV["TIMEZONE"] ?? 'America/Caracas';
$timezoneOffset = (new DateTime('now', new DateTimeZone($timezone)))->format('P');

/** Configuración de la aplicacion. */
return [
    // Rutas absolutas
    "fs" => [
        "base" => $fsBase,
        "cache" => $fsBase . "/bootstrap/cache",
        "uploads" => $fsBase . "/uploads",
    ],
    // Rutas relativas
    "web" => [
        "base" => $webBase,
        "assets" => $webBase . '/assets',
        "uploads" => $webBase . "/uploads",
    ],
    "timezone" => [
        "zone" => $timezone,
        "offset" => $timezoneOffset,
    ],
    // Modo desarrollo
    "debug" => filter_var(
        $_ENV["DEBUG"] ?? true,
        FILTER_VALIDATE_BOOLEAN
    ),
];
