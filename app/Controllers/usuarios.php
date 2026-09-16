<?php

namespace App\Controllers;

use App\Core\ControllerTools;
use App\Services\Auth\UserSession;
use App\Services\Auth\CurrentUser;
use App\Services\ImageStorage;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Http\Status;
use App\Core\Tools;
use App\Models\BitacoraModel;
use App\Models\Usuario;
use App\Models\UsuarioModel;

$logger = new BitacoraModel();
$image = new ImageStorage();
$usuarioModel = new UsuarioModel();
$user = UserSession::get();

function getId(): int
{
    return Request::queryInt("id") ?? 0;
}

function validateBody(): Usuario
{
    $body = Request::getParsedBody();
    return Tools::map(Usuario::class, $body);
}

function notFound(): string
{
    return Response::json(
        ['message' => 'El usuario no existe'],
        Status::NOT_FOUND
    );
}

/** Si no es administrador y trata de editar otro perfil que no sea el suyo, entonces evitar el acceso. */
function protectAccess(string $permiso, Usuario $usuario, ?CurrentUser $user): void
{
    if (
        !$user->hasPermiso($permiso)
        && $user->id !== $usuario->id_usuario
    ) {
        echo Response::json(
            ["message" => "No esta autorizado"],
            Status::FORBIDDEN
        );
        exit;
    }
}

switch (ControllerTools::action()) {
    case "index":
        ControllerTools::protect("usuarios:ver");
        return ControllerTools::render('usuarios/index', [
            "usuario" => $user,
        ]);

    case "query":
        ControllerTools::protect("usuarios:ver");
        $usuarios = $usuarioModel->query();
        return Response::json($usuarios);

    case "find":
        $id = getId();
        $usuario = $usuarioModel->findById($id);

        if (!$usuario) {
            return notFound();
        }

        protectAccess("usuarios:ver", $usuario, $user);
        return Response::json($usuario);

    case "insert":
        ControllerTools::protect("usuarios:crear");
        $new = validateBody();

        if ($usuarioModel->findByUsername($new->nombre_usuario)) {
            return Response::json(
                ['message' => 'El usuario ya existe'],
                Status::CONFLICT
            );
        }

        $new = $usuarioModel->insert($new);
        $logger->log("Usuario '{nombre_usuario}' creado", [
            "modulo" => "usuarios",
            "accion" => "crear",

            'nombre_usuario' => $new->nombre_usuario,
            'id_usuario'     => $new->id_usuario,
            'datos_nuevos'   => $new,
        ]);

        return Response::json($new, Status::CREATED);

    case "update":
        $id = getId();

        // Validar
        $old = $usuarioModel->findById($id);
        if (!$old) {
            return notFound();
        }
        protectAccess("usuarios:editar", $old, $user);

        // Actualizar
        $new = validateBody();
        $new = $usuarioModel->update($id, $new);

        if ($old->contrasena_hash !== $new->contrasena_hash) {
            $usuarioModel->updatePassword($id, $new->contrasena_hash);
        }

        // Retornar
        $logger->log("Usuario '{nombre_usuario}' actualizado", [
            "modulo" => "usuarios",
            "accion" => "editar",

            'nombre_usuario' => $old->nombre_usuario,
            'id_usuario'     => $id,
            'datos_previos'  => $old,
            'datos_nuevos'   => $new,
        ]);

        return Response::json($new, Status::CREATED);

    case "delete":
        ControllerTools::protect("usuarios:eliminar");

        $id = getId();
        $old = $usuarioModel->findById($id);

        if (!$old) {
            return notFound();
        }

        $usuarioModel->delete($id);
        $logger->log("Usuario '{nombre_usuario}' eliminado", [
            "modulo" => "usuarios",
            "accion" => "eliminar",

            'nombre_usuario' => $old->nombre_usuario,
            'id_usuario'     => $id,
            'datos_previos'  => $old,
        ]);

        return Response::noContent();

    case "uploadImage":
        $img = $_FILES['image'] ?? null;

        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !$img) {
            return Response::json(
                ['error' => 'Petición inválida'],
                Status::BAD_REQUEST
            );
        }

        $filename = $image->saveTemp($img);
        return Response::json([
            'temp_filename' => $filename
        ]);
}
