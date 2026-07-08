<?php

namespace App\Controllers;

use App\Controllers\Controller;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Http\Status;
use App\Core\Reportes\ReporteClientes;
use App\Core\Tools;
use App\Models\Clientes\Cliente;
use App\Models\Clientes\ClienteModel;
use App\Services\Logging\BitacoraLogger;

class ClientesController extends Controller
{
    public function __construct(
        private $logger = new BitacoraLogger(),
        private $clienteModelo = new ClienteModel(),
    ) {}

    public function index(): string
    {
        $this->protect("clientes:ver");
        return $this->render('clientes/index');
    }

    public function query(): string
    {
        $this->protect("clientes:ver");

        $search = Request::query("search");
        $filters = Request::query("filters") ?? [];

        $clientes = $this->clienteModelo->query($search, $filters);
        return Response::json($clientes);
    }

    public function summary(): string
    {
        $this->protect("clientes:ver");
        $clientes = $this->clienteModelo->getSummary();
        return Response::json($clientes);
    }

    public function find(): ?string
    {
        $this->protect("clientes:ver");

        $id = $this->getId();
        $cliente = $this->clienteModelo->find($id);

        return $cliente
            ? Response::json($cliente)
            : Response::noContent();
    }

    public function insert(): string
    {
        $this->protect("clientes:crear");

        $cliente = $this->validateBody();
        $id = $cliente->cedula;

        if ($this->clienteModelo->checkDuplicate($id)) {
            return Response::json(
                ['message' => "El cliente {$id} ya existe"],
                Status::CONFLICT
            );
        }

        $newCliente = $this->clienteModelo->insert($cliente);
        $this->logger->info(
            "Cliente '{cedula}' creado",
            [
                "cedula" => $id,
                "datos_nuevos" => $newCliente,
            ],
        );

        return Response::json($newCliente, Status::CREATED);
    }

    public function update(): string
    {
        $this->protect("clientes:editar");

        $cliente = $this->validateBody();
        $id = $this->getId();

        $oldCliente = $this->clienteModelo->find($id);
        if (!$oldCliente) {
            return $this->notFound();
        }

        $newCliente = $this->clienteModelo->update($id, $cliente);
        $this->logger->info(
            "Cliente '{cedula}' actualizado",
            [
                "cedula" => $oldCliente->cedula,
                "datos_previos" => $oldCliente,
                "datos_nuevos" => $newCliente,
            ],
        );

        return Response::json($cliente, Status::CREATED);
    }

    public function delete(): string|null
    {
        $this->protect("clientes:eliminar");
        $id = $this->getId();

        if (!$this->clienteModelo->find($id)) {
            return $this->notFound();
        }

        $this->clienteModelo->delete($id);
        $this->logger->info(
            "Cliente '{cedula}' eliminado",
            ["cedula" => $id]
        );

        return Response::noContent();
    }

    private function notFound(): string
    {
        return Response::json(
            ['message' => "El cliente no existe"],
            Status::NOT_FOUND
        );
    }

    private function getId()
    {
        return Request::query("id") ?? "";
    }

    private function validateBody(): Cliente
    {
        $body = Request::getParsedBody();
        return Tools::map(Cliente::class, $body);
    }

    // REPORTES
    public function reporteVista()
    {
        $this->protect("clientes:ver");
        return $this->render('reportes/clientes');
    }

    /**
     * Generar reporte PDF del listado general de clientes y sus estados de membresía.
     */
    public function reporteGeneral()
    {
        $this->protect("clientes:ver");

        // Opcional: Permitir filtrar desde la URL por estado (ej: ?page=clientes&action=reporte&estado=Activo)
        $estadoFiltro = $_GET['estado'] ?? null;

        // Solicitar al modelo los datos estructurados en array asociativo
        $clientesData = $this->clienteModelo->query(filters: [
            "estado_membresia" => $estadoFiltro
        ]);

        // Instanciar la clase FPDF encargada del reporte de clientes
        $pdf = new ReporteClientes();

        // Establecer los metadatos obligatorios para el documento PDF
        $pdf->SetTitle(utf8_decode('Reporte General de Clientes - SOFIT GYM'));
        $pdf->SetAuthor('Sistema SOFIT GYM');

        // Construir el cuerpo de las páginas pasando la data obtenida del modelo
        $pdf->crearReporte($clientesData);

        // Renderizar y forzar la visualización directa en el navegador de manera limpia
        $pdf->Output('I', 'reporte_general_clientes.pdf');
    }
}
