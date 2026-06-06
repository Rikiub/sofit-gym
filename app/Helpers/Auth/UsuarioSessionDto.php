<?php

namespace App\Helpers\Auth;

/** Datos accesibles del usuario que ha iniciado sesión */
readonly class UsuarioSessionDto
{
    public function __construct(
        public int $id,
        public Rol $rol,
        public string $nombre,
    ) {}
}
