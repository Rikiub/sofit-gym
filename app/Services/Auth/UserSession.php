<?php

namespace App\Services\Auth;

/** Manejador de la sesión actual del usuario. */
class UserSession
{
    private const SESSION_KEY = 'user';

    /** Obtener usuario actual */
    public static function get(): ?CurrentUser
    {
        return $_SESSION[self::SESSION_KEY] ?? null;
    }

    /** Iniciar sesion */
    public static function login(CurrentUser $usuario): void
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
