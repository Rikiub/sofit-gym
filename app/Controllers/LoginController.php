<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Helpers\Auth\UsuarioSession;
use App\Helpers\Auth\UsuarioSessionDto;
use App\Helpers\Response;
use App\Models\UsuariosModel;

class LoginController extends BaseController
{
    public function __construct(
        private Response $response,
        private UsuariosModel $usuariosModel,
    ) {}

    public function index()
    {
        if (UsuarioSession::getUsuario()) {
            // Si el usuario ya inicio sesión, redirigir a pagina de inicio.
            $this->response->redirect(["page" => "inicio"]);
            exit;
        }

        return $this->templates->render("login");
    }

    public function login(): string
    {
        $body = $this->response->getParsedBody();
        $nombre_usuario = $body["nombre_usuario"] ?? null;
        $contrasena = $body["contrasena"] ?? null;

        $usuario = $this->usuariosModel->find($nombre_usuario);
        if (!$usuario) {
            return $this->invalidInput();
        };

        if (!password_verify($contrasena, $usuario->contrasena_hash)) {
            return $this->invalidInput();
        }

        // Guardar la sesión utilizando un helper
        UsuarioSession::login(new UsuarioSessionDto(
            id: $usuario->id_usuario,
            id_rol: $usuario->id_rol,
            rol: $usuario->rol,
            nombre: $usuario->nombre_usuario,
        ));

        $this->logger->info(
            "Usuario {id_usuario} ha iniciado sesión",
            ["id_usuario" => $usuario->id_usuario]
        );

        // Devolver respuesta con la direccion donde deberia redireccionar
        return $this->response->json([
            "redirect" => "?" . $this->response->buildQueryParams(["page" => "inicio"])
        ]);
    }

    public function logout(): void
    {
        $usuario = UsuarioSession::getUsuario();
        $this->logger->info(
            "Usuario {id_usuario} ha cerrado sesión",
            ["id_usuario" => $usuario->id]
        );
        UsuarioSession::logout();

        $this->response->redirect(["page" => "login"]);
        exit;
    }

    /** Mensaje de error generico en caso de ingresar datos incorrectos */
    private function invalidInput(): string
    {
        return $this->response->json(["message" => "Usuario o contraseña incorrectos"], 401);
    }
}
