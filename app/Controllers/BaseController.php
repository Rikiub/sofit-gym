<?php

namespace App\Controllers;

use App\Helpers\Logger\BitacoraLogger;
use DI\Attribute\Inject;
use League\Plates\Engine;

abstract class BaseController
{
    #[Inject]
    protected Engine $templates;

    #[Inject]
    protected BitacoraLogger $logger;
}
