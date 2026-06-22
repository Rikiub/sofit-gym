<?php

namespace App\Helpers\Auth;

/** Helper para manejar sesiones de usuario de forma segura */
class UsuarioSession
{
    private const SESSION_KEY = 'usuario';

    public static function login(UsuarioSessionDto $usuario): void
    {
        session_regenerate_id();
        $_SESSION[self::SESSION_KEY] = $usuario;
    }

    public static function getUsuario(): ?UsuarioSessionDto
    {
        return $_SESSION[self::SESSION_KEY] ?? null;
    }

    public static function logout(): void
    {
        unset($_SESSION[self::SESSION_KEY]);
    }
}
