<?php

namespace App\Controllers;

use App\Core\Route;
use App\Core\Http\Response;
use App\Models\BitacoraModel;

$bitacoraModel = new BitacoraModel();

switch (Route::action()) {
    case "index":
        Route::protect("bitacora:ver");
        return Route::render("bitacora");

    case "query":
        Route::protect("bitacora:ver");
        $logs = $bitacoraModel->query();
        return Response::json($logs);
}
