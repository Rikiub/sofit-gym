<?php

/**
 * @return \Psr\Container\ContainerInterface
 */

// Cargar composer autoload
require_once __DIR__ . '/../vendor/autoload.php';

// Cargar variables de entorno
$dotenv = \Dotenv\Dotenv::createImmutable(__DIR__ . "/..");
$dotenv->safeLoad();

// Cargar constantes globales
require_once __DIR__ . '/../config/constants.php';

// Construir el contenedor DI (PHP-DI)
use DI\ContainerBuilder;

$builder = new ContainerBuilder();
$builder->addDefinitions(__DIR__ . '/../config/container.php');
$builder->useAttributes(true);

if (!DEBUG) {
    $builder->enableCompilation(CACHE_DIR . '/php-di');
}

$container = $builder->build();

// Devolver el contenedor para que quien lo requiera lo use
return $container;
