<?php

namespace App\Controllers;

use App\Core\ControllerTools;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Http\Status;
use App\Core\Tools;
use App\Models\BitacoraModel;
use App\Models\Trabajador;
use App\Models\TrabajadorModel;

$logger = new BitacoraModel();
$trabajadorModel = new TrabajadorModel();

function notFound(): string
{
    return Response::json(
        ["message" => "Trabajador no encontrado"],
        Status::NOT_FOUND
    );
}

function getId(): string
{
    return Request::query("id") ?? "";
}

function validateBody(): Trabajador
{
    $body = Request::getParsedBody();
    return Tools::map(Trabajador::class, $body);
}

switch (ControllerTools::action()) {
    case "index":
        ControllerTools::protect("trabajadores:ver");
        return ControllerTools::render('trabajadores');

    case "query":
        ControllerTools::protect("trabajadores:ver");

        $search = Request::query("search") ?? null;
        $id_rol = Request::queryInt("id_rol") ?? 0;

        $trabajadores = $trabajadorModel->query($search, $id_rol);
        return Response::json($trabajadores);

    case "summary":
        ControllerTools::protect("trabajadores:ver");
        $summary = $trabajadorModel->getSummary();
        return Response::json($summary);

    case "find":
        ControllerTools::protect("trabajadores:ver");

        $id = getId();
        $trabajador = $trabajadorModel->find($id);

        return $trabajador
            ? Response::json($trabajador)
            : Response::noContent();

    case "insert":
        ControllerTools::protect("trabajadores:crear");

        $new = validateBody();
        $id = $new->cedula;

        if ($trabajadorModel->checkDuplicate($id)) {
            return Response::json(
                ['message' => 'El trabajador ya existe'],
                Status::CONFLICT
            );
        }

        $new = $trabajadorModel->insert($new);
        $logger->log("Trabajador '{cedula}' creado", [
            "modulo" => "trabajadores",
            "accion" => "crear",

            'cedula'        => $new->cedula,
            'datos_nuevos'  => $new,
        ]);

        return Response::json($new, Status::CREATED);

    case "update":
        ControllerTools::protect("trabajadores:editar");

        $id = getId();
        $new = validateBody();

        $old = $trabajadorModel->find($id);
        if (!$old) {
            return notFound();
        }

        $new = $trabajadorModel->update($id, $new);
        $logger->log("Trabajador '{cedula}' actualizado", [
            "modulo" => "trabajadores",
            "accion" => "editar",

            'cedula'        => $old->cedula,
            'datos_previos' => $old,
            'datos_nuevos'  => $new,
        ]);

        return Response::json($new, Status::CREATED);

    case "delete":
        ControllerTools::protect("trabajadores:eliminar");
        $id = getId();

        $old = $trabajadorModel->find($id);
        if (!$old) {
            return notFound();
        }

        $trabajadorModel->delete($id);
        $logger->log("Trabajador '{cedula}' eliminado", [
            "modulo" => "trabajadores",
            "accion" => "eliminar",

            'cedula'        => $old->cedula,
            'datos_previos' => $old,
        ]);

        return Response::noContent();
}
