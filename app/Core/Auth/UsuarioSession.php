<?php

namespace App\Core\Auth;

/** Helper para manejar sesiones de usuario de forma segura */
class UsuarioSession
{
    private const SESSION_KEY = 'usuario';

    /** Obtener usuario actual */
    public static function getCurrent(): ?UsuarioSessionDto
    {
        return $_SESSION[self::SESSION_KEY] ?? null;
    }

    /** Iniciar sesion */
    public static function login(UsuarioSessionDto $usuario): void
    {
        session_regenerate_id();
        $_SESSION[self::SESSION_KEY] = $usuario;
    }

    /** Cerrar sesion */
    public static function logout(): void
    {
        unset($_SESSION[self::SESSION_KEY]);
    }
}
