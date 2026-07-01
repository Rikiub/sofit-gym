<?php
$this->layout('layout', ['title' => 'Clientes']);
$this->pushJs('pages/clientes/clientes.js');
?>

<?php ob_start() ?>
<?= $this->insert("statsList", [
    "params" => [
        "page" => "clientes",
        "action" => "summary",
    ],
    "items" => [
        [
            "mapKey" => "data.total_clientes",
            "title" => "Clientes Totales",
            "iconClass" => "fa-user",
            "iconContainer" => "bg-warning-subtle text-warning",
        ],
        [
            "mapKey" => "data.membresias_activas",
            "title" => "Membresias Activas",
            "iconClass" => "fa-check",
            "iconContainer" => "bg-primary-subtle text-primary",
        ],
        [
            "mapKey" => "`\$\${data?.ganancias_totales}`",
            "title" => "Ganancias Mensuales",
            "iconClass" => "fa-money-bill",
            "iconContainer" => "bg-success-subtle text-success",
        ]
    ]
]) ?>

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