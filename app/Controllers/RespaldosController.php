<?php

namespace App\Controllers;

use App\Core\ControllerTools;
use App\Core\Http\Response;
use App\Services\RespaldoService;

class RespaldosController
{
    public function __construct(
        private $respaldo = new RespaldoService()
    ) {}

    public function index()
    {
        ControllerTools::protect("respaldos:ver");
        return ControllerTools::render('respaldos');
    }

    public function query()
    {
        ControllerTools::protect("respaldos:ver");
        $respaldos = $this->respaldo->getAll();
        return Response::json($respaldos);
    }

    public function backup()
    {
        ControllerTools::protect("respaldos:respaldar");
        $this->respaldo->backup();
        return Response::noContent();
    }
}
