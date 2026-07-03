<?php

namespace App\Controllers;

use App\Controllers\Controller;
use App\Core\Http\Request;
use App\Core\Reportes\reporteClientes;
use App\Models\Clientes\ClienteDTO;
use App\Models\Clientes\ClientesModel;
use CuyZ\Valinor\Mapper\TreeMapper;

class ClientesController extends Controller
{
    public function __construct(
        private TreeMapper $mapper,
        private ClientesModel $clientesModelo,
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

        $clientes = $this->clientesModelo->query($search, $filters);
        return $this->json($clientes);
    }

    public function summary(): string
    {
        $this->protect("clientes:ver");
        $clientes = $this->clientesModelo->getSummary();
        return $this->json($clientes);
    }

    public function find(): ?string
    {
        $this->protect("clientes:ver");

        $id = $this->getId();
        $cliente = $this->clientesModelo->find($id);

        return $cliente
            ? $this->json($cliente)
            : $this->json(null, 404);
    }

    public function insert(): string
    {
        $this->protect("clientes:crear");

        $cliente = $this->validateBody();
        $id = $cliente->cedula;

        if ($this->clientesModelo->checkDuplicate($id)) {
            return $this->conflict(true, $id);
        }

        $newCliente = $this->clientesModelo->insert($cliente);
        $this->logger->info(
            "Cliente '{cedula}' creado",
            [
                "cedula" => $id,
                "datos_nuevos" => $newCliente,
            ],
        );

        return $this->json($newCliente, 201);
    }

    public function update(): string
    {
        $this->protect("clientes:editar");

        $cliente = $this->validateBody();
        $id = $this->getId();

        $oldCliente = $this->clientesModelo->find($id);
        if (!$oldCliente) {
            return $this->conflict(false, $id);
        }

        $newCliente = $this->clientesModelo->update($id, $cliente);
        $this->logger->info(
            "Cliente '{cedula}' actualizado",
            [
                "cedula" => $oldCliente->cedula,
                "datos_previos" => $oldCliente,
                "datos_nuevos" => $newCliente,
            ],
        );

        return $this->json($cliente, 201);
    }

    public function delete(): string|null
    {
        $this->protect("clientes:eliminar");
        $id = $this->getId();

        if (!$this->clientesModelo->find($id)) {
            return $this->conflict(false, $id);
        }

        $this->clientesModelo->delete($id);
        $this->logger->info(
            "Cliente '{cedula}' eliminado",
            ["cedula" => $id]
        );

        return $this->json(null, 204);
    }

    private function conflict(bool $exists, string $id): string
    {
        if ($exists) {
            $message = "El cliente {$id} ya existe";
            $code = 400;
        } else {
            $message = "El cliente {$id} no existe";
            $code = 404;
        }

        return $this->json(['message' => $message], $code);
    }

    private function getId()
    {
        return Request::query("id") ?? "";
    }

    private function validateBody(): ClienteDTO
    {
        $body = Request::getParsedBody();
        return $this->mapper->map(ClienteDTO::class, $body);
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
        $clientesData = $this->clientesModelo->obtenerClientesParaReporte($estadoFiltro);

        // Instanciar la clase FPDF encargada del reporte de clientes
        $pdf = new reporteClientes();

        // Establecer los metadatos obligatorios para el documento PDF
        $pdf->SetTitle(utf8_decode('Reporte General de Clientes - SOFIT GYM'));
        $pdf->SetAuthor('Sistema SOFIT GYM');

        // Construir el cuerpo de las páginas pasando la data obtenida del modelo
        $pdf->crearReporte($clientesData);

        // Renderizar y forzar la visualización directa en el navegador de manera limpia
        $pdf->Output('I', 'reporte_general_clientes.pdf');
    }
}
