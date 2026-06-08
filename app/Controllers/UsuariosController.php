<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Helpers\ImagesManager;
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
        return $this->templates->render('usuarios/gestion');
    }

    public function indexDetails(): string
    {
        $id = $_GET['id'] ?? null;
        $usuario = $this->usuariosModel->find($id);

        if (!$usuario) {
            $this->response->redirectToError(status: 404);
        }

        return $this->templates->render('usuarios/perfil', [
            "id_usuario" => $id,
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

        // Verificar que no exista
        $nombre_usuario = $body["nombre_usuario"] ?? "";
        if ($this->usuariosModel->find($nombre_usuario)) {
            return $this->response->json(['message' => 'El usuario ya existe'], 400);
        }

        // Ajustar foto de perfil
        $imagen_url = $body["imagen_url"] ?? null;
        if ($imagen_url) {
            $body["imagen_url"] = ImagesManager::moveFromTemp($imagen_url, "/usuarios");
        }

        // Insertar en base de datos
        $usuario = $this->mapper->map(UsuarioDTO::class, $body);
        $usuario = $this->usuariosModel->insert($usuario);

        // Devolver respuesta
        return $this->response->json($usuario, 201);
    }

    public function update(): string
    {
        $body = $this->response->getParsedBody();

        // Verificar que exista
        $nombre_usuario = $body["nombre_usuario"] ?? "";
        $oldUsuario = $this->usuariosModel->find($nombre_usuario);
        if (!$oldUsuario) {
            return $this->response->json(['message' => 'El usuario no existe'], 400);
        }

        // Actualizar foto de perfil
        $imagen_url = $body["imagen_url"] ?? null;
        if ($imagen_url) {
            ImagesManager::delete($oldUsuario->imagen_url ?? "");
            $body["imagen_url"] = ImagesManager::moveFromTemp($imagen_url, "/usuarios");
        }

        // Actualizar base de datos
        $usuario = $this->mapper->map(UsuarioDTO::class, $body);
        $usuario = $this->usuariosModel->update($usuario);

        // Devolver respuesta
        return $this->response->json($usuario, 201);
    }

    public function delete(): string|null
    {
        $id = $this->getParamId();
        $usuario = $this->usuariosModel->find($id);

        if (!$usuario) {
            return $this->response->json(['message' => 'El usuario no existe'], 404);
        }

        $this->usuariosModel->delete($usuario->id_usuario);
        if ($usuario->imagen_url) {
            ImagesManager::delete($usuario->imagen_url);
        }

        return $this->response->empty(204);
    }
}
