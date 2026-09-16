<?php

namespace App\Controllers;

use App\Core\Route;
use App\Services\Auth\UserSession;
use App\Services\Auth\CurrentUser;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Http\Status;
use App\Core\Tools;
use App\Models\UsuarioModel;
use App\Models\BitacoraModel;
use App\Models\Level;
use PHPMailer\PHPMailer\PHPMailer;
use DateTimeImmutable;

$logger = new BitacoraModel();
$usuarioModel = new UsuarioModel();
$mailer = Tools::getMailer();

/** Mensaje de error generico en caso de ingresar datos incorrectos */
function invalidInput(): string
{
    return Response::json(
        ["message" => "Usuario o contraseña incorrectos"],
        Status::UNAUTHORIZED
    );
}

switch (Route::action()) {
    case "index":
        if (UserSession::get()) {
            // Si el usuario ya inicio sesión, redirigir a pagina de inicio.
            Response::redirect(["page" => "dashboard"]);
        }

        return Route::render("login");

    case "login":
        $body = Request::getParsedBody();
        $nombre_usuario = $body["nombre_usuario"] ?? null;
        $contrasena = $body["contrasena"] ?? null;
        $direccion_ip = $_SERVER['REMOTE_ADDR'];

        # Comprobar intentos
        $maximoIntentos = 3;
        $minutosBloqueo = 1;
        $duracion = new DateTimeImmutable("-{$minutosBloqueo} minutes");

        if ($usuarioModel->intentosFallidos(
            duracion: $duracion,
            direccion_ip: $direccion_ip
        ) >= $maximoIntentos) {
            return Response::json(
                ["message" => "Numero de intentos excedidos. Vuelva a intentarlo en {$minutosBloqueo} minutos."],
                Status::UNAUTHORIZED
            );
        }

        # Validar y registrar intento
        $usuario = $usuarioModel->findByUsername($nombre_usuario);
        if (!$usuario) {
            $usuarioModel->insertIntentoAcceso(exito: false, direccion_ip: $direccion_ip);
            return invalidInput();
        };

        if (!password_verify($contrasena, $usuario->contrasena_hash)) {
            $logger->log(
                "Usuario {nombre_usuario} ha fallado al iniciar sesión",
                [
                    "modulo" => "login",
                    "accion" => "iniciar_sesion",
                    "nombre_usuario" => $usuario->nombre_usuario,
                ],
                nivel: Level::ERROR,
            );

            $usuarioModel->insertIntentoAcceso(
                exito: false,
                direccion_ip: $direccion_ip,
                id_usuario: $usuario->id_usuario
            );

            return invalidInput();
        }

        // Actualizar estado
        $usuarioModel->insertIntentoAcceso(
            direccion_ip: $direccion_ip,
            id_usuario: $usuario->id_usuario,
            exito: true
        );
        $usuarioModel->updateUltimoAcceso($usuario->id_usuario);

        // Guardar la sesión utilizando un helper
        UserSession::login(new CurrentUser(
            id: $usuario->id_usuario,
            id_rol: $usuario->id_rol,
            rol: $usuario->rol,
            nombre: $usuario->nombre_usuario,
            permisos: $usuario->permisos,
            ultimo_acceso: $usuario->ultimo_acceso,
        ));

        $logger->log(
            "Usuario {nombre_usuario} ha iniciado sesión",
            [
                "modulo" => "login",
                "accion" => "iniciar_sesion",
                "nombre_usuario" => $usuario->nombre_usuario
            ]
        );

        // Devolver respuesta con la direccion donde deberia redireccionar
        return Response::json([
            "redirect" => "?" . Request::buildQuery(["page" => "dashboard"])
        ]);

    case "logout":
        $user = UserSession::get();
        $logger->log(
            "Usuario {nombre_usuario} ha cerrado sesión",
            [
                "modulo" => "login",
                "accion" => "cerrar_sesion",
                "nombre_usuario" => $user->nombre
            ]
        );
        UserSession::logout();

        Response::redirect(["page" => "login"]);
        exit;

        // --- MÓDULO RECUPERACIÓN ---
    case "recover":
        $body = Request::getParsedBody();
        $email = $body["email"] ?? null;

        $usuario = $usuarioModel->findByEmail($email);
        if (!$usuario) {
            return Response::json(
                ["message" => "Correo no registrado"],
                Status::NOT_FOUND
            );
        }

        // Crear codigo de recuperacion
        $codigo = $usuarioModel->createRecoveryCode($usuario->id_usuario);

        // Enviar correo
        $mailer->addAddress($email);
        $mailer->isHTML(true);
        $mailer->Subject = 'Recuperación de cuenta - Sofit Gym';
        $mailer->Body = Route::render("recuperacionContrasena", [
            "codigo" => $codigo,
        ]);
        $mailer->send();

        return Response::json(["success" => true]);

    case "verify":
        $body = Request::getParsedBody();
        $codigo = $body["codigo"] ?? '';

        $usuario = $usuarioModel->verifyRecoveryCode($codigo);
        if (!$usuario) {
            return Response::json(
                ["message" => "Código inválido o expirado"],
                Status::UNPROCESSABLE_ENTITY
            );
        }

        $_SESSION['recover_user_id'] = $usuario->id_usuario;
        return Response::json(["success" => true]);

    case "reset":
        $body = Request::getParsedBody();
        $new_pass = $body["new_pass"] ?? '';

        if (!isset($_SESSION['recover_user_id'])) {
            return Response::json(
                ["message" => "Sesión expirada"],
                Status::UNAUTHORIZED
            );
        }

        $usuarioModel->updatePassword(
            $_SESSION['recover_user_id'],
            $new_pass,
        );

        unset($_SESSION['recover_user_id']);
        return Response::json(["success" => true]);
}
