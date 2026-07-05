<?php

use App\Services\RespaldoService;
use Psr\Container\ContainerInterface;

/** @var ContainerInterface */
$container = require "bootstrap/app.php";

/** @var RespaldoService */
$respaldo = $container->get(RespaldoService::class);
$respaldo->backup();
