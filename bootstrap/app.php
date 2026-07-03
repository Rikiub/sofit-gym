<?php

/**
 * @return \Psr\Container\ContainerInterface
 */

use App\Core\Config;
use DI\ContainerBuilder;

// Cargar composer autoload
require_once __DIR__ . '/../vendor/autoload.php';

// Cargar variables de entorno
$dotenv = \Dotenv\Dotenv::createImmutable(__DIR__ . "/..");
$dotenv->safeLoad();

// Cargar configuración
Config::load(__DIR__ . '/../config/app.php');

// Construir contenedor DI (PHP-DI)
$builder = new ContainerBuilder();
$builder->addDefinitions(__DIR__ . '/../config/container.php');
$builder->useAttributes(true);

if (!Config::get("debug")) {
    $builder->enableCompilation(Config::get("fs.cache") . '/php-di');
}

$container = $builder->build();

// Devolver contenedor para que quien lo requiera lo use
return $container;
