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
        return $this->json($clientes);
    }

    public function find(): ?string
    {
        $this->protect("clientes:ver");

        $id = Request::query("id") ?? "";
        $cliente = $this->clientesModelo->find($id);

        return $cliente
            ? $this->json($cliente)
            : $this->json(null, 404);
    }

    public function insert(): string
    {
        $this->protect("clientes:crear");

        $body = $this->getParsedBody();
        $cliente = $this->mapper->map(ClienteDTO::class, $body);

        // Verificar que el cliente no exista
        if ($this->clientesModelo->checkDuplicate($cliente->cedula)) {
            return $this->conflict(true, $cliente->cedula);
        }

        $cliente = $this->clientesModelo->insert($cliente);
        $this->logger->info(
            "Cliente {cedula_cliente} creado",
            [
                "cedula_cliente" => $cliente->cedula,
                "datos_nuevos" => $cliente,
            ],
        );

        return $this->json($cliente, 201);
    }

    public function update(): string
    {
        $this->protect("clientes:editar");

        $body = $this->getParsedBody();
        $cliente = $this->mapper->map(ClienteDTO::class, $body);

        $oldCliente = $this->clientesModelo->find($cliente->cedula);
        if (!$oldCliente) {
            return $this->conflict(true, $cliente->cedula);
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

        return $this->json($cliente, 201);
    }

    public function delete(): string|null
    {
        $this->protect("clientes:eliminar");
        $id = Request::query("id") ?? "";

        if (!$this->clientesModelo->find($id)) {
            return $this->conflict(false, $id);
        }

        $this->clientesModelo->delete($id);
        $this->logger->info(
            "Cliente {cedula_cliente} eliminado",
            ["cedula_cliente" => $id]
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

        $this->logger->error($message, ["cedula_cliente" => $id]);
        return $this->json(['message' => $message], $code);
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
