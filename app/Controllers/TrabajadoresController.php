<?php

namespace App\Controllers;

use App\Controllers\Controller;
use App\Core\Http\Request;
use App\Models\TrabajadorDTO;
use App\Models\TrabajadoresModel;
use CuyZ\Valinor\Mapper\TreeMapper;

class TrabajadoresController extends Controller
{
    public function __construct(
        private TreeMapper $mapper,
        private TrabajadoresModel $trabajadoresModel,
    ) {}

    public function index(): string
    {
        $this->protect("trabajadores:ver");
        return $this->templates->render('trabajadores');
    }

    public function query(): string
    {
        $this->protect("trabajadores:ver");

        $search = Request::query("search") ?? null;
        $id_rol = Request::queryInt("id_rol") ?? 0;

        $trabajadores = $this->trabajadoresModel->query($search, $id_rol);
        return $this->json($trabajadores);
    }

    public function find(): ?string
    {
        $this->protect("trabajadores:ver");

        $id = Request::query("id") ?? "";
        $trabajador = $this->trabajadoresModel->find($id);

        if (!$trabajador) {
            return $this->json(null, 404);
        }

        return $this->json($trabajador);
    }

    public function insert(): string
    {
        $this->protect("trabajadores:crear");

        $body = $this->getParsedBody();
        $trabajador = $this->mapper->map(TrabajadorDTO::class, $body);

        if ($this->trabajadoresModel->checkDuplicate($trabajador->cedula)) {
            return $this->json(['message' => 'El trabajador ya existe'], 400);
        }

        $trabajador = $this->trabajadoresModel->insert($trabajador);
        return $this->json($trabajador, 201);
    }

    public function update(): string
    {
        $this->protect("trabajadores:editar");

        $body = $this->getParsedBody();
        $trabajador = $this->mapper->map(TrabajadorDTO::class, $body);

        if (!$this->trabajadoresModel->find($trabajador->cedula)) {
            return $this->json(['message' => 'El trabajador no existe'], 400);
        }

        $trabajador = $this->trabajadoresModel->update($trabajador);
        return $this->json($trabajador, 201);
    }

    public function delete(): string|null
    {
        $this->protect("trabajadores:eliminar");
        $id = Request::query("id") ?? "";

        if (!$this->trabajadoresModel->find($id)) {
            return $this->json(['message' => 'El trabajador no existe'], 404);
        }

        $this->trabajadoresModel->delete($id);
        return $this->json(null, 204);
    }
}
