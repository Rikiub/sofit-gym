<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Helpers\Reportes\reporteClientes;
use App\Helpers\Response;
use App\Models\Clientes\ClienteDTO;
use App\Models\Clientes\ClientesModel;
use CuyZ\Valinor\Mapper\TreeMapper;
use Exception;

class ClientesController extends BaseController
{
    public function __construct(
        private Response $response,
        private TreeMapper $mapper,
        private ClientesModel $clientesModelo,
    ) {}

    // CLIENTES

    public function index(): string
    {
        $this->protect("clientes:ver");

        $templates = $this->templates->addData(
            ['formMeta' => $this->clientesModelo->queryMembresiaMetadata()]
        );
        return $templates->render('clientes/index');
    }

    public function query(): string
    {
        $this->protect("clientes:ver");

        $search = $_GET["search"] ?? null;
        $filters = $_GET["filters"] ?? [];

        $clientes = $this->clientesModelo->query($search, $filters);
        return $this->response->json($clientes);
    }

    public function find(): ?string
    {
        $this->protect("clientes:ver");

        $cedula = $this->getCedulaParam();
        $cliente = $this->clientesModelo->find($cedula);

        return $cliente
            ? $this->response->json($cliente)
            : $this->response->empty(404);
    }

    public function insert(): string
    {
        $this->protect("clientes:crear");

        $body = $this->response->getParsedBody();
        $cliente = $this->mapper->map(ClienteDTO::class, $body);

        // Verificar que el cliente no exista
        $oldCliente = $this->clientesModelo->find($cliente->cedula);
        if ($oldCliente) {
            return $this->conflict(true, 400);
        }

        $cliente = $this->clientesModelo->insert($cliente);
        $this->logger->info(
            "Cliente {cedula_cliente} creado",
            [
                "cedula_cliente" => $cliente->cedula,
                "datos_previos" => $oldCliente,
                "datos_nuevos" => $cliente,
            ],
        );

        return $this->response->json($cliente, 201);
    }

    public function update(): string
    {
        $this->protect("clientes:editar");

        $body = $this->response->getParsedBody();
        $cliente = $this->mapper->map(ClienteDTO::class, $body);

        $oldCliente = $this->clientesModelo->find($cliente->cedula);
        if (!$oldCliente) {
            return $this->conflict(true, 400);
        }

        $cliente = $this->clientesModelo->update($cliente);
        $this->logger->info(
            "Cliente {cedula_cliente} actualizado",
            [
                "cedula_cliente" => $cliente->cedula,
                "datos_previos" => $oldCliente,
                "datos_nuevos" => $cliente,
            ],
        );

        return $this->response->json($cliente, 201);
    }

    public function delete(): string|null
    {
        $this->protect("clientes:eliminar");
        $cedula = $this->getCedulaParam();

        if (!$this->clientesModelo->find($cedula)) {
            return $this->conflict(false, 404);
        }

        $this->clientesModelo->delete($cedula);
        $this->logger->info(
            "Cliente {cedula_cliente} eliminado",
            ["cedula_cliente" => $cedula]
        );

        return $this->response->empty(204);
    }

    private function conflict(bool $exists, int $id, ?int $code = 400): string
    {
        $message = match ($exists) {
            true => "El cliente {cedula_cliente} ya existe",
            false => "El cliente {cedula_cliente} no existe",
        };

        $this->logger->error($message, ["cedula_cliente" => $id]);
        return $this->response->json(['message' => $message], $code);
    }

    private function getCedulaParam(): string
    {
        $cedula =
            $_GET['cedula']
            ?? $_GET['id']
            ?? throw new Exception("'id' or 'cedula' param is required");
        return $cedula;
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
