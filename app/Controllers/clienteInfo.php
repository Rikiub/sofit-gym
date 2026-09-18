<?php

namespace App\Controllers;

use App\Core\Route;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Http\Status;
use App\Core\Tools;
use App\Models\Clientes\ClienteModel;
use App\Models\Clientes\SeguimientoFisico;
use App\Models\Clientes\SeguimientoNutricional;
use App\Models\Clientes\SegumientoFisicoModel;
use App\Models\Clientes\SegumientoNutricionalModel;

$clienteModel = new ClienteModel();
$fisicoModel = new SegumientoFisicoModel();
$nutricionalModel = new SegumientoNutricionalModel();

function getCedula(): string
{
    return Request::query("cedula") ?? "";
}

function getIdSeguimiento(): int
{
    return Request::queryInt("id_seguimiento") ?? 0;
}

function notFoundCliente(): string
{
    return Response::json(["message" => "Cliente no encontrado"], Status::NOT_FOUND);
}

function notFoundSeg(): string
{
    return Response::json(['message' => "El seguimiento no existe"], Status::NOT_FOUND);
}

function validateBodyFisico(): SeguimientoFisico
{
    $body = Request::getParsedBody();
    return Tools::map(SeguimientoFisico::class, $body);
}

function validateBodyNutricion(): SeguimientoNutricional
{
    $body = Request::getParsedBody();
    return Tools::map(SeguimientoNutricional::class, $body);
}

switch (Route::action()) {
    // INFORMACIÓN DE CLIENTE
    case "index":
        Route::protect("clientes:ver");
        $cedula = getCedula();

        if (!$clienteModel->find($cedula)) {
            Response::redirect([
                "page" => "error",
                "status" => Status::NOT_FOUND,
            ]);
        }

        return Route::render('clientes/info', [
            "cedula" => $cedula,
        ]);

        // SEGUIMIENTO FISICO
    case "queryFisico":
        Route::protect("clientes:ver");
        $cedula = getCedula();

        if (!$clienteModel->find($cedula)) {
            return notFoundCliente();
        }

        $seguimiento = $fisicoModel->queryByCliente($cedula);
        return Response::json($seguimiento);

    case "insertFisico":
        Route::protect("clientes:crear");

        $seguimiento = validateBodyFisico();
        $cedula = getCedula();

        if (!$clienteModel->find($cedula)) {
            return notFoundCliente();
        }

        $new = $fisicoModel->insert($cedula, $seguimiento);
        return Response::json($new, Status::CREATED);

    case "deleteFisico":
        Route::protect("clientes:eliminar");
        $id = getIdSeguimiento();

        $old = $fisicoModel->find($id);
        if (!$old) {
            return notFoundSeg();
        }

        $fisicoModel->delete($id);
        return Response::noContent();

        // SEGUMIENTO NUTRICIONAL
    case "queryNutricion":
        Route::protect("clientes:ver");
        $cedula = getCedula();

        if (!$clienteModel->find($cedula)) {
            return notFoundCliente();
        }

        $seguimientos = $nutricionalModel->queryByCliente($cedula);
        return Response::json($seguimientos);

    case "insertNutricion":
        Route::protect("clientes:crear");

        $seguimiento = validateBodyNutricion();
        $cedula = getCedula();

        if (!$clienteModel->find($cedula)) {
            return notFoundCliente();
        }

        $new = $nutricionalModel->insert($cedula, $seguimiento);
        return Response::json($new, Status::CREATED);

    case "deleteNutricion":
        Route::protect("clientes:eliminar");
        $id = getIdSeguimiento();

        $old = $nutricionalModel->find($id);
        if (!$old) {
            return notFoundSeg();
        }

        $nutricionalModel->delete($id);
        return Response::noContent();
}
