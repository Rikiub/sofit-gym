<?php

namespace App\Controllers;

use App\Controllers\Controller;
use App\Core\Http\Request;
use App\Core\Http\StatusCode;
use App\Core\Reportes\ReporteClientes;
use App\Models\Clientes\Cliente;
use App\Models\Clientes\ClienteModel;
use CuyZ\Valinor\Mapper\TreeMapper;

class ClientesController extends Controller
{
    public function __construct(
        private TreeMapper $mapper,
        private ClienteModel $clienteModelo,
    ) {}

    public function index(): string
    {
        $this->protect("clientes:ver");
        return $this->templates->render('clientes/index');
    }

    public function query(): string
    {
        $this->protect("clientes:ver");

        $search = Request::query("search");
        $filters = Request::query("filters") ?? [];

        $clientes = $this->clienteModelo->query($search, $filters);
        return $this->json($clientes);
    }

    public function summary(): string
    {
        $this->protect("clientes:ver");
        $clientes = $this->clienteModelo->getSummary();
        return $this->json($clientes);
    }

    public function find(): ?string
    {
        $this->protect("clientes:ver");

        $id = $this->getId();
        $cliente = $this->clienteModelo->find($id);

        return $cliente
            ? $this->json($cliente)
            : $this->json(null, StatusCode::NOT_FOUND);
    }

    public function insert(): string
    {
        $this->protect("clientes:crear");

        $cliente = $this->validateBody();
        $id = $cliente->cedula;

        if ($this->clienteModelo->checkDuplicate($id)) {
            return $this->json(
                ['message' => "El cliente {$id} ya existe"],
                StatusCode::CONFLICT
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

        return $this->json($newCliente, StatusCode::CREATED);
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

        return $this->json($cliente, StatusCode::CREATED);
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

        return $this->json(null, StatusCode::NO_CONTENT);
    }

    private function notFound(): string
    {
        return $this->json(
            ['message' => "El cliente no existe"],
            StatusCode::NOT_FOUND
        );
    }

    private function getId()
    {
        return Request::query("id") ?? "";
    }

    private function validateBody(): Cliente
    {
        $body = Request::getParsedBody();
        return $this->mapper->map(Cliente::class, $body);
    }

    // REPORTES
    public function reporteVista()
    {
        $this->protect("clientes:ver");
        return $this->templates->render('reportes/clientes');
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
