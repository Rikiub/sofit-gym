<?php

namespace App\Controllers;

use App\Core\Route;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Http\Status;
use App\Core\Tools;
use App\Models\Clientes\Cliente;
use App\Models\Clientes\ClienteModel;
use App\Services\Reportes\ReporteClientes;

$clienteModelo = new ClienteModel();

function notFound(): string
{
    return Response::json(
        ['message' => "El cliente no existe"],
        Status::NOT_FOUND
    );
}

function getId()
{
    return Request::query("id") ?? "";
}

function validateBody(): Cliente
{
    $body = Request::getParsedBody();
    return Tools::map(Cliente::class, $body);
}

switch (Route::action()) {
    case "index":
        Route::protect("clientes:ver");
        return Route::render('clientes/index');

    case "query":
        Route::protect("clientes:ver");

        $search = Request::query("search");
        $filters = Request::query("filters") ?? [];

        $clientes = $clienteModelo->query($search, $filters);
        return Response::json($clientes);

    case "summary":
        Route::protect("clientes:ver");
        $clientes = $clienteModelo->getSummary();
        return Response::json($clientes);

    case "find":
        Route::protect("clientes:ver");

        $id = getId();
        $cliente = $clienteModelo->find($id);

        return $cliente
            ? Response::json($cliente)
            : Response::noContent();

    case "insert":
        Route::protect("clientes:crear");

        $new = validateBody();
        $id = $new->cedula;

        if ($clienteModelo->checkDuplicate($id)) {
            return Response::json(
                ['message' => "El cliente {$id} ya existe"],
                Status::CONFLICT
            );
        }

        $new = $clienteModelo->insert($new);
        return Response::json($new, Status::CREATED);

    case "update":
        Route::protect("clientes:editar");

        $new = validateBody();
        $id = getId();

        $old = $clienteModelo->find($id);
        if (!$old) {
            return notFound();
        }

        $new = $clienteModelo->update($id, $new);
        return Response::json($new, Status::CREATED);

    case "delete":
        Route::protect("clientes:eliminar");
        $id = getId();

        if (!$clienteModelo->find($id)) {
            return notFound();
        }

        $clienteModelo->delete($id);
        return Response::noContent();

        // REPORTES
    case "reporteVista":
        Route::protect("clientes:ver");
        return Route::render('reportes/clientes');

    case "reporteGeneral":
        Route::protect("clientes:ver");

        $estadoFiltro = $_GET['estado'] ?? null;

        $clientesData = $clienteModelo->query(filters: [
            "estado_membresia" => $estadoFiltro
        ]);

        $pdf = new ReporteClientes();

        $pdf->SetTitle(utf8_decode('Reporte General de Clientes - SOFIT GYM'));
        $pdf->SetAuthor('Sistema SOFIT GYM');

        $pdf->crearReporte($clientesData);

        $pdf->Output('I', 'reporte_general_clientes.pdf');
        break;
}
