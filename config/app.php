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
        "backups" => $fsBase . "/database/backups",
    ],
    // Rutas relativas
    "web" => [
        "base" => $webBase,
        "assets" => $webBase . '/assets',
        "uploads" => $webBase . "/uploads",
    ],

    // Base de datos
    "db" => [
        "host" => $_ENV['DB_HOST'] ?? "localhost",
        "database" => $_ENV['DB_DATABASE'] ?? "sofit_gym",
        "username" =>  $_ENV['DB_USERNAME'] ?? 'root',
        "password" => $_ENV['DB_PASSWORD'] ?? '',
        "path" => [
            "mysqldump" => $_ENV['DB_PATH_MYSQLDUMP'] ?? 'mysqldump',
        ],
    ],
    // Credenciales de correo
    "mail" => [
        "host" => $_ENV["MAIL_HOST"] ?? 'smtp.gmail.com',
        "username" => $_ENV["MAIL_USERNAME"] ?? "",
        "password" => $_ENV["MAIL_PASSWORD"] ?? "",
        "from_address" => $_ENV["MAIL_FROM_ADDRESS"] ?? null,
        "from_name" => $_ENV["MAIL_FROM_NAME"] ?? 'Soporte Sofit GYM',
    ],

    // Modo desarrollo
    "debug" => filter_var(
        $_ENV["DEBUG"] ?? true,
        FILTER_VALIDATE_BOOLEAN
    ),

    "timezone" => [
        "zone" => $timezone,
        "offset" => $timezoneOffset,
    ],
];
