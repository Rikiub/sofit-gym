<?php

use App\Services\RespaldoService;

require "bootstrap/app.php";

$respaldo = new RespaldoService();
$respaldo->backup();

// echo json_encode($respaldo->getAll());
