<?php

namespace App\Controllers;

use App\Core\Route;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Http\Status;
use App\Core\Tools;
use App\Models\BitacoraModel;
use App\Models\Equipos\Equipo;
use App\Models\Equipos\EquipoModel;

$logger = new BitacoraModel();
$equipoModel = new EquipoModel();

function getId(): string
{
    return Request::query("id") ?? "";
}

function validateBody(): Equipo
{
    $body = Request::getParsedBody();
    return Tools::map(Equipo::class, $body);
}

function notFound(): string
{
    return Response::json(['message' => 'El equipo no existe'], Status::NOT_FOUND);
}

switch (Route::action()) {
    case "index":
        return Route::render('equipos');

    case "query":
        Route::protect("equipos:ver");
        $equipos = $equipoModel->query();
        return Response::json($equipos);

    case "find":
        Route::protect("equipos:ver");

        $id = getId();
        $equipo = $equipoModel->find($id);

        return $equipo
            ? Response::json($equipo)
            : Response::noContent();

    case "insert":
        Route::protect("equipos:crear");

        $new = validateBody();
        $id = $equipo->codigo_equipo ?? "";

        if ($equipoModel->find($id)) {
            return Response::json(
                ['message' => 'El equipo ya existe'],
                Status::CONFLICT
            );
        }

        $new = $equipoModel->insert($new);
        $logger->log("Equipo '{codigo_equipo}' creado", [
            "modulo" => "equipos",
            "accion" => "crear",

            "codigo_equipo" => $new->codigo_equipo,
            "datos_nuevos" => $new,
        ]);

        return Response::json($new, Status::CREATED);

    case "update":
        Route::protect("equipos:editar");

        $new = validateBody();
        $id = getId();

        $old = $equipoModel->find($id);
        if (!$old) {
            return notFound();
        }

        $new = $equipoModel->update($id, $new);
        $logger->log("Equipo '{codigo_equipo}' actualizado", [
            "modulo" => "equipos",
            "accion" => "editar",

            "codigo_equipo" => $id,
            "datos_previos" => $old,
            "datos_nuevos" => $new,
        ]);

        return Response::json($new, Status::CREATED);

    case "delete":
        Route::protect("equipos:eliminar");
        $id = getId();

        $old = $equipoModel->find($id);
        if (!$old) {
            return notFound();
        }

        $equipoModel->delete($id);
        $logger->log("Equipo '{codigo_equipo}' eliminado", [
            "modulo" => "equipos",
            "accion" => "eliminar",

            "codigo_equipo" => $id,
            "datos_previos" => $old,
        ]);

        return Response::noContent();
}
