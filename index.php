<?php

// Cargar composer autoload
require 'vendor/autoload.php';

// Cargar entorno desde el archivo .env
// Luego cargar constantes
$dotenv = \Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();
require 'config/constants.php';

// Iniciar
$front = new \App\Controllers\FrontController;
$front->run();
