<?php

namespace App\Core;

use CuyZ\Valinor\Cache\FileSystemCache;
use CuyZ\Valinor\Cache\FileWatchingCache;
use CuyZ\Valinor\MapperBuilder;
use CuyZ\Valinor\Normalizer\Format;
use CuyZ\Valinor\NormalizerBuilder;
use DateTimeInterface;

class Tools
{
    /** Mapea un array en un objeto y aplica validaciones.
     * 
     * @template T of object
     * @param string|class-string<T> $signature
     * @return T
     */
    public static function map(string $signature, mixed $source)
    {
        return (new MapperBuilder())
            ->withCache(self::getCache())
            ->allowScalarValueCasting()
            ->allowSuperfluousKeys()
            ->allowUndefinedValues()
            ->supportDateFormats(
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
            ->withCache(self::getCache())
            ->registerTransformer(fn(DateTimeInterface $date) => $date->format(DateTimeInterface::ATOM))
            ->normalizer(Format::json())
            ->normalize($source);
    }

    private static function getCache(): FileSystemCache|FileWatchingCache
    {
        $cache = new FileSystemCache(
            Config::get("web.cache") . '/valinor'
        );
        if (Config::get("debug")) {
            $cache = new FileWatchingCache($cache);
        }
        return $cache;
    }
}
