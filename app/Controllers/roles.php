<?php

namespace App\Controllers;

use App\Core\ControllerTools;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Http\Status;
use App\Core\Tools;
use App\Models\Rol;
use App\Models\RolModel;

$rolModel = new RolModel();

function notFound(): string
{
    return Response::json(["message" => "El rol no existe"], Status::NOT_FOUND);
}

function getId(): int
{
    return Request::queryInt("id") ?? 0;
}

function validateBody(): Rol
{
    $body = Request::getParsedBody();
    return Tools::map(Rol::class, $body);
}

switch (ControllerTools::action()) {
    case "index":
        $permisos = $rolModel->queryPermisos();
        return ControllerTools::render('roles', [
            "permisos" => $permisos
        ]);

    case "query":
        ControllerTools::protect("roles:ver");
        $roles = $rolModel->query();
        return Response::json($roles);

    case "find":
        ControllerTools::protect("roles:ver");

        $id = getId();
        $rol = $rolModel->find($id);

        if (!$rol) {
            return notFound();
        }

        return Response::json($rol);

    case "update":
        ControllerTools::protect("roles:editar");

        $id = getId();
        $rol = validateBody();

        if (!$rolModel->find($id)) {
            notFound();
        }

        $rol = $rolModel->update($id, $rol);
        return Response::json($rol, Status::CREATED);
}
