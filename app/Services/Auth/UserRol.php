<?php

namespace App\Services\Auth;

/** ID que corresponde a un rol de usuario en la base de datos. */
enum UserRol: int
{
    case Administrador = 1;
    case Entrenador = 2;
    case Recepcionista = 3;
}
