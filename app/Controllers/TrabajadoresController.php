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

    public function summary(): string
    {
        $this->protect("trabajadores:ver");
        $summary = $this->trabajadoresModel->getSummary();
        return $this->json($summary);
    }

    public function find(): ?string
    {
        $this->protect("trabajadores:ver");

        $id = $this->getId();
        $trabajador = $this->trabajadoresModel->find($id);

        return $trabajador
            ? $this->json($trabajador)
            : $this->json(null, 404);
    }

    public function insert(): string
    {
        $this->protect("trabajadores:crear");

        $trabajador = $this->validateBody();
        $id = $this->getId();

        if ($this->trabajadoresModel->checkDuplicate($id)) {
            return $this->json(['message' => 'El trabajador ya existe'], 400);
        }

        $trabajador = $this->trabajadoresModel->insert($trabajador);
        return $this->json($trabajador, 201);
    }

    public function update(): string
    {
        $this->protect("trabajadores:editar");

        $trabajador = $this->validateBody();
        $id = $this->getId();

        if (!$this->trabajadoresModel->find($id)) {
            return $this->notFound();
        }

        $trabajador = $this->trabajadoresModel->update($id, $trabajador);
        return $this->json($trabajador, 201);
    }

    public function delete(): string|null
    {
        $this->protect("trabajadores:eliminar");
        $id = $this->getId();

        if (!$this->trabajadoresModel->find($id)) {
            return $this->notFound();
        }

        $this->trabajadoresModel->delete($id);
        return $this->json(null, 204);
    }

    private function notFound(): string
    {
        return $this->json(["message" => "Trabajador no encontrado"], 404);
    }

    private function getId(): string
    {
        return Request::query("id") ?? "";
    }

    private function validateBody(): TrabajadorDTO
    {
        $body = Request::getParsedBody();
        return $this->mapper->map(TrabajadorDTO::class, $body);
    }
}
