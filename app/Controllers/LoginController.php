<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Helpers\Auth\Rol;
use App\Helpers\Auth\UsuarioSession;
use App\Helpers\Auth\UsuarioSessionDto;
use App\Helpers\Response;
use App\Models\LoginModel;
use App\Models\UsuariosModel;
use DateTimeImmutable;

class LoginController extends BaseController
{
    public function __construct(
        private Response $response,
        private UsuariosModel $usuariosModel,
        private LoginModel $loginModel,
    ) {}

    public function index()
    {
        if (UsuarioSession::getUsuario()) {
            // Si el usuario ya inicio sesión, redirigir a pagina de inicio.
            $this->response->redirect(["page" => "dashboard"]);
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

        // Actualizar ultimo acceso
        $this->usuariosModel->actualizarUltimoAcceso($usuario->id_usuario);

        // Guardar la sesión utilizando un helper
        UsuarioSession::login(new UsuarioSessionDto(
            id: $usuario->id_usuario,
            rol: Rol::from($usuario->id_rol),
            nombre: $usuario->nombre_usuario,
            permisos: $usuario->permisos,
        ));

        $this->logger->info(
            "Usuario {id_usuario} ha iniciado sesión",
            ["id_usuario" => $usuario->id_usuario]
        );

        // Devolver respuesta con la direccion donde deberia redireccionar
        return $this->response->json([
            "redirect" => "?" . $this->response->buildQueryParams(["page" => "dashboard"])
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


    // --- MÓDULO RECUPERACIÓN (LIMPIO) ---
    public function recover(): string
    {
        $body = $this->response->getParsedBody();
        $email = $body["email"] ?? null;

        $usuario = $this->usuariosModel->findByEmail($email);

        if (!$usuario) {
            return $this->response->json(["message" => "Correo no registrado"], 404);
        }

        $codigo = sprintf("%06d", mt_rand(100000, 999999));
        $expiracion = new DateTimeImmutable('+15 minutes');

        $this->usuariosModel->saveRecoveryCode($usuario->id_usuario, $codigo, $expiracion);

        // AQUÍ LLAMAMOS AL MODELO, NO CONFIGURAMOS EL CORREO AQUÍ
        if ($this->loginModel->enviarCorreo($email, $codigo)) {
            return $this->response->json(["success" => true]);
        } else {
            return $this->response->json(["message" => "Error al enviar correo, revise el log."], 500);
        }
    }

    public function verify(): string
    {
        $body = $this->response->getParsedBody();
        $codigo = $body["codigo"] ?? '';
        $usuario = $this->usuariosModel->verifyRecoveryCode($codigo);

        if (!$usuario) {
            return $this->response->json(["message" => "Código inválido o expirado"], 401);
        }

        $_SESSION['recover_user_id'] = $usuario->id_usuario;
        return $this->response->json(["success" => true]);
    }

    public function reset(): string
    {
        $body = $this->response->getParsedBody();
        $new_pass = $body["new_pass"] ?? '';

        if (!isset($_SESSION['recover_user_id'])) {
            return $this->response->json(["message" => "Sesión expirada"], 401);
        }

        $this->usuariosModel->updatePasswordAndClearCode(
            $_SESSION['recover_user_id'],
            $new_pass,
        );

        unset($_SESSION['recover_user_id']);
        return $this->response->json(["success" => true]);
    }
}
