<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Helpers\Response;
use App\Models\PermisoDTO;
use App\Models\PermisosModel;
use App\Models\RolDTO;
use App\Models\RolesModel;
use CuyZ\Valinor\Mapper\TreeMapper;
use Exception;

class RolesController extends BaseController
{
    public function __construct(
        private Response $response,
        private TreeMapper $mapper,
        private RolesModel $rolesModel,
        private PermisosModel $permisosModel,
    ) {}

    public function index(): string
    {
        $permisos = $this->permisosModel->query();
        return $this->templates->render('roles', [
            "permisos" => $permisos
        ]);
    }

    private function getParamId(): string
    {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            throw new Exception("'id' param is required");
        }
        return $id;
    }

    public function query(): string
    {
        $this->protect("roles:ver");
        $roles = $this->rolesModel->query();
        return $this->response->json($roles);
    }

    public function find(): ?string
    {
        $this->protect("roles:ver");

        $id = $this->getParamId();
        $rol = $this->rolesModel->find($id);

        if (!$rol) {
            return $this->response->empty(404);
        }

        return $this->response->json($rol);
    }

    public function insert(): string
    {
        $this->protect("roles:crear");

        $body = $this->response->getParsedBody();
        $permiso = $this->mapper->map(RolDTO::class, $body);

        if ($this->rolesModel->find($permiso->nombre)) {
            return $this->response->json(['message' => 'El rol ya existe'], 400);
        }

        $permiso = $this->rolesModel->insert($permiso);
        return $this->response->json($permiso, 201);
    }

    public function update(): string
    {
        $this->protect("roles:editar");

        $body = $this->response->getParsedBody();
        $permiso = $this->mapper->map(RolDTO::class, $body);

        if (!$this->rolesModel->find($permiso->id_rol)) {
            return $this->response->json(['message' => 'El rol no existe'], 404);
        }

        $permiso = $this->rolesModel->update($permiso);
        return $this->response->json($permiso, 201);
    }

    public function delete(): string|null
    {
        $this->protect("roles:eliminar");

        $id = $this->getParamId();
        $permiso = $this->permisosModel->find($id);

        if (!$permiso) {
            return $this->response->json(['message' => 'El rol no existe'], 404);
        }

        $this->permisosModel->delete($permiso->id_permiso);
        return $this->response->empty(204);
    }
}
