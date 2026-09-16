<?php

namespace App\Controllers;

use App\Core\Route;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Http\Status;
use App\Core\Tools;
use App\Models\Equipos\MantenimientoEquipo;
use App\Models\Equipos\MantenimientoEquipoModel;
use App\Models\BitacoraModel;

$logger = new BitacoraModel();
$model = new MantenimientoEquipoModel();

function notFound(): string
{
    return Response::json(['message' => 'El mantenimiento no existe'], Status::NOT_FOUND);
}

function getId(): int
{
    return Request::queryInt("id") ?? 0;
}

function validateBody(): MantenimientoEquipo
{
    $body = Request::getParsedBody();
    return Tools::map(MantenimientoEquipo::class, $body);
}

switch (Route::action()) {
    case "index":
        Route::protect("equipos:ver");
        return Route::render('equipos_mantenimiento');

    case "query":
        Route::protect("equipos:ver");
        $data = $model->query();
        return Response::json($data);

    case "find":
        Route::protect("equipos:ver");

        $id = getId();
        $data = $model->find($id);

        return $data
            ? Response::json($data)
            : Response::noContent();

    case "insert":
        Route::protect("equipos:crear");

        $new = validateBody();
        $new = $model->insert($new);

        $logger->log("Mantenimiento de equipo '{id_mantenimiento}' registrado", [
            "modulo" => "equipos",
            "accion" => "crear",

            'id_mantenimiento' => $new->id_mantenimiento,
            'codigo_equipo'    => $new->codigo_equipo,
            'datos_nuevos' => $new,
        ]);

        return Response::json($new, Status::CREATED);

    case "update":
        Route::protect("equipos:editar");

        $id = getId();
        $old = $model->find($id);

        if (!$old) {
            return notFound();
        }

        $new = validateBody();
        $new = $model->update($id, $new);

        $logger->log("Mantenimiento de equipo '{id_mantenimiento}' actualizado", [
            "modulo" => "equipos",
            "accion" => "editar",

            'id_mantenimiento' => $id,
            'codigo_equipo'    => $old->codigo_equipo,
            'datos_previos'    => $old,
            'datos_nuevos'     => $new,
        ]);

        return Response::json($new, Status::CREATED);

    case "delete":
        Route::protect("equipos:eliminar");
        $id = getId();

        $old = $model->find($id);
        if (!$old) {
            return notFound();
        }

        $model->delete($id);
        $logger->log("Mantenimiento de equipo '{id_mantenimiento}' eliminado", [
            "modulo" => "equipos",
            "accion" => "eliminar",

            'id_mantenimiento' => $id,
            'codigo_equipo'    => $old->codigo_equipo,
            'datos_previos' => $old,
        ]);

        return Response::noContent();
}
