<?php

namespace App\Services;

use App\Core\Config;
use App\Services\Logging\BitacoraLogger;
use App\Services\Logging\LogLevel;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RuntimeException;

use function App\Core\formatSize;

class RespaldoService
{
    private const DB_NAMES = ["sofit_gym", "sofit_gym_seguridad"];

    private string $cmdPath;
    private string $backupsDir;

    public function __construct(private BitacoraLogger $logger)
    {
        $this->cmdPath = Config::get("db.path.mysqldump");
        $this->backupsDir = Config::get("fs.backups");
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
        $host = Config::get("db.host");
        $username = Config::get("db.username");
        $password = Config::get("db.password");

        $dir = $this->generateTimestampDir();
        mkdir($dir, 0755, true);

        foreach (self::DB_NAMES as $name) {
            $backupPath = "{$dir}/{$name}.sql";
            $cmd = sprintf(
                '"%s" --opt -h %s -u %s --password="%s" "%s" > "%s"',
                $this->cmdPath,
                $host,
                $username,
                $password,
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
                    LogLevel::ERROR,
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

    private function generateTimestampDir(): string
    {
        return $this->backupsDir . "/" . date("Y/m/d/H-i-s");
    }
}
