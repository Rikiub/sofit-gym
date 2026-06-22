<?php

namespace App\Helpers\Auth;

use DateTimeImmutable;

/** Datos accesibles del usuario que ha iniciado sesión */
readonly class UsuarioSessionDto
{
    public function __construct(
        public int $id,
        public Rol $rol,
        public string $nombre,
        public array $permisos,
        public DateTimeImmutable|null $ultimo_acceso = null,
    ) {}

    public function hasPermiso(string $permiso): bool
    {
        return in_array($permiso, $this->permisos);
    }
}
