<?php

namespace App\Core\Auth;

enum UserRol: int
{
    case Administrador = 1;
    case Entrenador = 2;
    case Recepcionista = 3;
    case Cliente = 4;
    case Bot = 5;
}
