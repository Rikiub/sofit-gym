<?php

namespace App\Controllers;

use App\Controllers\Controller;
use App\Core\Http\Request;
use App\Models\Equipos\MantenimientoEquipoDTO;
use App\Models\Equipos\MantenimientoEquipoModel;
use CuyZ\Valinor\Mapper\TreeMapper;

class EquiposMantenimientoController extends Controller
{
    public function __construct(
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
        return $this->json($data);
    }

    public function find(): ?string
    {
        $this->protect("equipos:ver");

        $id = Request::queryInt("id") ?? 0;
        $data = $this->model->find($id);

        return $data
            ? $this->json($data)
            : $this->json(null, 404);
    }

    public function insert(): string
    {
        $this->protect("equipos:crear");

        $body = $this->getParsedBody();
        $data = $this->mapper->map(MantenimientoEquipoDTO::class, $body);

        $data = $this->model->insert($data);
        return $this->json($data, 201);
    }

    public function update(): string
    {
        $this->protect("equipos:editar");

        $body = $this->getParsedBody();
        $data = $this->mapper->map(MantenimientoEquipoDTO::class, $body);

        if (!$this->model->find($data->id_mantenimiento)) {
            return $this->json(['message' => 'El mantenimiento no existe'], 404);
        }

        $data = $this->model->update($data);
        return $this->json($data, 201);
    }

    public function delete(): string|null
    {
        $this->protect("equipos:eliminar");
        $id = Request::queryInt("id") ?? 0;

        if (!$this->model->find($id)) {
            return $this->json(['message' => 'El mantenimiento no existe'], 404);
        }

        $this->model->delete($id);
        return $this->json(null, 204);
    }
}
