<?php

namespace App\Controllers;

use App\Controllers\Controller;
use App\Core\Http\Request;
use App\Models\Equipos\EquipoDTO;
use App\Models\Equipos\EquiposModel;
use CuyZ\Valinor\Mapper\TreeMapper;

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
        $equipos = $this->equiposModel->query();
        return $this->json($equipos);
    }

    public function find(): ?string
    {
        $this->protect("equipos:ver");

        $id = $this->getId();
        $equipo = $this->equiposModel->find($id);

        return $equipo
            ? $this->json($equipo)
            : $this->json(null, 404);
    }

    public function insert(): string
    {
        $this->protect("equipos:crear");

        $equipo = $this->validateBody();
        $id = $equipo->codigo_equipo ?? "";

        if ($this->equiposModel->find($id)) {
            return $this->json(['message' => 'El equipo ya existe'], 400);
        }

        $equipo = $this->equiposModel->insert($equipo);
        return $this->json($equipo, 201);
    }

    public function update(): string
    {
        $this->protect("equipos:editar");

        $equipo = $this->validateBody();
        $id = $this->getId();

        if (!$this->equiposModel->find($id)) {
            return $this->notFound();
        }

        $equipo = $this->equiposModel->update($id, $equipo);
        return $this->json($equipo, 201);
    }

    public function delete(): string|null
    {
        $this->protect("equipos:eliminar");
        $id = $this->getId();

        if (!$this->equiposModel->find($id)) {
            return $this->notFound();
        }

        $this->equiposModel->delete($id);
        return $this->json(null, 204);
    }

    private function getId(): string
    {
        return Request::query("id") ?? "";
    }

    private function validateBody(): EquipoDTO
    {
        $body = Request::getParsedBody();
        return $this->mapper->map(EquipoDTO::class, $body);
    }

    private function notFound(): string
    {
        return $this->json(['message' => 'El equipo no existe'], 404);
    }
}
