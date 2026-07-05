<?php

use App\Core\Config;
use App\Core\Database;
use App\Core\Plates\AssetExtension;
use App\Services\Auth\UserSession;
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

/**
 * Configuración de PHP-DI.
 * Deficiones iniciales de los objetos a inyectar en la aplicación automaticamente.
 */
return [
    // Conexion a la base de datos
    Database::class => function () {
        return new Database(
            host: Config::get("db.host"),
            database: Config::get("db.database"),
            username: Config::get("db.username"),
            password: Config::get("db.password"),
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
            Theme::new('app/views/emails', 'Emails'),
            Theme::new('app/views/pages', 'Page'),
        ]))
            ->loadExtension(new AssetExtension(Config::get("web.assets")));

        $user = UserSession::get();
        $engine->addData(["sesion_usuario" => $user]);
        return $engine;
    },

    PHPMailer::class => function () {
        $mail = new PHPMailer(true);

        // Configuración SMTP
        $mail->isSMTP();
        $mail->Host = Config::get("mail.host");
        $mail->SMTPAuth = true;

        // Configuracion compatible con Gmail
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        $mail->CharSet = 'UTF-8';

        // Credenciales
        $mail->Username = Config::get("mail.username");
        $mail->Password = Config::get("mail.password");

        // Remitente
        $from = Config::get("mail.from_address");
        $name = Config::get("mail.from_name");

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
        $cache = new FileSystemCache(
            Config::get("web.cache") . '/valinor'
        );
        if (Config::get("debug")) {
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
        $cache = new FileSystemCache(
            Config::get("web.cache") . '/valinor'
        );
        if (Config::get("debug")) {
            $cache = new FileWatchingCache($cache);
        }

        return (new NormalizerBuilder())
            ->withCache($cache)
            ->registerTransformer(fn(DateTimeInterface $date) => $date->format(DateTimeInterface::ATOM))
            ->normalizer(Format::json());
    },
];
