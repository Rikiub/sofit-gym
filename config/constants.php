<?php

/** Constantes globales accesibles por toda la aplicacion. */

// Directorios
define("ROOT_DIR", dirname(__DIR__));
define("CACHE_DIR", ROOT_DIR . "/bootstrap/cache");
define("UPLOADS_DIR", ROOT_DIR . "/uploads");
define("UPLOADS_TEMP_DIR", UPLOADS_DIR . "/tmp");

define('BASE_DIR', rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\'));
define('ASSETS_DIR', BASE_DIR . '/assets');

// Modo Desarrollo
define('DEBUG', filter_var($_ENV["DEBUG"] ?? true, FILTER_VALIDATE_BOOLEAN));

// Timezones
define("TIMEZONE", $_ENV["TIMEZONE"] ?? 'America/Caracas');
define('TIMEZONE_OFFSET', (new DateTime('now', new DateTimeZone(TIMEZONE)))->format('P'));
date_default_timezone_set(TIMEZONE);
