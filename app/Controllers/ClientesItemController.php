<?php

namespace App\Controllers;

use App\Controllers\Controller;
use App\Models\Clientes\ClientesModel;
use App\Models\Clientes\SeguimientoFisicoDTO;
use App\Models\Clientes\SeguimientoNutricionalDTO;
use App\Models\Clientes\SegumientoFisicoModel;
use App\Models\Clientes\SegumientoNutricionalModel;
use CuyZ\Valinor\Mapper\TreeMapper;
use Exception;

class ClientesItemController extends Controller
{
    public function __construct(
        private TreeMapper $mapper,
        private ClientesModel $clientesModel,
        private SegumientoFisicoModel $fisicoModel,
        private SegumientoNutricionalModel $nutricionalModel,
    ) {}

    // CLIENTES: Pagina unica

    public function index(): string
    {
        $this->protect("clientes:ver");
        $cedula = $this->getCedulaParam();

        if (!$this->clientesModel->find($cedula)) {
            $this->redirectToError();
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
        $this->protect("clientes:ver");
        $cedula = $this->getCedulaParam();

        if (!$this->clientesModel->find($cedula)) {
            return $this->json(null, 404);
        }

        $registros = $this->fisicoModel->queryByCliente($cedula);
        return $this->json($registros);
    }

    public function insertSegFisico(): string
    {
        $this->protect("clientes:crear");

        $body = $this->getParsedBody();
        $registro = $this->mapper->map(SeguimientoFisicoDTO::class, $body);

        // Verificar que el cliente exista
        if (!$this->clientesModel->find($registro->cedula_cliente)) {
            return $this->json(['message' => 'El cliente no existe'], 404);
        }

        $cliente = $this->fisicoModel->insert($registro);
        return $this->json($cliente, 201);
    }

    public function updateSegFisico(): string
    {
        $this->protect("clientes:editar");
        $cedula = $this->getCedulaParam();

        $body = $this->getParsedBody();
        $body['cedula_cliente'] = $cedula;

        $registro = $this->mapper->map(SeguimientoFisicoDTO::class, $body);

        if (!$this->clientesModel->find($cedula)) {
            return $this->json(['message' => 'El cliente no existe'], 400);
        }

        $registro = $this->fisicoModel->update($registro);
        return $this->json($registro, 201);
    }

    public function deleteSegFisico(): string|null
    {
        $this->protect("clientes:eliminar");
        $idSeguimiento = isset($_GET['id']) ? intval($_GET['id']) : null;

        if (!$this->fisicoModel->find($idSeguimiento)) {
            return $this->json(['message' => 'Seguimiento no existe'], 404);
        }

        $this->fisicoModel->delete($idSeguimiento);
        return $this->json(null, 204);
    }

    // SEGUMIENTO NUTRICIONAL: JSON API

    public function getSegNutricionalByCliente(): ?string
    {
        $this->protect("clientes:ver");
        $cedula = $this->getCedulaParam();

        if (!$this->clientesModel->find($cedula)) {
            return $this->json(null, 404);
        }

        $registros = $this->nutricionalModel->queryByCliente($cedula);
        return $this->json($registros);
    }

    public function insertSegNutricional(): string
    {
        $this->protect("clientes:crear");

        $body = $this->getParsedBody();
        $registro = $this->mapper->map(SeguimientoNutricionalDTO::class, $body);

        // Verificar que el cliente exista
        if (!$this->clientesModel->find($registro->cedula_cliente)) {
            return $this->json(['message' => 'El cliente no existe'], 404);
        }

        $cliente = $this->nutricionalModel->insert($registro);
        return $this->json($cliente, 201);
    }

    public function updateSegNutricional(): string
    {
        $this->protect("clientes:editar");
        $cedula = $this->getCedulaParam();

        $body = $this->getParsedBody();
        $body['cedula_cliente'] = $cedula;

        $registro = $this->mapper->map(SeguimientoNutricionalDTO::class, $body);

        if (!$this->clientesModel->find($cedula)) {
            return $this->json(['message' => 'El cliente no existe'], 400);
        }

        $registro = $this->nutricionalModel->update($registro);
        return $this->json($registro, 201);
    }

    public function deleteSegNutricional(): string|null
    {
        $this->protect("clientes:eliminar");
        $idSeguimiento = isset($_GET['id']) ? intval($_GET['id']) : null;

        if (!$this->nutricionalModel->find($idSeguimiento)) {
            return $this->json(['message' => 'Seguimiento no existe'], 404);
        }

        $this->nutricionalModel->delete($idSeguimiento);
        return $this->json(null, 204);
    }
}
