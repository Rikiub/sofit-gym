<?php

namespace App\Services;

use App\Core\Config;
use App\Services\Logging\BitacoraLogger;
use App\Services\Logging\Level;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RuntimeException;

use function App\Core\formatSize;

class RespaldoService
{
    private const DB_NAMES = ["sofit_gym", "sofit_gym_seguridad"];

    private string $backupsDir;

    private string $mysqlPath;
    private string $mysqldumpPath;

    private string $dbHost;
    private string $dbUsername;
    private string $dbPassword;

    public function __construct(
        private $logger = new BitacoraLogger()
    ) {
        $this->backupsDir = Config::get("fs.backups");

        $this->mysqlPath = Config::get("db.path.mysql");
        $this->mysqldumpPath = Config::get("db.path.mysqldump");

        $this->dbHost = Config::get("db.host");
        $this->dbUsername = Config::get("db.username");
        $this->dbPassword = Config::get("db.password");
    }

    public function getAll(): array
    {
        $backups = [];
        $root = $this->backupsDir;

        if (!is_dir($root)) {
            return $backups;
        }

        // Escanear recursivamente los directorios en busqueda de este patron: YYYY/MM/DD/HH-MM-SS
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($root, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $item) {
            // Solo procesar directorios
            if (!$item->isDir()) {
                continue;
            }

            $path = $item->getPathname();
            $dirName = $item->getFilename();

            // Verificar que el directorio encaja con HH-MM-SS
            if (!preg_match('/^\d{2}-\d{2}-\d{2}$/', $dirName)) {
                continue;
            }

            // Asegurarse de que los directorios estan correctamente nombrados
            $parent = $item->getPath();
            $day = basename($parent);
            if (!preg_match('/^\d{2}$/', $day)) continue;

            $parent2 = dirname($parent);
            $month = basename($parent2);
            if (!preg_match('/^\d{2}$/', $month)) continue;

            $parent3 = dirname($parent2);
            $year = basename($parent3);
            if (!preg_match('/^\d{4}$/', $year)) continue;

            // Ahora tenemos el directorio de la timestamp
            $datetime = sprintf('%s-%s-%s %s', $year, $month, $day, str_replace('-', ':', $dirName));

            // Escanear por archivos .sql
            $sqlFiles = glob($path . '/*.sql');
            if (empty($sqlFiles)) {
                continue; // Ignorar directorios vacios
            }

            $files = [];
            $totalSize = 0;
            foreach ($sqlFiles as $sqlFile) {
                $dbName = basename($sqlFile, '.sql');
                $size = filesize($sqlFile);
                $files[$dbName] = [
                    'path' => $sqlFile,
                    'size' => $size,
                    'size_human' => formatSize($size),
                ];
                $totalSize += $size;
            }

            $backups[] = [
                'datetime' => $datetime,
                'timestamp' => "$year/$month/$day/$dirName",
                'path' => $path,
                'files' => $files,
                'total_size' => $totalSize,
                'total_size_human' => formatSize($totalSize),
                'count' => count($files),
            ];
        }

        // Sortear por más recientes
        usort($backups, function ($a, $b) {
            return strtotime($b['datetime']) - strtotime($a['datetime']);
        });

        return $backups;
    }

    public function backup()
    {
        $dir = $this->generateTimestampDir();
        mkdir($dir, 0755, true);

        foreach (self::DB_NAMES as $name) {
            $backupPath = "{$dir}/{$name}.sql";
            $cmd = sprintf(
                '"%s" --opt --routines --triggers --events --single-transaction -h %s -u %s --password="%s" "%s" > "%s"',
                $this->mysqldumpPath,
                $this->dbHost,
                $this->dbUsername,
                $this->dbPassword,
                $name,
                $backupPath,
            );

            $output = [];
            $returnCode = 0;
            exec($cmd, $output, $returnCode);

            if ($returnCode !== 0) {
                $msg = "Respaldo de base de datos fallido para {$name}";

                $this->logger->error($msg, [
                    "modulo" => "respaldos",
                    "accion" => "backup",
                ]);
                $this->logger->console(
                    Level::ERROR,
                    "Output: " . implode("\n", $output)
                );

                throw new RuntimeException($msg);
            }
        }

        $this->logger->info("Respaldo de base de datos creado con exito", [
            "modulo" => "respaldos",
            "accion" => "backup",
        ]);
    }

    /**
     * Elimina un respaldo completo.
     *
     * @param string $timestamp Ej: "2026/07/05/14-30-00"
     * @throws RuntimeException Si no existe o no se puede eliminar
     */
    public function delete(string $timestamp): void
    {
        $dir = $this->backupsDir . '/' . $timestamp;

        if (!is_dir($dir)) {
            throw new RuntimeException("El directorio de respaldo no existe: $dir");
        }

        // Eliminar recursivamente
        $this->deleteDirectory($dir);

        $this->logger->info("Respaldo eliminado: $timestamp", [
            'modulo' => 'respaldos',
            'accion' => 'delete'
        ]);
    }

    /**
     * Elimina un directorio y todo su contenido recursivamente.
     */
    private function deleteDirectory(string $dir): void
    {
        if (!is_dir($dir)) {
            return;
        }

        $items = scandir($dir);
        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }

            $path = $dir . DIRECTORY_SEPARATOR . $item;
            if (is_dir($path)) {
                $this->deleteDirectory($path);
            } else {
                unlink($path);
            }
        }

        rmdir($dir);
    }

    private function generateTimestampDir(): string
    {
        return $this->backupsDir . "/" . date("Y/m/d/H-i-s");
    }
}
