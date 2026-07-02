<?php

use App\Controllers\FrontController;

// Cargar autoload, variables de entorno y construir contenedor DI
$container = require 'bootstrap/app.php';

// Iniciar aplicacion
$front = $container->get(FrontController::class);
$front->run();
