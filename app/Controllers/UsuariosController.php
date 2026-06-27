<?php

namespace App\Controllers;

use App\Controllers\Controller;
use App\Core\Auth\UsuarioSession;
use App\Core\Http\Request;
use App\Core\ImagesManager;
use App\Models\UsuarioDTO;
use App\Models\UsuariosModel;
use CuyZ\Valinor\Mapper\TreeMapper;

class UsuariosController extends Controller
{
    public function __construct(
        private TreeMapper $mapper,
        private UsuariosModel $usuariosModel,
    ) {}

    public function index(): string
    {
        $this->protect("usuarios:ver");

        $usuario = UsuarioSession::getUsuario();
        return $this->templates->render('usuarios/index', [
            "usuario" => $usuario,
        ]);
    }

    public function query(): string
    {
        $this->protect("usuarios:ver");
        $usuarios = $this->usuariosModel->query();
        return $this->json($usuarios);
    }

    public function find(): ?string
    {
        $id = Request::queryInt("id") ?? 0;
        $usuario = $this->usuariosModel->find($id);

        if (!$usuario) {
            return $this->json(null, 404);
        }

        // Si no es administrador y trata de buscar otro perfil que no sea el suyo
        // entonces pararlo
        $usuarioSesion = UsuarioSession::getUsuario();
        if (
            !$usuarioSesion->hasPermiso("usuarios:ver")
            && $usuarioSesion->id !== $usuario->id_usuario
        ) {
            return  $this->json(["message" => "No esta autorizado"], 403);
        }

        return $this->json($usuario);
    }

    public function insert(): string
    {
        $this->protect("usuarios:crear");
        $body = $this->getParsedBody();

        // Verificar que no exista
        $nombre_usuario = $body["nombre_usuario"] ?? "";
        if ($this->usuariosModel->find($nombre_usuario)) {
            return $this->json(['message' => 'El usuario ya existe'], 400);
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
        return $this->json($usuario, 201);
    }

    public function update(): string
    {
        $body = $this->getParsedBody();

        // Verificar que exista
        $nombre_usuario = $body["nombre_usuario"] ?? "";
        $oldUsuario = $this->usuariosModel->find($nombre_usuario);
        if (!$oldUsuario) {
            return $this->json(['message' => 'El usuario no existe'], 400);
        }

        // Si no es administrador y trata de editar otro perfil que no sea el suyo
        // entonces pararlo
        $usuarioSesion = UsuarioSession::getUsuario();
        if (
            !$usuarioSesion->hasPermiso("usuarios:editar")
            && $usuarioSesion->id !== $oldUsuario->id_usuario
        ) {
            return $this->json(["message" => "No esta autorizado"], 403);
        }

        // Actualizar foto de perfil
        $imagen_url = $body["imagen_url"] ?? null;
        if ($imagen_url !== $oldUsuario->imagen_url) {
            ImagesManager::delete($oldUsuario->imagen_url ?? "");
            $body["imagen_url"] = ImagesManager::moveFromTemp($imagen_url, "/usuarios");
        }

        // Actualizar base de datos
        $usuario = $this->mapper->map(UsuarioDTO::class, $body);
        $usuario = $this->usuariosModel->update($usuario);

        // Devolver respuesta
        return $this->json($usuario, 201);
    }

    public function delete(): string|null
    {
        $this->protect("usuarios:eliminar");

        $id = Request::queryInt("id") ?? 0;
        $usuario = $this->usuariosModel->find($id);

        if (!$usuario) {
            return $this->json(['message' => 'El usuario no existe'], 404);
        }

        $this->usuariosModel->delete($usuario->id_usuario);
        if ($usuario->imagen_url) {
            ImagesManager::delete($usuario->imagen_url);
        }

        return $this->json(null, 204);
    }

    public function uploadImage(): string
    {
        $image = $_FILES['image'] ?? null;

        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !$image) {
            return $this->json(['error' => 'Petición inválida'], 400);
        }

        $filename = ImagesManager::saveTemp($image);
        return $this->json([
            'temp_filename' => $filename
        ]);
    }
}
