<?php

use App\Controllers\FrontController;

// Cargar autoload y variables de entorno
require 'bootstrap/app.php';

// Iniciar aplicacion
$front = new FrontController();
$front->run();
