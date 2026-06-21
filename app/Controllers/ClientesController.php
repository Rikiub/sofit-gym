<?php

namespace App\Controllers;

use App\Controllers\BaseController;
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
        // Cargar vista app/views/clientes/index.php
        $templates = $this->templates->addData(
            ['formMeta' => $this->clientesModelo->queryMembresiaMetadata()]
        );
        return $templates->render('clientes/index');
    }

    private function getCedulaParam(): string
    {
        $cedula = $_GET['cedula'] ?? $_GET['id'] ?? '';
        if (!$cedula) {
            throw new Exception("'id' or 'cedula' param is required");
        }
        return $cedula;
    }

    public function query(): string
    {
        $search = $_GET["search"] ?? null;
        $clientes = $this->clientesModelo->query($search);
        return $this->response->json($clientes);
    }

    public function find(): ?string
    {
        $cedula = $this->getCedulaParam();
        $cliente = $this->clientesModelo->find($cedula);

        if (!$cliente) {
            return $this->response->empty(404);
        }

        return $this->response->json($cliente);
    }

    public function insert(): string
    {
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
        $cedula = $this->getCedulaParam();

        if (!$this->clientesModelo->find($cedula)) {
            $this->conflict(false, 404);
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
        if ($exists) {
            $message = "El cliente {cedula_cliente} ya existe";
        } else {
            $message = "El cliente {cedula_cliente} no existe";
        }

        $this->logger->error($message, ["cedula_cliente" => $id]);
        return $this->response->json(['message' => $message], $code);
    }
}
