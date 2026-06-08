<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Helpers\Response;
use App\Models\Clientes\ClientesModel;
use App\Models\Clientes\SeguimientoFisicoDTO;
use App\Models\Clientes\SeguimientoNutricionalDTO;
use App\Models\Clientes\SegumientoFisicoModel;
use App\Models\Clientes\SegumientoNutricionalModel;
use CuyZ\Valinor\Mapper\TreeMapper;
use Exception;

class ClientesItemController extends BaseController
{
    public function __construct(
        private Response $response,
        private TreeMapper $mapper,
        private ClientesModel $clientesModel,
        private SegumientoFisicoModel $fisicoModel,
        private SegumientoNutricionalModel $nutricionalModel,
    ) {}

    // CLIENTES: Pagina unica

    public function index(): string
    {
        $cedula = $this->getCedulaParam();

        if (!$this->clientesModel->find($cedula)) {
            Response::redirectToError();
        }

        $templates = $this->templates->addData(
            ['formMeta' => $this->clientesModel->queryMembresiaMetadata()]
        );
        return $templates->render('clientes/item', [
            "cedula" => $cedula,
        ]);
    }

    private function getCedulaParam(): string
    {
        $cedula = $_GET['cedula_cliente'] ?? $_GET['cedula'] ?? $_GET['id'] ?? null;
        if (!$cedula) {
            throw new Exception("'id' or 'cedula' param is required");
        }
        return $cedula;
    }

    // SEGUIMIENTO FISICO: JSON API

    public function getSegFisicoByCliente(): ?string
    {
        $cedula = $this->getCedulaParam();

        if (!$this->clientesModel->find($cedula)) {
            return $this->response->empty(404);
        }

        $registros = $this->fisicoModel->queryByCliente($cedula);
        return $this->response->json($registros);
    }

    public function insertSegFisico(): string
    {
        $body = $this->response->getParsedBody();

        // Valida el POST
        $registro = $this->mapper->map(SeguimientoFisicoDTO::class, $body);

        // Verificar que el cliente exista
        if (!$this->clientesModel->find($registro->cedula_cliente)) {
            return $this->response->json(['message' => 'El cliente no existe'], 404);
        }

        // Crea el cliente
        $cliente = $this->fisicoModel->insert($registro);

        // Enviar JSON
        return $this->response->json($cliente, 201);
    }

    public function updateSegFisico(): string
    {
        $cedula = $this->getCedulaParam();

        $body = $this->response->getParsedBody();
        $body['cedula_cliente'] = $cedula;

        $registro = $this->mapper->map(SeguimientoFisicoDTO::class, $body);

        if (!$this->clientesModel->find($cedula)) {
            return $this->response->json(['message' => 'El cliente no existe'], 400);
        }

        $registro = $this->fisicoModel->update($registro);
        return $this->response->json($registro, 201);
    }

    public function deleteSegFisico(): string|null
    {
        $idSeguimiento = isset($_GET['id']) ? intval($_GET['id']) : null;

        if (!$this->fisicoModel->find($idSeguimiento)) {
            return $this->response->json(['message' => 'Seguimiento no existe'], 404);
        }

        $this->fisicoModel->delete($idSeguimiento);
        return $this->response->empty(204);
    }

    // SEGUMIENTO NUTRICIONAL: JSON API

    public function getSegNutricionalByCliente(): ?string
    {
        $cedula = $this->getCedulaParam();

        if (!$this->clientesModel->find($cedula)) {
            return $this->response->empty(404);
        }

        $registros = $this->nutricionalModel->queryByCliente($cedula);
        return $this->response->json($registros);
    }

    public function insertSegNutricional(): string
    {
        $body = $this->response->getParsedBody();

        // Valida el POST
        $registro = $this->mapper->map(SeguimientoNutricionalDTO::class, $body);

        // Verificar que el cliente exista
        if (!$this->clientesModel->find($registro->cedula_cliente)) {
            return $this->response->json(['message' => 'El cliente no existe'], 404);
        }

        // Crea el cliente
        $cliente = $this->nutricionalModel->insert($registro);

        // Enviar JSON
        return $this->response->json($cliente, 201);
    }

    public function updateSegNutricional(): string
    {
        $cedula = $this->getCedulaParam();

        $body = $this->response->getParsedBody();
        $body['cedula_cliente'] = $cedula;

        $registro = $this->mapper->map(SeguimientoNutricionalDTO::class, $body);

        if (!$this->clientesModel->find($cedula)) {
            return $this->response->json(['message' => 'El cliente no existe'], 400);
        }

        $registro = $this->nutricionalModel->update($registro);
        return $this->response->json($registro, 201);
    }

    public function deleteSegNutricional(): string|null
    {
        $idSeguimiento = isset($_GET['id']) ? intval($_GET['id']) : null;

        if (!$this->nutricionalModel->find($idSeguimiento)) {
            return $this->response->json(['message' => 'Seguimiento no existe'], 404);
        }

        $this->nutricionalModel->delete($idSeguimiento);
        return $this->response->empty(204);
    }
}
