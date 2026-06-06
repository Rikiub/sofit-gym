<?php

// Cargar composer autoload
require 'vendor/autoload.php';

// Cargar entorno desde el archivo .env
$dotenv = \Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

// Cargar front-controller
require 'app/bootstrap.php';
