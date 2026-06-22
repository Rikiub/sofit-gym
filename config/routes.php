<?php

/** Mapeo de rutas basico
 * 
 * Si una ruta tiene el atributo "permisos"
 * Entonces solo permitira ingresar a los usuarios con dichos permisos.
 */
return [
    "clientes" => [
        "permisos" => ["clientes:ver"],
    ],
    "clientesItem" => [
        "permisos" => ["clientes:ver"],
    ],
    "trabajadores" => [
        "permisos" => ["trabajadores:ver"],
    ],
    "clases" => [
        "permisos" => ["clases:ver"],
    ],
    "productos" => [
        "permisos" => ["productos:ver"],
    ],
    "rutinas" => [
        "permisos" => ["rutinas:ver"],
    ],
    "equipos" => [
        "permisos" => ["equipos:ver"],
    ],
    "asistencia" => [
        "permisos" => ["asistencia:ver"],
    ],
    "asistente" => [
        "permisos" => ["asistente:ver"],
    ],
    "facturacion" => [
        "permisos" => ["facturacion:ver"],
    ],
    "usuarios" => [
        "permisos" => ["usuarios:ver"],
        "actions" => [
            "find" => ["todos"],
            "update" => ["usuarios:editar"],
        ],
    ],
    "roles" => [
        "permisos" => ["roles:ver"],
    ],
    "bitacora" => [
        "permisos" => ["bitacora:ver"],
    ],
];
