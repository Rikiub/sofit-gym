<?php

namespace App\Controllers;

use App\Controllers\Controller;
use App\Core\Http\StatusCode;
use App\Services\RespaldoService;

class RespaldosController extends Controller
{
    public function __construct(private RespaldoService $respaldo) {}

    public function index()
    {
        return $this->templates->render('respaldos');
    }

    public function backup()
    {
        $this->respaldo->backup();
        return $this->json(null, StatusCode::NO_CONTENT);
    }
}
