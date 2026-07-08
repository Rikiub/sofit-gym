<?php

/**
 * Funciones globales cargadas por composer.
 */

namespace App\Core;

use DateTimeInterface;

/** Convierte un objeto DateTime en una string compatible con bases de datos */
function toDbDate(?DateTimeInterface $date): ?string
{
    return $date ? $date->format("Y-m-d H:i:s") : null;
}

/** Convierte los bytes en una unidad user-friendly. */
function formatSize(int $bytes): string
{
    $units = ['B', 'KB', 'MB', 'GB'];
    $i = 0;

    while ($bytes >= 1024 && $i < count($units) - 1) {
        $bytes /= 1024;
        $i++;
    }

    return round($bytes, 2) . ' ' . $units[$i];
}

/** Convierte un array en una lista de atributos HTML */
function stringifyAttributes(array $inputAttributes): string
{
    $htmlParts = [];

    foreach ($inputAttributes as $key => $value) {
        // Handle boolean attributes (true means just render the key, false means skip it)
        if ($value === true) {
            $htmlParts[] = $key;
        } elseif ($value !== false && $value !== null) {
            // Securely escape the value for HTML attributes
            $escapedValue = htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
            $htmlParts[] = sprintf('%s="%s"', $key, $escapedValue);
        }
    }

    // Join them with a single space
    return implode(' ', $htmlParts);
}

/** Convierte cualquier dato y lo codifica en JSON escapeandolo */
function encodeToJson(mixed $js): string
{
    return json_encode($js, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE);
}

/** Convierte una string JSON en texto seguro */
function escapeJs(string $js): string
{
    return htmlspecialchars($js, ENT_QUOTES, 'UTF-8');
}
