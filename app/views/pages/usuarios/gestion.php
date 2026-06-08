<?php
$title = "Usuarios";
$this->layout('layout', ['title' => $title]);
$this->pushJs('pages/usuarios/usuarios.js');

$selectTrabajadores = $this->fetch("select/trabajadores", [
    "input" => [
        "name" => "cedula_persona",
        "required" => true
    ]
]);

$modalForm = $this->fetch('modalForm', [
    'xData' => 'modalFormUsuarios',
    'form' => $this->fetch("usuarios/form"),
]);
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
