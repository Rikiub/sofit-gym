<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Helpers\Response;
use App\Models\Equipos\MantenimientoEquipoDTO;
use App\Models\Equipos\MantenimientoEquipoModel;
use CuyZ\Valinor\Mapper\TreeMapper;
use Exception;

class EquiposMantenimientoController extends BaseController
{
    public function __construct(
        private Response $response,
        private TreeMapper $mapper,
        private MantenimientoEquipoModel $model,
    ) {}

    public function index()
    {
        return $this->templates->render('equipos_mantenimiento');
    }

    private function getIdParam(): int
    {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            throw new Exception("'id' param is required");
        }
        return (int) $id;
    }

    public function query()
    {
        $mantenimientos = $this->model->query();
        return $this->response->json($mantenimientos);
    }

    public function find(): ?string
    {
        $id = $this->getIdParam();
        $mantenimiento = $this->model->find($id);

        if (!$mantenimiento) {
            return $this->response->empty(404);
        }

        return $this->response->json($mantenimiento);
    }

    public function insert(): string
    {
        $body = $this->response->getParsedBody();
        $mantenimiento = $this->mapper->map(MantenimientoEquipoDTO::class, $body);

        $mantenimiento = $this->model->insert($mantenimiento);
        return $this->response->json($mantenimiento, 201);
    }

    public function update(): string
    {
        $body = $this->response->getParsedBody();
        $mantenimiento = $this->mapper->map(MantenimientoEquipoDTO::class, $body);

        if (!$this->model->find($mantenimiento->id_mantenimiento ?? 0)) {
            return $this->response->json(['message' => 'El mantenimiento no existe'], 404);
        }

        $mantenimiento = $this->model->update($mantenimiento);
        return $this->response->json($mantenimiento, 201);
    }

    public function delete(): string|null
    {
        $id = $this->getIdParam();

        if (!$this->model->find($id)) {
            return $this->response->json(['message' => 'El mantenimiento no existe'], 404);
        }

        $this->model->delete($id);
        return $this->response->empty(204);
    }
}
