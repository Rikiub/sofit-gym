<?php

namespace App\Controllers;

use App\Controllers\Controller;
use App\Services\Auth\UserSession;
use App\Services\Auth\AuthenticatedUser;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Http\Status;
use App\Core\Tools;
use App\Models\UsuarioModel;
use App\Services\Logging\BitacoraLogger;
use PHPMailer\PHPMailer\PHPMailer;
use DateTimeImmutable;

class LoginController extends Controller
{
    private PHPMailer $mailer;

    public function __construct(
        private $usuarioModel = new UsuarioModel(),
        private $logger = new BitacoraLogger(),
    ) {
        $this->mailer = Tools::getMailer();
    }

    public function index()
    {
        if (UserSession::get()) {
            // Si el usuario ya inicio sesión, redirigir a pagina de inicio.
            Response::redirect(["page" => "dashboard"]);
        }

        return $this->render("login");
    }

    public function login(): string
    {
        $body = Request::getParsedBody();
        $nombre_usuario = $body["nombre_usuario"] ?? null;
        $contrasena = $body["contrasena"] ?? null;

        $usuario = $this->usuarioModel->findByUsername($nombre_usuario);
        if (!$usuario) {
            return $this->invalidInput();
        };

        if (!password_verify($contrasena, $usuario->contrasena_hash)) {
            $this->logger->info(
                "Usuario {nombre_usuario} ha fallado al iniciar sesión",
                ["nombre_usuario" => $usuario->nombre_usuario]
            );

            $maximoIntentos = 3;
            $minutosBloqueo = 15;
            $duracion = new DateTimeImmutable("-{$minutosBloqueo} minutes");

            // Si el usuario excedio el maximo numero de intentos
            if ($this->usuarioModel->intentosFallidos(
                $usuario->id_usuario,
                $duracion,
            ) >= $maximoIntentos) {
                return Response::json(
                    ["message" => "Numero de intentos excedidos. Vuelva a intentarlo en {$minutosBloqueo} minutos."],
                    Status::UNAUTHORIZED
                );
            } else {
                $this->usuarioModel->insertIntentoAcceso($usuario->id_usuario, exito: false);
            }

            return $this->invalidInput();
        }

        // Actualizar estado
        $this->usuarioModel->insertIntentoAcceso($usuario->id_usuario, exito: true);
        $this->usuarioModel->updateUltimoAcceso($usuario->id_usuario);

        // Guardar la sesión utilizando un helper
        UserSession::login(new AuthenticatedUser(
            id: $usuario->id_usuario,
            id_rol: $usuario->id_rol,
            rol: $usuario->rol,
            nombre: $usuario->nombre_usuario,
            permisos: $usuario->permisos,
            ultimo_acceso: $usuario->ultimo_acceso,
        ));

        $this->logger->info(
            "Usuario {nombre_usuario} ha iniciado sesión",
            ["nombre_usuario" => $usuario->nombre_usuario]
        );

        // Devolver respuesta con la direccion donde deberia redireccionar
        return Response::json([
            "redirect" => "?" . Request::buildQuery(["page" => "dashboard"])
        ]);
    }

    public function logout(): void
    {
        $user = UserSession::get();
        $this->logger->info(
            "Usuario {nombre_usuario} ha cerrado sesión",
            ["nombre_usuario" => $user->nombre]
        );
        UserSession::logout();

        Response::redirect(["page" => "login"]);
        exit;
    }

    /** Mensaje de error generico en caso de ingresar datos incorrectos */
    private function invalidInput(): string
    {
        return Response::json(
            ["message" => "Usuario o contraseña incorrectos"],
            Status::UNAUTHORIZED
        );
    }

    // --- MÓDULO RECUPERACIÓN ---
    public function recover(): string
    {
        $body = Request::getParsedBody();
        $email = $body["email"] ?? null;

        $usuario = $this->usuarioModel->findByEmail($email);
        if (!$usuario) {
            return Response::json(
                ["message" => "Correo no registrado"],
                Status::NOT_FOUND
            );
        }

        // Crear codigo de recuperacion
        $codigo = $this->usuarioModel->createRecoveryCode($usuario->id_usuario);

        // Enviar correo
        $this->mailer->addAddress($email);
        $this->mailer->isHTML(true);
        $this->mailer->Subject = 'Recuperación de cuenta - Sofit Gym';
        $this->mailer->Body = $this->render("recuperacionContrasena", [
            "codigo" => $codigo,
        ]);
        $this->mailer->send();

        return Response::json(["success" => true]);
    }

    public function verify(): string
    {
        $body = Request::getParsedBody();
        $codigo = $body["codigo"] ?? '';

        $usuario = $this->usuarioModel->verifyRecoveryCode($codigo);
        if (!$usuario) {
            return Response::json(
                ["message" => "Código inválido o expirado"],
                Status::UNPROCESSABLE_ENTITY
            );
        }

        $_SESSION['recover_user_id'] = $usuario->id_usuario;
        return Response::json(["success" => true]);
    }

    public function reset(): string
    {
        $body = Request::getParsedBody();
        $new_pass = $body["new_pass"] ?? '';

        if (!isset($_SESSION['recover_user_id'])) {
            return Response::json(
                ["message" => "Sesión expirada"],
                Status::UNAUTHORIZED
            );
        }

        $this->usuarioModel->updatePassword(
            $_SESSION['recover_user_id'],
            $new_pass,
        );

        unset($_SESSION['recover_user_id']);
        return Response::json(["success" => true]);
    }
}
