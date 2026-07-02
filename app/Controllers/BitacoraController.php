<?php

namespace App\Controllers;

use App\Models\BitacoraModel;

class BitacoraController extends Controller
{
    public function __construct(
        private BitacoraModel $bitacoraModel
    ) {}

    public function index()
    {
        $this->protect("bitacora:ver");
        return $this->templates->render("bitacora");
    }

    public function query(): string
    {
        $this->protect("bitacora:ver");
        $logs = $this->bitacoraModel->query();
        return $this->json($logs);
    }
}
