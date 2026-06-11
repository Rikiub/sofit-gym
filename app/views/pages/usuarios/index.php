<?php

/** @var \App\Helpers\Auth\UsuarioSessionDTO $usuario */

use App\Helpers\Auth\Rol;

$title = "Usuarios";
$this->layout('layout', ['title' => $title]);
$this->pushJs('pages/usuarios/usuarios.js');

$modalForm = $this->fetch("usuarios/modalForm", [
    "id" => "usuarios",
    "isAdmin" => $usuario->rol === Rol::Administrador
])
?>

<?= $this->insert('card', [
    'title' => $title,
    'body' => <<<HTML
            <main>
                {$this->fetch('crudTable', ['xData' => 'crudTableUsuarios'])}
            </main>
            
            {$modalForm}
        HTML
]) ?>
