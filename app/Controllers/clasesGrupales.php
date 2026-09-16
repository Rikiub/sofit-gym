<?php

namespace App\Controllers;

use App\Core\Route;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Http\Status;
use App\Core\Tools;
use App\Models\BitacoraModel;
use App\Models\ClaseGrupal;
use App\Models\ClaseGrupalModel;

$logger = new BitacoraModel();
$claseModel = new ClaseGrupalModel();

function getId(): int
{
    return Request::queryInt("id") ?? 0;
}

function notFound(): string
{
    return Response::json(["message" => "Clase no encontrada"], Status::NOT_FOUND);
}

function validateBody(): ClaseGrupal
{
    $body = Request::getParsedBody();
    return Tools::map(ClaseGrupal::class, $body);
}

switch (Route::action()) {
    case "index":
        Route::protect("clases:ver");
        return Route::render('clases');

    case "query":
        Route::protect("clases:ver");
        $clases = $claseModel->query();
        return Response::json($clases);

    case "find":
        Route::protect("clases:ver");

        $id = getId();
        $clase = $claseModel->find($id);

        return $clase
            ? Response::json($clase)
            : Response::noContent();

    case "insert":
        Route::protect("clases:crear");

        $new = validateBody();
        $new = $claseModel->insert($new);

        $logger->log("Clase grupal '{nombre}' creada", [
            "modulo" => "clases_grupales",
            "accion" => "crear",

            'nombre' => $new->nombre,
            'id_clase'      => $new->id_clase,
            'datos_nuevos'  => $new,
        ]);

        return Response::json($new, Status::CREATED);

    case "update":
        Route::protect("clases:editar");

        $id = getId();
        $new = validateBody();

        $old = $claseModel->find($id);
        if (!$old) {
            return notFound();
        }

        $new = $claseModel->update($id, $new);
        $logger->log("Clase grupal '{nombre}' actualizada", [
            "modulo" => "clases_grupales",
            "accion" => "editar",

            'nombre' => $old->nombre,
            'id_clase' => $id,
            'datos_previos' => $old,
            'datos_nuevos' => $new,
        ]);

        return Response::json($new, Status::CREATED);

    case "delete":
        Route::protect("clases:eliminar");
        $id = getId();

        $old = $claseModel->find($id);
        if (!$old) {
            return notFound();
        }

        $claseModel->delete($id);
        $logger->log("Clase grupal '{nombre}' eliminada", [
            "modulo" => "clases_grupales",
            "accion" => "eliminar",

            'nombre' => $old->nombre,
            'id_clase'      => $id,
            'datos_previos' => $old,
        ]);

        return Response::noContent();
}
