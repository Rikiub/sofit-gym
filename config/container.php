<?php

use App\Core\Auth\UsuarioSession;
use App\Core\Database;
use App\Core\Plates\AssetExtension;
use CuyZ\Valinor\Cache\FileSystemCache;
use CuyZ\Valinor\Cache\FileWatchingCache;
use CuyZ\Valinor\Mapper\TreeMapper;
use CuyZ\Valinor\MapperBuilder;
use CuyZ\Valinor\Normalizer\Format;
use CuyZ\Valinor\Normalizer\Normalizer;
use CuyZ\Valinor\NormalizerBuilder;
use League\Plates\Template\Theme;
use League\Plates\Engine;
use LLPhant\GeminiOpenAIConfig;
use PHPMailer\PHPMailer\PHPMailer;

/** Configuración de PHP-DI
 * Aqui se definen los objetos a instanciar en la aplicación automaticamente.
 */
return [
    // Conexion a la base de datos
    Database::class => function () {
        return new Database(
            host: $_ENV['DB_HOST'] ?? "localhost",
            database: $_ENV['DB_DATABASE'] ?? 'sofit_gym',
            username: $_ENV['DB_USERNAME'] ?? 'root',
            password: $_ENV['DB_PASSWORD'] ?? '',
        );
    },

    // Configuración Gemini
    GeminiOpenAIConfig::class => function () {
        $config = new GeminiOpenAIConfig();
        $config->apiKey = $_ENV["GEMINI_API_KEY"];
        $config->model = "gemini-2.5-flash-lite";
        return $config;
    },

    // Directorios donde cargar vistas/plantillas
    Engine::class => function () {
        $engine = Engine::fromTheme(Theme::hierarchy([
            Theme::new('app/views/base', 'Base'),
            Theme::new('app/views/components', 'Components'),
            Theme::new('app/views/pages', 'Page'),
        ]))
            ->loadExtension(new AssetExtension(ASSETS_DIR));

        $usuario = UsuarioSession::getCurrent();
        $engine->addData(["sesion_usuario" => $usuario]);
        return $engine;
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

        if ($from) {
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
