<?php

namespace App\Controllers;

use App\Core\ControllerTools;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Models\OpcionModel;

class OpcionesController
{
    public function __construct(
        private $model = new OpcionModel()
    ) {}

    public function index()
    {
        ControllerTools::protect("opciones:ver");

        $datos = $this->model->query();
        return ControllerTools::render("opciones", ["opciones" => $datos]);
    }

    public function query()
    {
        ControllerTools::protect("opciones:ver");
        $datos = $this->model->query();
        return Response::json($datos);
    }

    public function find()
    {
        ControllerTools::protect("opciones:ver");

        $clave = Request::query("id");
        $datos = $this->model->find($clave);

        return $datos
            ? Response::json($datos)
            : Response::noContent();
    }

    public function update()
    {
        ControllerTools::protect("opciones:editar");
        $datos = Request::getParsedBody();

        if (is_array($datos)) {
            foreach ($datos as $item) {
                if (isset($item['clave']) && array_key_exists('valor', $item)) {
                    $this->model->update($item['clave'], $item['valor']);
                }
            }
        }

        return Response::json([
            "success" => true,
            "message" => "Opciones actualizadas correctamente"
        ]);
    }
}
