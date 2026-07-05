<?php

namespace App\Models\Clientes;

use App\Models\Personas\Persona;
use DateTimeImmutable;

readonly class Cliente extends Persona
{
    public function __construct(
        // Atributos heredados
        ?string $cedula = null,
        ?string $nombre = null,
        ?string $apellido = null,
        ?string $nombre_completo = null,
        ?string $correo = null,
        ?string $telefono = null,
        ?string $direccion = null,
        ?bool $activo = true,
        ?DateTimeImmutable $fecha_nacimiento = null,
        ?DateTimeImmutable $fecha_creacion = null,
        // Atributos nuevos
        public ?Membresia $membresia = null,
    ) {
        parent::__construct(
            cedula: $cedula,
            nombre: $nombre,
            apellido: $apellido,
            nombre_completo: $nombre_completo,
            correo: $correo,
            telefono: $telefono,
            direccion: $direccion,
            activo: $activo,
            fecha_nacimiento: $fecha_nacimiento,
            fecha_creacion: $fecha_creacion,
        );
    }
}

readonly class Membresia
{
    public function __construct(
        public ?int $id_membresia = null,
        public ?int $id_tipo = null,
        public ?int $id_estado = null,
        public ?string $tipo = null,
        public ?string $estado = null,
        public ?DateTimeImmutable $fecha_inicio = null,
        public ?DateTimeImmutable $fecha_fin = null,
    ) {}
}
