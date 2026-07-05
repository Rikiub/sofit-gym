<?php

namespace App\Controllers;

use App\Core\Http\StatusCode;
use App\Services\RespaldoService;

class RespaldosController extends Controller
{
    public function __construct(private RespaldoService $respaldo) {}

    public function index()
    {
        $this->protect("respaldos:ver");
        return $this->templates->render('respaldos');
    }

    public function query()
    {
        $this->protect("respaldos:ver");
        $respaldos = $this->respaldo->getAll();
        return $this->json($respaldos);
    }

    public function backup()
    {
        $this->protect("respaldos:respaldar");
        $this->respaldo->backup();
        return $this->json(null, StatusCode::NO_CONTENT);
    }
}
