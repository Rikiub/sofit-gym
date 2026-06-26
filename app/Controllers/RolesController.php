<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Helpers\Http\Request;
use App\Helpers\Http\Response;
use App\Models\RolDTO;
use App\Models\RolesModel;
use CuyZ\Valinor\Mapper\TreeMapper;
use Exception;

class RolesController extends BaseController
{
    public function __construct(
        private TreeMapper $mapper,
        private RolesModel $rolesModel,
    ) {}

    public function index(): string
    {
        $permisos = $this->rolesModel->queryPermisos();
        return $this->templates->render('roles', [
            "permisos" => $permisos
        ]);
    }

    public function query(): string
    {
        $this->protect("roles:ver");
        $roles = $this->rolesModel->query();
        return $this->json($roles);
    }

    public function find(): ?string
    {
        $this->protect("roles:ver");

        $id = $this->getParamId();
        $rol = $this->rolesModel->find($id);

        if (!$rol) {
            return $this->json(null, 404);
        }

        return $this->json($rol);
    }

    public function insert(): string
    {
        $this->protect("roles:crear");

        $body = $this->getParsedBody();
        $permiso = $this->mapper->map(RolDTO::class, $body);

        if ($this->rolesModel->find($permiso->nombre)) {
            return $this->json(['message' => 'El rol ya existe'], 400);
        }

        $permiso = $this->rolesModel->insert($permiso);
        return $this->json($permiso, 201);
    }

    public function update(): string
    {
        $this->protect("roles:editar");

        $body = $this->getParsedBody();
        $permiso = $this->mapper->map(RolDTO::class, $body);

        if (!$this->rolesModel->find($permiso->id_rol)) {
            return $this->json(['message' => 'El rol no existe'], 404);
        }

        $permiso = $this->rolesModel->update($permiso);
        return $this->json($permiso, 201);
    }

    public function delete(): string|null
    {
        $this->protect("roles:eliminar");

        $id = $this->getParamId();
        $permiso = $this->rolesModel->find($id);

        if (!$permiso) {
            return $this->json(['message' => 'El rol no existe'], 404);
        }

        $this->rolesModel->delete($permiso->id_rol);
        return $this->json(null, 204);
    }

    private function getParamId(): string
    {
        return
            $_GET['id']
            ?? throw new Exception("'id' param is required");
    }
}
