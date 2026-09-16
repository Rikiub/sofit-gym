<?php

namespace App\Controllers;

use App\Core\Route;
use App\Core\Http\Response;
use App\Services\RespaldoService;

$respaldo = new RespaldoService();

switch (Route::action()) {
    case "index":
        Route::protect("respaldos:ver");
        return Route::render('respaldos');

    case "query":
        Route::protect("respaldos:ver");
        $respaldos = $respaldo->getAll();
        return Response::json($respaldos);

    case "backup":
        Route::protect("respaldos:respaldar");
        $respaldo->backup();
        return Response::noContent();
}
