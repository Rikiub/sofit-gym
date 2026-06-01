<?php

use App\Helpers\Logger\BitacoraLogger;
use App\Helpers\Plates\AssetExtension;
use CuyZ\Valinor\Mapper\TreeMapper;
use CuyZ\Valinor\Normalizer\Format;
use CuyZ\Valinor\Normalizer\Normalizer;
use CuyZ\Valinor\MapperBuilder;
use CuyZ\Valinor\NormalizerBuilder;
use League\Plates\Template\Theme;
use League\Plates\Engine;
use Psr\Log\LoggerInterface;

use function DI\get;

return [
    // Conexion PDO a la base de datos
    PDO::class => function () {
        $host = $_ENV['DB_HOST'] ?? 'localhost';
        $database = $_ENV['DB_DATABASE'] ?? 'sofit_gym';
        $username = $_ENV['DB_USERNAME'] ?? 'root';
        $password = $_ENV['DB_PASSWORD'] ?? '';
        $charset = 'utf8mb4';

        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::MYSQL_ATTR_INIT_COMMAND => sprintf("SET time_zone = '%s'", TIMEZONE_OFFSET),
        ];

        $dsn = "mysql:host={$host};dbname={$database};charset={$charset};";

        try {
            return new PDO(
                dsn: $dsn,
                username: $username,
                password: $password,
                options: $options
            );
        } catch (PDOException $e) {
            throw new RuntimeException('Failed database connection: ' . $e->getMessage());
        }
    },
    // Logger de la bitacora
    LoggerInterface::class => get(BitacoraLogger::class),

    // Directorios donde cargar vistas/plantillas
    Engine::class => function () {
        return Engine::fromTheme(Theme::hierarchy([
            Theme::new('app/views/base', 'Base'),
            Theme::new('app/views/components', 'Components'),
            Theme::new('app/views/pages', 'Page'),
        ]))
            ->loadExtension(new AssetExtension(ASSETS_DIR));
    },

    // Valinor: Mapper
    // Utilizado para convertir arrays en DTOs
    // y validarlos en el proceso
    TreeMapper::class => function () {
        return (new MapperBuilder())
            ->allowScalarValueCasting()
            ->allowSuperfluousKeys()
            ->allowUndefinedValues()
            ->supportDateFormats(
                DateTimeInterface::ATOM,
                'Y-m-d\TH:i',
                'Y-m-d H:i:s',
                'Y-m-d',
            )
            ->mapper();
    },
    // Valinor: Normalizer
    // Utilizado para convertir arrays en JSON
    // y convertir tipos como DateTime en texto
    Normalizer::class => function () {
        return (new NormalizerBuilder())
            ->registerTransformer(fn(DateTimeInterface $date) => $date->format(DateTimeInterface::ATOM))
            ->normalizer(Format::json());
    },
];
