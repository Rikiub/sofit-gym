<?php

namespace App\Models\Personas;

use App\Helpers\Validator;
use DateTimeImmutable;

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
    ) {
        if ($this->cedula) {
            Validator::cedula($this->cedula, "cedula");
        }
        if ($this->correo) {
            Validator::email($this->correo, "correo");
        }
        if ($this->telefono) {
            Validator::telefono($this->telefono, "telefono");
        }
    }

    public function validateInsert()
    {
        Validator::required($this->cedula, "cedula");
        Validator::required($this->nombre, "nombre");
        Validator::required($this->apellido, "apellido");
    }
}
