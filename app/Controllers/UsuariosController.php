<?php

namespace App\Controllers;

use App\Controllers\Controller;
use App\Core\Auth\UsuarioSession;
use App\Core\Auth\UsuarioSessionDto;
use App\Core\Http\Request;
use App\Core\ImagesManager;
use App\Models\UsuarioDTO;
use App\Models\UsuariosModel;
use CuyZ\Valinor\Mapper\TreeMapper;

class UsuariosController extends Controller
{
    private UsuarioSessionDto $usuarioSesion;

    public function __construct(
        private TreeMapper $mapper,
        private UsuariosModel $usuariosModel,
    ) {
        $this->usuarioSesion = UsuarioSession::getCurrent();
    }

    public function index(): string
    {
        $this->protect("usuarios:ver");
        return $this->templates->render('usuarios/index', [
            "usuario" => $this->usuarioSesion,
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
        $id = $this->getId();
        $usuario = $this->usuariosModel->findById($id);

        if (!$usuario) {
            $this->notFound();
        }

        $this->protectAccess("usuarios:ver", $usuario);
        return $this->json($usuario);
    }

    public function insert(): string
    {
        $this->protect("usuarios:crear");
        $new = $this->validateBody();

        if ($this->usuariosModel->findByUsername($new->nombre_usuario)) {
            return $this->json(['message' => 'El usuario ya existe'], 400);
        }

        $new = $this->usuariosModel->insert($new);
        $this->logger->info("Usuario '{nombre_usuario}' creado", [
            'nombre_usuario' => $new->nombre_usuario,
            'id_usuario'     => $new->id_usuario,
            'datos_nuevos'   => $new,
        ]);

        return $this->json($new, 201);
    }

    public function update(): string
    {
        $id = $this->getId();

        $old = $this->usuariosModel->findById($id);
        if (!$old) {
            return $this->notFound();
        }
        $this->protectAccess("usuarios:editar", $old);

        $new = $this->validateBody();
        $new = $this->usuariosModel->update($id, $new);

        $this->logger->info("Usuario '{nombre_usuario}' actualizado", [
            'nombre_usuario' => $old->nombre_usuario,
            'id_usuario'     => $id,
            'datos_previos'  => $old,
            'datos_nuevos'   => $new,
        ]);

        return $this->json($new, 201);
    }

    public function delete(): string|null
    {
        $this->protect("usuarios:eliminar");

        $id = $this->getId();
        $old = $this->usuariosModel->findById($id);

        if (!$old) {
            return $this->notFound();
        }

        $this->usuariosModel->delete($id);
        $this->logger->info("Usuario '{nombre_usuario}' eliminado", [
            'nombre_usuario' => $old->nombre_usuario,
            'id_usuario'     => $id,
            'datos_previos'  => $old,
        ]);

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

    // Helpers
    private function getId(): int
    {
        return Request::queryInt("id") ?? 0;
    }

    private function validateBody(): UsuarioDTO
    {
        $body = Request::getParsedBody();
        return $this->mapper->map(UsuarioDTO::class, $body);
    }

    private function notFound(): string
    {
        return $this->json(['message' => 'El usuario no existe'], 404);
    }

    /**
     * Si no es administrador y trata de editar otro perfil que no sea el suyo
     * entonces evitar el acceso.
     */
    protected function protectAccess(string $permiso, UsuarioDTO $usuario): void
    {
        if (
            !$this->usuarioSesion->hasPermiso($permiso)
            && $this->usuarioSesion->id !== $usuario->id_usuario
        ) {
            echo $this->json(["message" => "No esta autorizado"], 403);
            exit;
        }
    }
}
