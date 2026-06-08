<?php

namespace App\Models\Clientes;

use App\Models\Personas\PersonaDTO;
use DateTimeImmutable;

readonly class ClienteDTO extends PersonaDTO
{
    public function __construct(
        // Atributos heredados
        ?string $cedula = null,
        ?string $nombre = null,
        ?string $apellido = null,
        ?string $correo = null,
        ?string $telefono = null,
        ?string $direccion = null,
        ?bool $activo = true,
        ?DateTimeImmutable $fecha_nacimiento = null,
        ?DateTimeImmutable $fecha_registro = new DateTimeImmutable(),
    ) {
        parent::__construct(
            cedula: $cedula,
            nombre: $nombre,
            apellido: $apellido,
            correo: $correo,
            telefono: $telefono,
            direccion: $direccion,
            activo: $activo,
            fecha_nacimiento: $fecha_nacimiento,
            fecha_registro: $fecha_registro,
        );
    }
}
