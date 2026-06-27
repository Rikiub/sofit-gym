<?php

namespace App\Controllers;

use App\Controllers\Controller;
use App\Core\Http\Request;
use App\Models\Equipos\EquipoDTO;
use App\Models\Equipos\EquiposModel;
use CuyZ\Valinor\Mapper\TreeMapper;
use Exception;

class EquiposController extends Controller
{
    public function __construct(
        private TreeMapper $mapper,
        private EquiposModel $equiposModel,
    ) {}

    public function index()
    {
        return $this->templates->render('equipos');
    }

    public function query()
    {
        $this->protect("equipos:ver");
        $results = $this->equiposModel->query();
        return $this->json($results);
    }

    public function find(): ?string
    {
        $this->protect("equipos:ver");

        $id = Request::query("id") ?? "";
        $equipo = $this->equiposModel->find($id);

        if (!$equipo) {
            return $this->json(null, 404);
        }

        return $this->json($equipo);
    }

    public function insert(): string
    {
        $this->protect("equipos:crear");

        $body = $this->getParsedBody();
        $equipo = $this->mapper->map(EquipoDTO::class, $body);

        // Verificar que el equipo no exista
        if ($this->equiposModel->find($equipo->codigo)) {
            return $this->json(['message' => 'El equipo ya existe'], 400);
        }

        $equipo = $this->equiposModel->insert($equipo);
        return $this->json($equipo, 201);
    }

    public function update(): string
    {
        $this->protect("equipos:editar");

        $body = $this->getParsedBody();
        $equipo = $this->mapper->map(EquipoDTO::class, $body);

        if (!$this->equiposModel->find($equipo->codigo)) {
            return $this->json(['message' => 'El equipo no existe'], 404);
        }

        $equipo = $this->equiposModel->update($equipo);
        return $this->json($equipo, 201);
    }

    public function delete(): string|null
    {
        $this->protect("equipos:eliminar");
        $id = Request::query("id") ?? "";

        if (!$this->equiposModel->find($id)) {
            return $this->json(['message' => 'El equipo no existe'], 404);
        }

        $this->equiposModel->delete($id);
        return $this->json(null, 204);
    }
}
