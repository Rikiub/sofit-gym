<?php

use App\Core\Config;

// Cargar composer autoload
require_once __DIR__ . '/../vendor/autoload.php';

// Cargar variables de entorno
$dotenv = \Dotenv\Dotenv::createImmutable(__DIR__ . "/..");
$dotenv->safeLoad();

// Cargar configuración
Config::load(__DIR__ . '/../config/app.php');
