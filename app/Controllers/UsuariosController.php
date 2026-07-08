<?php

namespace App\Controllers;

use App\Controllers\Controller;
use App\Services\Auth\UserSession;
use App\Services\Auth\AuthenticatedUser;
use App\Services\ImageStorage;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Http\Status;
use App\Core\Tools;
use App\Models\Usuario;
use App\Models\UsuarioModel;
use App\Services\Logging\BitacoraLogger;

class UsuariosController extends Controller
{
    private AuthenticatedUser $user;

    public function __construct(
        private $logger = new BitacoraLogger(),
        private $image = new ImageStorage(),
        private $usuarioModel = new UsuarioModel(),
    ) {
        $this->user = UserSession::get();
    }

    public function index(): string
    {
        $this->protect("usuarios:ver");
        return $this->render('usuarios/index', [
            "usuario" => $this->user,
        ]);
    }

    public function query(): string
    {
        $this->protect("usuarios:ver");
        $usuarios = $this->usuarioModel->query();
        return Response::json($usuarios);
    }

    public function find(): ?string
    {
        $id = $this->getId();
        $usuario = $this->usuarioModel->findById($id);

        if (!$usuario) {
            return $this->notFound();
        }

        $this->protectAccess("usuarios:ver", $usuario);
        return Response::json($usuario);
    }

    public function insert(): string
    {
        $this->protect("usuarios:crear");
        $new = $this->validateBody();

        if ($this->usuarioModel->findByUsername($new->nombre_usuario)) {
            return Response::json(
                ['message' => 'El usuario ya existe'],
                Status::CONFLICT
            );
        }

        $new = $this->usuarioModel->insert($new);
        $this->logger->info("Usuario '{nombre_usuario}' creado", [
            'nombre_usuario' => $new->nombre_usuario,
            'id_usuario'     => $new->id_usuario,
            'datos_nuevos'   => $new,
        ]);

        return Response::json($new, Status::CREATED);
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

        return Response::json($new, Status::CREATED);
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

        return Response::noContent();
    }

    public function uploadImage(): string
    {
        $image = $_FILES['image'] ?? null;

        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !$image) {
            return Response::json(
                ['error' => 'Petición inválida'],
                Status::BAD_REQUEST
            );
        }

        $filename = $this->image->saveTemp($image);
        return Response::json([
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
        return Tools::map(Usuario::class, $body);
    }

    private function notFound(): string
    {
        return Response::json(
            ['message' => 'El usuario no existe'],
            Status::NOT_FOUND
        );
    }

    /** Si no es administrador y trata de editar otro perfil que no sea el suyo, entonces evitar el acceso. */
    protected function protectAccess(string $permiso, Usuario $usuario): void
    {
        if (
            !$this->user->hasPermiso($permiso)
            && $this->user->id !== $usuario->id_usuario
        ) {
            echo Response::json(
                ["message" => "No esta autorizado"],
                Status::FORBIDDEN
            );
            exit;
        }
    }
}
