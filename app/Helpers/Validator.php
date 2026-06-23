<?php

namespace App\Helpers;

use DateTimeInterface;

/**
 * En caso de que tengan validaciones reutilizables
 * agregenlas a esta clase.
 */
class Validator
{
    public static function dateToString(?DateTimeInterface $date): ?string
    {
        return $date ? $date->format("Y-m-d H:i:s") : null;
    }
}
