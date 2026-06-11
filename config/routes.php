<?php

use App\Helpers\Auth\Rol;

/** Mapeo de rutas basico
 * 
 * Si una ruta tiene el atributo "roles"
 * Entonces solo permitira ingresar a los usuarios con dichos roles.
 */
return [
    "trabajadores" => [
        "roles" => [Rol::Administrador],
    ],
    "clases" => [
        "roles" => [Rol::Administrador, Rol::Entrenador],
    ],
    "facturacion" => [
        "roles" => [Rol::Administrador],
    ],
    "usuarios" => [
        "roles" => [Rol::Administrador],
    ],
    "bitacora" => [
        "roles" => [Rol::Administrador],
    ],
];
