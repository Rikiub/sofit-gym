<?php

namespace App\Controllers;

use App\Core\Route;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Models\OpcionModel;

$model = new OpcionModel();

switch (Route::action()) {
    case "index":
        Route::protect("opciones:ver");

        $datos = $model->query();
        return Route::render("opciones", ["opciones" => $datos]);

    case "query":
        Route::protect("opciones:ver");
        $datos = $model->query();
        return Response::json($datos);

    case "find":
        Route::protect("opciones:ver");

        $clave = Request::query("id");
        $datos = $model->find($clave);

        return $datos
            ? Response::json($datos)
            : Response::noContent();

    case "update":
        Route::protect("opciones:editar");
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
