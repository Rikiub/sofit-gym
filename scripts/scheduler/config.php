<?php

// Tareas programadas
return [
    [
        'name'     => 'Enviar notificaciones',
        'script'   => 'notify-users',
        'interval' => 60, // 1 minuto
    ],
    [
        'name'     => 'Limpiar bitacora',
        'script'   => 'limpiar-bitacora',
        'interval' => 86400, // 24 horas
    ],
    [
        'name'     => 'Respaldar base de datos',
        'script'   => 'respaldo-db',
        'interval' => 86400, // 24 horas
    ],
];
