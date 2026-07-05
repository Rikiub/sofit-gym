<?php

namespace App\Controllers;

use App\Controllers\Controller;
use App\Services\Auth\UserSession;
use App\Services\Auth\AuthenticatedUser;
use App\Services\ImageStorage;
use App\Core\Http\Request;
use App\Core\Http\StatusCode;
use App\Models\Usuario;
use App\Models\UsuarioModel;
use CuyZ\Valinor\Mapper\TreeMapper;

class UsuariosController extends Controller
{
    private AuthenticatedUser $user;

    public function __construct(
        private TreeMapper $mapper,
        private UsuarioModel $usuarioModel,
        private ImageStorage $image,
    ) {
        $this->user = UserSession::get();
    }

    public function index(): string
    {
        $this->protect("usuarios:ver");
        return $this->templates->render('usuarios/index', [
            "usuario" => $this->user,
        ]);
    }

    public function query(): string
    {
        $this->protect("usuarios:ver");
        $usuarios = $this->usuarioModel->query();
        return $this->json($usuarios);
    }

    public function find(): ?string
    {
        $id = $this->getId();
        $usuario = $this->usuarioModel->findById($id);

        if (!$usuario) {
            return $this->notFound();
        }

        $this->protectAccess("usuarios:ver", $usuario);
        return $this->json($usuario);
    }

    public function insert(): string
    {
        $this->protect("usuarios:crear");
        $new = $this->validateBody();

        if ($this->usuarioModel->findByUsername($new->nombre_usuario)) {
            return $this->json(
                ['message' => 'El usuario ya existe'],
                StatusCode::CONFLICT
            );
        }

        $new = $this->usuarioModel->insert($new);
        $this->logger->info("Usuario '{nombre_usuario}' creado", [
            'nombre_usuario' => $new->nombre_usuario,
            'id_usuario'     => $new->id_usuario,
            'datos_nuevos'   => $new,
        ]);

        return $this->json($new, StatusCode::CREATED);
    }

    public function update(): string
    {
        $id = $this->getId();

        // Validar
        $old = $this->usuarioModel->findById($id);
        if (!$old) {
            return $this->notFound();
        }
        $this->protectAccess("usuarios:editar", $old);

        // Actualizar
        $new = $this->validateBody();
        $new = $this->usuarioModel->update($id, $new);

        if ($old->contrasena_hash !== $new->contrasena_hash) {
            $this->usuarioModel->updatePassword($id, $new->contrasena_hash);
        }

        // Retornar
        $this->logger->info("Usuario '{nombre_usuario}' actualizado", [
            'nombre_usuario' => $old->nombre_usuario,
            'id_usuario'     => $id,
            'datos_previos'  => $old,
            'datos_nuevos'   => $new,
        ]);

        return $this->json($new, StatusCode::CREATED);
    }

    public function delete(): string|null
    {
        $this->protect("usuarios:eliminar");

        $id = $this->getId();
        $old = $this->usuarioModel->findById($id);

        if (!$old) {
            return $this->notFound();
        }

        $this->usuarioModel->delete($id);
        $this->logger->info("Usuario '{nombre_usuario}' eliminado", [
            'nombre_usuario' => $old->nombre_usuario,
            'id_usuario'     => $id,
            'datos_previos'  => $old,
        ]);

        return $this->json(null, StatusCode::NO_CONTENT);
    }

    public function uploadImage(): string
    {
        $image = $_FILES['image'] ?? null;

        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !$image) {
            return $this->json(
                ['error' => 'Petición inválida'],
                StatusCode::BAD_REQUEST
            );
        }

        $filename = $this->image->saveTemp($image);
        return $this->json([
            'temp_filename' => $filename
        ]);
    }

    // Helpers
    private function getId(): int
    {
        return Request::queryInt("id") ?? 0;
    }

    private function validateBody(): Usuario
    {
        $body = Request::getParsedBody();
        return $this->mapper->map(Usuario::class, $body);
    }

    private function notFound(): string
    {
        return $this->json(
            ['message' => 'El usuario no existe'],
            StatusCode::NOT_FOUND
        );
    }

    /** Si no es administrador y trata de editar otro perfil que no sea el suyo, entonces evitar el acceso. */
    protected function protectAccess(string $permiso, Usuario $usuario): void
    {
        if (
            !$this->user->hasPermiso($permiso)
            && $this->user->id !== $usuario->id_usuario
        ) {
            echo $this->json(
                ["message" => "No esta autorizado"],
                StatusCode::FORBIDDEN
            );
            exit;
        }
    }
}
