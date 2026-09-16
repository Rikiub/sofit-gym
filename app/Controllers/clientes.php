<?php

namespace App\Controllers;

use App\Core\ControllerTools;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Http\Status;
use App\Core\Tools;
use App\Models\BitacoraModel;
use App\Models\Clientes\Cliente;
use App\Models\Clientes\ClienteModel;
use App\Services\Reportes\ReporteClientes;

$logger = new BitacoraModel();
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

switch (ControllerTools::action()) {
    case "index":
        ControllerTools::protect("clientes:ver");
        return ControllerTools::render('clientes/index');

    case "query":
        ControllerTools::protect("clientes:ver");

        $search = Request::query("search");
        $filters = Request::query("filters") ?? [];

        $clientes = $clienteModelo->query($search, $filters);
        return Response::json($clientes);

    case "summary":
        ControllerTools::protect("clientes:ver");
        $clientes = $clienteModelo->getSummary();
        return Response::json($clientes);

    case "find":
        ControllerTools::protect("clientes:ver");

        $id = getId();
        $cliente = $clienteModelo->find($id);

        return $cliente
            ? Response::json($cliente)
            : Response::noContent();

    case "insert":
        ControllerTools::protect("clientes:crear");

        $new = validateBody();
        $id = $new->cedula;

        if ($clienteModelo->checkDuplicate($id)) {
            return Response::json(
                ['message' => "El cliente {$id} ya existe"],
                Status::CONFLICT
            );
        }

        $new = $clienteModelo->insert($new);
        $logger->log(
            "Cliente '{cedula}' creado",
            [
                "modulo" => "clientes",
                "accion" => "crear",

                "cedula" => $id,
                "datos_nuevos" => $new,
            ],
        );

        return Response::json($new, Status::CREATED);

    case "update":
        ControllerTools::protect("clientes:editar");

        $new = validateBody();
        $id = getId();

        $old = $clienteModelo->find($id);
        if (!$old) {
            return notFound();
        }

        $new = $clienteModelo->update($id, $new);
        $logger->log(
            "Cliente '{cedula}' actualizado",
            [
                "modulo" => "clientes",
                "accion" => "editar",

                "cedula" => $old->cedula,
                "datos_previos" => $old,
                "datos_nuevos" => $new,
            ],
        );

        return Response::json($new, Status::CREATED);

    case "delete":
        ControllerTools::protect("clientes:eliminar");
        $id = getId();

        if (!$clienteModelo->find($id)) {
            return notFound();
        }

        $clienteModelo->delete($id);
        $logger->log(
            "Cliente '{cedula}' eliminado",
            [
                "modulo" => "clientes",
                "accion" => "eliminar",
                "cedula" => $id
            ]
        );

        return Response::noContent();

        // REPORTES
    case "reporteVista":
        ControllerTools::protect("clientes:ver");
        return ControllerTools::render('reportes/clientes');

    case "reporteGeneral":
        ControllerTools::protect("clientes:ver");

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
