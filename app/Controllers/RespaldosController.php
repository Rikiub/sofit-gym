<?php

namespace App\Controllers;

use App\Core\Http\Response;
use App\Services\RespaldoService;

class RespaldosController extends Controller
{
    public function __construct(
        private $respaldo = new RespaldoService()
    ) {}

    public function index()
    {
        $this->protect("respaldos:ver");
        return $this->render('respaldos');
    }

    public function query()
    {
        $this->protect("respaldos:ver");
        $respaldos = $this->respaldo->getAll();
        return Response::json($respaldos);
    }

    public function backup()
    {
        $this->protect("respaldos:respaldar");
        $this->respaldo->backup();
        return Response::noContent();
    }
}
