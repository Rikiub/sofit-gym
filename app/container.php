<?php

use App\Helpers\Logger\BitacoraLogger;
use App\Helpers\Plates\AssetExtension;
use CuyZ\Valinor\Cache\FileSystemCache;
use CuyZ\Valinor\Cache\FileWatchingCache;
use CuyZ\Valinor\Mapper\TreeMapper;
use CuyZ\Valinor\Normalizer\Format;
use CuyZ\Valinor\Normalizer\Normalizer;
use CuyZ\Valinor\MapperBuilder;
use CuyZ\Valinor\NormalizerBuilder;
use League\Plates\Template\Theme;
use League\Plates\Engine;
use LLPhant\GeminiOpenAIConfig;
use PHPMailer\PHPMailer\PHPMailer;
use Psr\Log\LoggerInterface;

use function DI\get;

/** Configuración de PHP-DI
 * Aqui se definen los objetos a instanciar en los controladores automaticamente.
 */
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

    // Configuración Gemini
    GeminiOpenAIConfig::class => function () {
        $config = new GeminiOpenAIConfig();
        $config->apiKey = $_ENV["GEMINI_API_KEY"];
        $config->model = "gemini-2.5-flash-lite";
        return $config;
    },

    // Directorios donde cargar vistas/plantillas
    Engine::class => function () {
        return Engine::fromTheme(Theme::hierarchy([
            Theme::new('app/views/base', 'Base'),
            Theme::new('app/views/components', 'Components'),
            Theme::new('app/views/pages', 'Page'),
        ]))
            ->loadExtension(new AssetExtension(ASSETS_DIR));
    },

    PHPMailer::class => function () {
        $mail = new PHPMailer(true);

        // Configuración SMTP
        $mail->isSMTP();
        $mail->Host = $_ENV["MAIL_HOST"] ?? 'smtp.gmail.com';
        $mail->SMTPAuth = true;

        // Configuracion compatible con Gmail
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        $mail->CharSet = 'UTF-8';

        // Credenciales
        $mail->Username = $_ENV["MAIL_USERNAME"] ?? "";
        $mail->Password = $_ENV["MAIL_PASSWORD"] ?? "";

        // Remitente
        $from = $_ENV["MAIL_FROM_ADDRESS"] ?? null;
        $name = $_ENV["MAIL_FROM_NAME"] ?? null;

        if ($from && $name) {
            $mail->setFrom(
                $from,
                $name ?? 'Soporte Sofit GYM'
            );
        }

        return $mail;
    },

    // Valinor: Mapper
    // Utilizado para convertir arrays en DTOs
    // y validarlos en el proceso
    TreeMapper::class => function () {
        $cache = new FileSystemCache(CACHE_DIR . '/valinor');
        if (DEBUG) {
            $cache = new FileWatchingCache($cache);
        }

        return (new MapperBuilder())
            ->withCache($cache)
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
        $cache = new FileSystemCache(CACHE_DIR . '/valinor');
        if (DEBUG) {
            $cache = new FileWatchingCache($cache);
        }

        return (new NormalizerBuilder())
            ->withCache($cache)
            ->registerTransformer(fn(DateTimeInterface $date) => $date->format(DateTimeInterface::ATOM))
            ->normalizer(Format::json());
    },
];
