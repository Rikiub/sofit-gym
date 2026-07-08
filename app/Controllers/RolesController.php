<?php

namespace App\Controllers;

use App\Controllers\Controller;
use App\Core\Http\Request;
use App\Core\Http\Status;
use App\Models\Rol;
use App\Models\RolModel;
use CuyZ\Valinor\Mapper\TreeMapper;

class RolesController extends Controller
{
    public function __construct(
        private TreeMapper $mapper,
        private RolModel $rolModel,
    ) {}

    public function index(): string
    {
        $permisos = $this->rolModel->queryPermisos();
        return $this->templates->render('roles', [
            "permisos" => $permisos
        ]);
    }

    public function query(): string
    {
        $this->protect("roles:ver");
        $roles = $this->rolModel->query();
        return $this->json($roles);
    }

    public function find(): ?string
    {
        $this->protect("roles:ver");

        $id = $this->getId();
        $rol = $this->rolModel->find($id);

        if (!$rol) {
            return $this->notFound();
        }

        return $this->json($rol);
    }

    public function update(): string
    {
        $this->protect("roles:editar");

        $id = $this->getId();
        $rol = $this->validateBody();

        if (!$this->rolModel->find($id)) {
            $this->notFound();
        }

        $rol = $this->rolModel->update($id, $rol);
        return $this->json($rol, Status::CREATED);
    }

    private function notFound(): string
    {
        return $this->json(["message" => "El rol no existe"], Status::NOT_FOUND);
    }

    private function getId(): int
    {
        return Request::queryInt("id") ?? 0;
    }

    private function validateBody(): Rol
    {
        $body = Request::getParsedBody();
        return $this->mapper->map(Rol::class, $body);
    }
}
