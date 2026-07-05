<?php

namespace App\Services\Auth;

use DateTimeImmutable;

/** Datos del usuario actual. */
readonly class AuthenticatedUser
{
    public function __construct(
        public int $id,
        public int $id_rol,
        public string $rol,
        public string $nombre,
        public array $permisos,
        public DateTimeImmutable|null $ultimo_acceso = null,
    ) {}

    public function hasPermiso(string $permiso): bool
    {
        return in_array($permiso, $this->permisos);
    }
}
