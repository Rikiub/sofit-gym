<?php

namespace App\Controllers;

use App\Core\ControllerTools;
use App\Core\Http\Response;
use App\Services\RespaldoService;

$respaldo = new RespaldoService();

switch (ControllerTools::action()) {
    case "index":
        ControllerTools::protect("respaldos:ver");
        return ControllerTools::render('respaldos');

    case "query":
        ControllerTools::protect("respaldos:ver");
        $respaldos = $respaldo->getAll();
        return Response::json($respaldos);

    case "backup":
        ControllerTools::protect("respaldos:respaldar");
        $respaldo->backup();
        return Response::noContent();
}
