<?php
$this->pushJs('pages/trabajadores/trabajadores.js');
$this->layout('layout', ['title' => 'Trabajadores']);
?>

<?php ob_start() ?>
<?= $this->insert("statsList", [
    "params" => [
        "page" => "trabajadores",
        "action" => "summary",
    ],
    "items" => [
        [
            "mapKey" => "data.total_trabajadores",
            "title" => "Trabajadores Totales",
            "iconClass" => "fa-user",
            "iconContainer" => "bg-warning-subtle text-warning",
        ],
        [
            "mapKey" => "`\$\${data.salario_total_pagado}`",
            "title" => "Gastos en Salario",
            "iconClass" => "fa-money-bill",
            "iconContainer" => "bg-success-subtle text-success",
        ],
    ]
]) ?>

<main>
    <?= $this->insert('crudTable', ['xData' => 'crudTable']) ?>
</main>

<?= $this->insert('modalForm', [
    'xData' => 'modalForm',
    'form' => <<<HTML
            {$this->fetch('persona/form')}

            <hr>

            <fieldset class="row">
                <label class="col">
                    <span class="form-label">Rol</span>
                    <select class="form-select" name="id_rol" required>
                        <option value="">Seleccione un rol</option>
                        <option value="1">Gerente</option>
                        <option value="2">Entrenador</option>
                        <option value="3">Recepcionista</option>
                    </select>
                    <small class="text-danger" x-text="errors.id_rol"></small>
                </label>

                <label class="col">
                    <span class="form-label">Salario</span>
                    <input class="form-control" type="number" name="salario" step="any" required>
                    <small class="text-danger" x-text="errors.salario"></small>
                </label>
            </fieldset>

            <fieldset class="row">
                <label class="col">
                    <span class="form-label">Fecha de contratación</span>
                    <input class="form-control" type="date" name="fecha_contratacion" required>
                    <small class="text-danger" x-text="errors.fecha_contratacion"></small>
                </label>
            </fieldset>
        HTML,
]); ?>
<?php $BODY = ob_get_clean() ?>

<?= $this->insert('card', [
    'title' => 'Trabajadores',
    'body' => $BODY,
]) ?>