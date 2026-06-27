<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\TrabajadorDTO;
use App\Models\TrabajadoresModel;
use CuyZ\Valinor\Mapper\TreeMapper;
use Exception;

class TrabajadoresController extends BaseController
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

    private function getCedulaParam(): string
    {
        $cedula = $_GET['cedula'] ?? $_GET['id'] ?? null;
        if (!$cedula) {
            throw new Exception("'id' or 'cedula' param is required");
        }
        return $cedula;
    }

    public function query(): string
    {
        $this->protect("trabajadores:ver");

        $search = $_GET["search"] ?? null;
        $id_rol = (int)($_GET["id_rol"] ?? 0);

        $trabajadores = $this->trabajadoresModel->query($search, $id_rol);
        return $this->json($trabajadores);
    }

    public function find(): ?string
    {
        $this->protect("trabajadores:ver");

        $cedula = $this->getCedulaParam();
        $trabajador = $this->trabajadoresModel->find($cedula);

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
        $cedula = $this->getCedulaParam();

        if (!$this->trabajadoresModel->find($cedula)) {
            return $this->json(['message' => 'El trabajador no existe'], 404);
        }

        $this->trabajadoresModel->delete($cedula);
        return $this->json(null, 204);
    }
}
