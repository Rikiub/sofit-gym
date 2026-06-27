<?php

/** @var \App\Core\Auth\UsuarioSessionDTO $usuario */

$title = "Usuarios";
$this->layout('layout', ['title' => $title]);
$this->pushJs('pages/usuarios/usuarios.js');

$modalForm = $this->fetch("usuarios/modalForm", [
    "id" => "usuarios",
    "isAdmin" => $usuario->hasPermiso("usuarios:ver")
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
