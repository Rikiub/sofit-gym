<?php

namespace App\Helpers\Auth;

/** Roles del sistema.
 * Debe coincidir con los id_rol de la base de datos.
 */
enum Rol: int
{
    case Administrador = 1;
    case Entrenador = 2;
    case Recepcionista = 3;
}
