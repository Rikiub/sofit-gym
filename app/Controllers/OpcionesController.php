<?php

namespace App\Controllers;

use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Models\OpcionModel;

class OpcionesController extends Controller
{
    public function __construct(
        private $model = new OpcionModel()
    ) {}

    public function index()
    {
        $this->protect("opciones:ver");

        $datos = $this->model->query();
        return $this->render("opciones", ["opciones" => $datos]);
    }

    public function query()
    {
        $this->protect("opciones:ver");
        $datos = $this->model->query();
        return Response::json($datos);
    }

    public function find()
    {
        $this->protect("opciones:ver");

        $clave = Request::query("id");
        $datos = $this->model->find($clave);

        return $datos
            ? Response::json($datos)
            : Response::noContent();
    }

    public function update()
    {
        $this->protect("opciones:editar");
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
