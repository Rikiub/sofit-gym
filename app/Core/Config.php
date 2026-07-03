<?php

namespace App\Core;

/** Gestor de configuración de la aplicacion.
 * Accede a los atributos con dot-notation, ejemplo:
 * 
 * ```
 * Config::get("web.assets")
 * ```
 */
class Config
{
    private static array $data = [];

    public static function load(string $configFile): void
    {
        if (empty(self::$data)) {
            self::$data = require $configFile;
            date_default_timezone_set(self::get("timezone.zone"));
        }
    }

    public static function get(string $key): mixed
    {
        $keys = explode('.', $key);
        $value = self::$data;

        foreach ($keys as $segment) {
            if (!is_array($value) || !array_key_exists($segment, $value)) {
                return null;
            }
            $value = $value[$segment];
        }

        return $value;
    }
}
