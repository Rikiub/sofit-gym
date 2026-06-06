<?php

// Cargar composer autoload
require 'vendor/autoload.php';

// Cargar entorno desde el archivo .env
$dotenv = \Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Cargar front-controller
require 'app/bootstrap.php';
