<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Helpers\Response;
use App\Models\UsuarioDTO;
use App\Models\UsuariosModel;
use CuyZ\Valinor\Mapper\TreeMapper;
use Exception;

class UsuariosController extends BaseController
{
    public function __construct(
        private Response $response,
        private TreeMapper $mapper,
        private UsuariosModel $usuariosModel,
    ) {}

    public function index(): string
    {
        return $this->templates->render('usuarios');
    }

    public function indexDetails(): string
    {
        $id = $_GET['id'] ?? null;
        $usuario = $this->usuariosModel->find($id);

        if (!$usuario) {
            $this->response->redirectToError(status: 404);
        }

        return $this->templates->render('usuario_perfil', [
            "id_usuario" => $id,
            "cedula" => $usuario->cedula_persona,
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
        $usuarios = $this->usuariosModel->query();
        return $this->response->json($usuarios);
    }

    public function find(): ?string
    {
        $id = $this->getParamId();
        $usuario = $this->usuariosModel->find($id);

        if (!$usuario) {
            return $this->response->empty(404);
        }

        return $this->response->json($usuario);
    }

    public function insert(): string
    {
        $body = $this->response->getParsedBody();
        $usuario = $this->mapper->map(UsuarioDTO::class, $body);

        if ($this->usuariosModel->find($usuario->nombre_usuario)) {
            return $this->response->json(['message' => 'El usuario ya existe'], 400);
        }

        $usuario = $this->usuariosModel->insert($usuario);
        return $this->response->json($usuario, 201);
    }

    public function update(): string
    {
        $body = $this->response->getParsedBody();
        $usuario = $this->mapper->map(UsuarioDTO::class, $body);

        if (!$this->usuariosModel->find($usuario->nombre_usuario)) {
            return $this->response->json(['message' => 'El usuario no existe'], 400);
        }

        $usuario = $this->usuariosModel->update($usuario);
        return $this->response->json($usuario, 201);
    }

    public function delete(): string|null
    {
        $id = $this->getParamId();

        if (!$this->usuariosModel->find($id)) {
            return $this->response->json(['message' => 'El usuario no existe'], 404);
        }

        $this->usuariosModel->delete($id);
        return $this->response->empty(204);
    }
}
