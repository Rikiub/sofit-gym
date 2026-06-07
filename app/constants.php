<?php

/** Constantes globales accesibles por toda la aplicacion. */

// Directorios
define("ROOT_DIR", dirname(__DIR__));
define("CACHE_DIR", ROOT_DIR . "/.cache");

define('BASE_DIR', rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\'));
define('ASSETS_DIR', BASE_DIR . '/assets');
define("UPLOADS_DIR", BASE_DIR . "/uploads");

// Modo Desarrollo
define('DEBUG', filter_var($_ENV["DEBUG"] ?? true, FILTER_VALIDATE_BOOLEAN));

// Timezones
define("TIMEZONE", $_ENV["TIMEZONE"] ?? 'America/Caracas');
define('TIMEZONE_OFFSET', (new DateTime('now', new DateTimeZone(TIMEZONE)))->format('P'));
date_default_timezone_set(TIMEZONE);

// Otros
// Necesarios para el front-controller
define("CONTAINER_FILE", 'app/container.php');
define("CONTROLLERS_NAMESPACE", 'App\Controllers');
