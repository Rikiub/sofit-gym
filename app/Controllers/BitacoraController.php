<?php

namespace App\Controllers;

use App\Helpers\Response;
use App\Models\BitacoraModel;

class BitacoraController extends BaseController
{
    public function __construct(
        private Response $response,
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
        return $this->response->json($logs);
    }
}
