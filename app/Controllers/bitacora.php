<?php

namespace App\Controllers;

use App\Core\ControllerTools;
use App\Core\Http\Response;
use App\Models\BitacoraModel;

$bitacoraModel = new BitacoraModel();

switch (ControllerTools::action()) {
    case "index":
        ControllerTools::protect("bitacora:ver");
        return ControllerTools::render("bitacora");

    case "query":
        ControllerTools::protect("bitacora:ver");
        $logs = $bitacoraModel->query();
        return Response::json($logs);
}
