<?php
$title = "Usuarios";
$cedula ??= null;

$this->layout('layout', ['title' => $title]);
$this->pushJs('pages/usuarios/usuarios.js');
?>

<?php ob_start() ?>
<?= $this->fetch("persona/info", [
    "type" => "trabajadores",
    "cedula" => $cedula,
]) ?>
<?php $BODY = ob_get_clean() ?>

<?= $this->insert('card', [
    "icon" => "fa-info-circle",
    "title" => "Información",
    "header_right" => <<<HTML
        <a href="?page=usuarios" class="btn btn-primary">
            <i class="fa-solid fa-arrow-left"></i>
            Volver
        </a>
    HTML,
    "body" => $BODY,
]) ?>