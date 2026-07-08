<?php

namespace App\Models;

use App\Core\Database;

abstract class Model
{
    protected const DB_DEFAULT = "sofit_gym";
    protected const DB_SECURITY = "sofit_gym_seguridad";

    protected function dbSecurity(string $table): string
    {
        return self::DB_SECURITY . ".{$table}";
    }

    public function __construct(
        protected Database $db = new Database()
    ) {}
}
