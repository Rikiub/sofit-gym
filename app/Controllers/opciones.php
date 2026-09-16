<?php

namespace App\Controllers;

use App\Core\ControllerTools;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Models\OpcionModel;

$model = new OpcionModel();

switch (ControllerTools::action()) {
    case "index":
        ControllerTools::protect("opciones:ver");

        $datos = $model->query();
        return ControllerTools::render("opciones", ["opciones" => $datos]);

    case "query":
        ControllerTools::protect("opciones:ver");
        $datos = $model->query();
        return Response::json($datos);

    case "find":
        ControllerTools::protect("opciones:ver");

        $clave = Request::query("id");
        $datos = $model->find($clave);

        return $datos
            ? Response::json($datos)
            : Response::noContent();

    case "update":
        ControllerTools::protect("opciones:editar");
        $datos = Request::getParsedBody();

        if (is_array($datos)) {
            foreach ($datos as $item) {
                if (isset($item['clave']) && array_key_exists('valor', $item)) {
                    $model->update($item['clave'], $item['valor']);
                }
            }
        }

        return Response::json([
            "success" => true,
            "message" => "Opciones actualizadas correctamente"
        ]);
}
