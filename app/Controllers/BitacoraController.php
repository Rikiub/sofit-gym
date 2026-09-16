<?php

namespace App\Controllers;

use App\Core\ControllerTools;
use App\Core\Http\Response;
use App\Models\BitacoraModel;

class BitacoraController
{
    public function __construct(
        private $bitacoraModel = new BitacoraModel()
    ) {}

    public function index()
    {
        ControllerTools::protect("bitacora:ver");
        return ControllerTools::render("bitacora");
    }

    public function query(): string
    {
        ControllerTools::protect("bitacora:ver");
        $logs = $this->bitacoraModel->query();
        return Response::json($logs);
    }
}
