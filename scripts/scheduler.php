<?php
// Programador de tareas para desarrollo local
// CONFIGURACIÓN
$tasks = [
    [
        'name'     => 'Enviar notificaciones',
        'script'   => 'notify-users.php',
        'interval' => 60, // 1 minuto
    ],
    [
        'name'     => 'Limpieza de bitacora',
        'script'   => 'limpiar-bitacora.php',
        'interval' => 86400, // 24 horas
    ],
];

// IMPLEMENTACIÓN
set_time_limit(0);

echo "🕒 Programador iniciado. Ejecutando " . count($tasks) . " tareas.\n";
echo "Presiona Ctrl+C para parar.\n";
echo str_repeat('-', 50) . "\n";

// Seguimiento de cuándo se ejecutó cada tarea por última vez
$lastRun = [];
$startTime = time();

foreach ($tasks as $index => $job) {
    $lastRun[$index] = $startTime;
}

while (true) {
    $now = time();

    foreach ($tasks as $index => $job) {
        $elapsed = $now - $lastRun[$index];

        if ($elapsed >= $job['interval']) {
            echo date('H:i:s') . " - [{$job['name']}] Ejecutando...\n";

            try {
                // Ejecutar script
                include $job['script'];
                echo "✅ Completado.\n";
            } catch (Throwable $e) {
                echo "❌ Error: " . $e->getMessage() . "\n";
            }

            // Actualizar tiempo de la ultima tarea
            $lastRun[$index] = $now;
        }
    }

    sleep(1);
}
