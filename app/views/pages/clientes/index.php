<?php
$this->layout('layout', ['title' => 'Clientes']);
$this->pushJs('pages/clientes/clientes.js');

$modalForm = $this->fetch('modalForm', [
    'xData' => 'modalClientes',
    'form' => $this->fetch("persona/form"),
]);
?>

<?= $this->insert('card', [
    'icon' => "fa-id-card",
    'title' => 'Clientes',
    'body' => <<<HTML
            <main>
                {$this->fetch('crudTable', ['xData' => 'crudClientes'])}
            </main>
            {$modalForm}
        HTML,
]) ?>