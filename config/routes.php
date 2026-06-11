<?php

use App\Helpers\Auth\Rol;

const Todos = [Rol::Administrador, Rol::Entrenador, Rol::Recepcionista];

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
        "actions" => [
            "find" => Todos,
            "update" => Todos,
        ],
    ],
    "bitacora" => [
        "roles" => [Rol::Administrador],
    ],
];
