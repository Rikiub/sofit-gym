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
        $this->protect("equipos:ver");
        return $this->templates->render('equipos_mantenimiento');
    }

    public function query()
    {
        $this->protect("equipos:ver");
        $data = $this->model->query();
        return $this->response->json($data);
    }

    public function find(): ?string
    {
        $this->protect("equipos:ver");

        $id = $this->getParamId();
        $data = $this->model->find($id);

        return $data
            ? $this->response->json($data)
            : $this->response->empty(404);
    }

    public function insert(): string
    {
        $this->protect("equipos:crear");

        $body = $this->response->getParsedBody();
        $data = $this->mapper->map(MantenimientoEquipoDTO::class, $body);

        $data = $this->model->insert($data);
        return $this->response->json($data, 201);
    }

    public function update(): string
    {
        $this->protect("equipos:editar");

        $body = $this->response->getParsedBody();
        $data = $this->mapper->map(MantenimientoEquipoDTO::class, $body);

        if (!$this->model->find($data->id_mantenimiento)) {
            return $this->response->json(['message' => 'El mantenimiento no existe'], 404);
        }

        $data = $this->model->update($data);
        return $this->response->json($data, 201);
    }

    public function delete(): string|null
    {
        $this->protect("equipos:eliminar");
        $id = $this->getParamId();

        if (!$this->model->find($id)) {
            return $this->response->json(['message' => 'El mantenimiento no existe'], 404);
        }

        $this->model->delete($id);
        return $this->response->empty(204);
    }

    private function getParamId(): int
    {
        return
            $_GET['id']
            ?? new Exception("'id' param is required");
    }
}
