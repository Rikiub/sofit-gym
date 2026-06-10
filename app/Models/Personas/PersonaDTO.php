<?php

namespace App\Models\Personas;

use DateTimeImmutable;
use InvalidArgumentException;

/**
 * Base para compartir tipos y validaciones con las clases: ClienteDTO y TrabajadorDTO.
 */
readonly class PersonaDTO
{
    public function __construct(
        public ?string $cedula = null,
        public ?string $nombre = null,
        public ?string $apellido = null,
        public ?string $correo = null,
        public ?string $telefono = null,
        public ?string $direccion = null,
        public ?bool $activo = true,
        public ?DateTimeImmutable $fecha_nacimiento = null,
        public ?DateTimeImmutable $fecha_creacion = null,
    ) {}

    public function validateInsert()
    {
        if (!$this->cedula) {
            throw new InvalidArgumentException('Debe tener una cedula');
        }
        if (!$this->nombre || !$this->apellido) {
            throw new InvalidArgumentException('Debe tener nombre y apellido');
        }
    }
    public function validateUpdate() {}
}
