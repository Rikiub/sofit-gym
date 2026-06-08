<?php

namespace App\Helpers;

use RuntimeException;

class ImagesManager
{
    /**
     * Guarda el archivo en la carpeta temporal.
     * Devuelve la ruta web relativa del archivo temporal (ej: '/uploads/tmp/abc123xyz.jpg').
     * * @param array{name: string, tmp_name: string, error: int, size: int} $file
     */
    public static function saveTemp(array $file): string
    {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new RuntimeException("Error de subida.");
        }

        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($extension, ['jpg', 'jpeg', 'png', 'webp', 'avif'], true)) {
            throw new RuntimeException("Formato no permitido.");
        }

        $tmpDir = rtrim(UPLOADS_TEMP_DIR, '/');
        $newName = bin2hex(random_bytes(10)) . '.' . $extension;
        $absolutePath = "{$tmpDir}/{$newName}";

        if (!is_dir($tmpDir) && !mkdir($tmpDir, 0755, true)) {
            throw new RuntimeException("Error al crear directorio temporal.");
        }

        if (!move_uploaded_file($file['tmp_name'], $absolutePath)) {
            throw new RuntimeException("No se pudo guardar el archivo temporal.");
        }

        // CAMBIO AQUÍ: Retorna la ruta web relativa para el frontend/controlador
        return BASE_DIR . '/uploads/tmp/' . $newName;
    }

    /**
     * Mueve el archivo temporal a su destino definitivo.
     * DEVUELVE: La ruta web lista para guardar en la BD (ej: '/uploads/usuarios/foto.jpg')
     */
    public static function moveFromTemp(string $filename, string $subfolder): string
    {
        $filename = basename($filename);
        $subfolder = trim($subfolder, '/');

        // 1. Definir la ruta web relativa que irá a la Base de Datos
        $webPath = "/uploads/{$subfolder}/{$filename}";

        // 2. Traducir esa ruta web a rutas absolutas del disco duro
        $absoluteTmp = rtrim(UPLOADS_TEMP_DIR, '/') . '/' . $filename;
        $absoluteDest = rtrim(ROOT_DIR, '/') . $webPath;
        $destDir = dirname($absoluteDest);

        if (!file_exists($absoluteTmp)) {
            throw new RuntimeException("La imagen temporal ya no existe o expiró.");
        }

        if (!is_dir($destDir) && !mkdir($destDir, 0755, true)) {
            throw new RuntimeException("No se pudo crear el directorio definitivo.");
        }

        if (!rename($absoluteTmp, $absoluteDest)) {
            throw new RuntimeException("Error al consolidar la imagen.");
        }

        return BASE_DIR . $webPath;
    }

    /**
     * Elimina un archivo utilizando directamente la ruta guardada en la base de datos.
     */
    public static function delete(string $webPath): bool
    {
        // Seguridad básica: Evitar que intenten salir de la carpeta pública (Path Traversal)
        if (str_contains($webPath, '..')) {
            return false;
        }

        // Traduce la ruta de la BD ('/uploads/...') a la del disco duro
        $absolutePath = rtrim(ROOT_DIR, '/') . '/' . ltrim($webPath, '/');

        if (file_exists($absolutePath) && is_file($absolutePath)) {
            return unlink($absolutePath);
        }

        return false;
    }
}
