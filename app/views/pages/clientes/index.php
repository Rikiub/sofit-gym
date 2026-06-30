<?php
$this->layout('layout', ['title' => 'Clientes']);
$this->pushJs('pages/clientes/clientes.js');
?>

<?php ob_start() ?>
<?= $this->insert("statsList") ?>

<main>
    <?= $this->fetch('crudTable', ['xData' => 'crudClientes']) ?>
</main>

<?= $this->fetch('modalForm', [
    'xData' => 'modalClientes',
    'form' => $this->fetch("persona/form"),
]) ?>
<?php $BODY = ob_get_clean() ?>

<?= $this->insert('card', [
    'icon' => "fa-id-card",
    'title' => 'Clientes',
    'body' => $BODY,
]) ?>