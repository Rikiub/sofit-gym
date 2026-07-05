<?php

namespace App\Core;

use InvalidArgumentException;
use RuntimeException;

class ImageStorage
{
    private const TEMP_DIRNAME = "tmp";
    private const ALLOWED_TYPES = [
        'image/jpeg' => ['jpg', 'jpeg'],
        'image/png'  => ['png'],
        'image/webp' => ['webp'],
        'image/avif' => ['avif'],
    ];
    private const MAX_FILE_SIZE = 5 * 1024 * 1024; // 5 MB
    private const UPLOAD_ERR_MSGS = [
        UPLOAD_ERR_INI_SIZE   => 'El archivo excede el tamaño máximo permitido por el servidor.',
        UPLOAD_ERR_FORM_SIZE  => 'El archivo excede el tamaño máximo permitido por el formulario.',
        UPLOAD_ERR_PARTIAL    => 'El archivo solo se subió parcialmente.',
        UPLOAD_ERR_NO_FILE    => 'No se seleccionó ningún archivo.',
        UPLOAD_ERR_NO_TMP_DIR => 'Falta la carpeta temporal.',
        UPLOAD_ERR_CANT_WRITE => 'No se pudo escribir el archivo en el disco.',
        UPLOAD_ERR_EXTENSION  => 'Una extensión de PHP detuvo la subida del archivo.',
    ];

    // Paths
    private string $fsBase;
    private string $webBase;

    private string $fsUploads;
    private string $webUploads;

    private string $fsTemp;
    private string $webTemp;

    public function __construct()
    {
        $this->fsBase = Config::get("fs.base");
        $this->webBase = Config::get("web.base");

        $this->fsUploads = Config::get("fs.uploads");
        $this->webUploads = Config::get("web.uploads");

        $this->fsTemp = $this->fsUploads . "/" . self::TEMP_DIRNAME;
        $this->webTemp = $this->webUploads . "/" . self::TEMP_DIRNAME;
    }

    /**
     * Guarda el archivo en la carpeta temporal.
     *
     * @param array{name: string, tmp_name: string, error: int, size: int} $file
     * @return string Ruta web relativa del archivo temporal (ej: '/uploads/tmp/abc123.jpg')
     * @throws RuntimeException Si ocurre un error en la subida o validación.
     */
    public function saveTemp(array $file): string
    {
        $this->validateUpload($file);

        // Obtener extensión y MIME real
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        // Validar tipos
        if (!isset(self::ALLOWED_TYPES[$mime])) {
            throw new InvalidArgumentException(
                "Tipo MIME '{$mime}' no permitido."
            );
        }

        if (!in_array($extension, self::ALLOWED_TYPES[$mime], true)) {
            throw new InvalidArgumentException(
                "Extensión '{$extension}' no válida para el tipo MIME '{$mime}'. " .
                    "Extensiones permitidas: " . implode(', ', self::ALLOWED_TYPES[$mime])
            );
        }

        // Subir imagen
        $tmpDir = $this->ensureDirectory($this->fsTemp);
        $newName = $this->generateUniqueName($extension);
        $absolutePath = $tmpDir . DIRECTORY_SEPARATOR . $newName;

        if (!move_uploaded_file($file['tmp_name'], $absolutePath)) {
            throw new RuntimeException('No se pudo mover el archivo temporal.');
        }

        // Retornar ruta web relativa
        return $this->webTemp . "/" . $newName;
    }

    /**
     * Mueve un archivo temporal a su destino definitivo.
     *
     * @param string $filename Nombre del archivo temporal (ruta web o solo nombre)
     * @param string $subfolder Subcarpeta dentro de /uploads (ej: 'usuarios')
     * @return string Ruta web definitiva para guardar en la base de datos
     * @throws RuntimeException Si el archivo temporal no existe o no se puede mover.
     */
    public function moveFromTemp(string $filename, string $subfolder): string
    {
        $filename = basename($filename);
        $subfolder = trim($subfolder, '/');

        $webPath = $this->webUploads . "/{$subfolder}/{$filename}";
        $absoluteTmp = rtrim($this->fsTemp, '/') . '/' . $filename;
        $absoluteDest = rtrim($this->fsUploads, '/') . '/' . $subfolder . '/' . $filename;

        if (!file_exists($absoluteTmp)) {
            throw new RuntimeException("El archivo temporal '{$filename}' ya no existe.");
        }

        $destDir = dirname($absoluteDest);
        $this->ensureDirectory($destDir);

        if (!rename($absoluteTmp, $absoluteDest)) {
            throw new RuntimeException("No se pudo mover el archivo a '{$webPath}'.");
        }

        return $webPath;
    }

    /**
     * Elimina un archivo físico a partir de su ruta web guardada en BD.
     *
     * @param string $path Ruta web relativa (ej: '/uploads/usuarios/foto.jpg')
     * @return bool true si se eliminó correctamente, false si no existía o falló.
     */
    public function delete(string $path): bool
    {
        if (empty($path)) {
            return false;
        }

        // Normalizar ruta web
        $cleanPath = $path;
        if (str_starts_with($cleanPath, $this->webBase)) {
            $cleanPath = substr($cleanPath, strlen($this->webBase));
        }
        $cleanPath = '/' . ltrim($cleanPath, '/');

        // Prevenir directory traversal
        if (str_contains($cleanPath, '..')) {
            return false;
        }

        $absolutePath = rtrim($this->fsBase, '/') . $cleanPath;
        if (!file_exists($absolutePath) || !is_file($absolutePath)) {
            return false;
        }

        return unlink($absolutePath);
    }

    private function validateUpload(array $file): void
    {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $msg = self::UPLOAD_ERR_MSGS[$file['error']] ?? 'Error desconocido en la subida.';
            throw new RuntimeException("Subida fallida: {$msg}");
        }

        if (!is_uploaded_file($file['tmp_name'])) {
            throw new RuntimeException('El archivo no proviene de una subida válida.');
        }

        if ($file['size'] > self::MAX_FILE_SIZE) {
            throw new RuntimeException(sprintf(
                'El archivo excede el tamaño máximo permitido de %s.',
                self::formatBytes(self::MAX_FILE_SIZE)
            ));
        }
    }

    private function ensureDirectory(string $path): string
    {
        $path = rtrim($path, '/\\');

        if (!is_dir($path)) {
            if (!mkdir($path, 0755, true) && !is_dir($path)) {
                throw new RuntimeException("No se pudo crear el directorio '{$path}'.");
            }
        }

        return $path;
    }

    private function generateUniqueName(string $extension): string
    {
        $prefix = date('Ymd_His_');
        $random = bin2hex(random_bytes(6));
        $name = $prefix . $random . '.' . $extension;
        return $name;
    }

    private function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;

        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }

        return round($bytes, $precision) . ' ' . $units[$i];
    }
}
