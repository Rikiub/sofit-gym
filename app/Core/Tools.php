<?php

namespace App\Core;

use CuyZ\Valinor\Cache\FileSystemCache;
use CuyZ\Valinor\Cache\FileWatchingCache;
use CuyZ\Valinor\MapperBuilder;
use CuyZ\Valinor\Normalizer\Format;
use CuyZ\Valinor\NormalizerBuilder;
use PHPMailer\PHPMailer\PHPMailer;
use DateTimeInterface;

/** Colección de helpers que utilizan librerias externas. */
class Tools
{
    /** Obtener configuración estandar de PHPMailer. */
    public static function getMailer(): PHPMailer
    {
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
    }

    /** Mapea un array a un objeto y aplica validaciones.
     * 
     * @template T of object
     * @param string|class-string<T> $signature class-string de referencia.
     * @param $source Array principal a validar.
     * @return T
     */
    public static function map(string $signature, mixed $source)
    {
        return (new MapperBuilder())
            ->withCache(self::getValinorCache())
            ->allowScalarValueCasting() // Convertir strings en el tipo correspondiente
            ->allowSuperfluousKeys() // Ignorar keys extras
            ->allowUndefinedValues() // Valores indefinidos se convierten en null
            ->allowPermissiveTypes() // Permitir arrays y objetos
            ->supportDateFormats( // Formatos de fecha soportados
                DateTimeInterface::ATOM,
                'Y-m-d\TH:i',
                'Y-m-d H:i:s',
                'Y-m-d',
            )
            ->mapper()
            ->map($signature, $source);
    }

    /** Convierte un objeto o array en JSON. */
    public static function normalizeJson(mixed $source): string
    {
        return (new NormalizerBuilder())
            ->withCache(self::getValinorCache())
            ->registerTransformer( // Convertir atributos DateTime en strings
                fn(DateTimeInterface $date) => $date->format(DateTimeInterface::ATOM)
            )
            ->normalizer(Format::json()) // Convertir $source en JSON
            ->normalize($source);
    }

    private static function getValinorCache(): FileSystemCache|FileWatchingCache
    {
        $cache = new FileSystemCache(
            Config::get("fs.cache") . '/valinor'
        );

        if (Config::get("debug")) {
            $cache = new FileWatchingCache($cache);
        }

        return $cache;
    }
}
