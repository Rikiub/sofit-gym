<?php
$title = 'Historial de mantenimientos';
$this->layout('layout', ['title' => $title]);
$this->pushJs('pages/equipos/equipos_mantenimiento.js');

$querySelect = $this->fetch("querySelect", [
    "input" => ["name" => "codigo_equipo", "required" => true],
    "columns" => [
        ["name" => "Codigo", "id" => 'codigo'],
        ["name" => "Nombre", "id" => 'nombre'],
    ],
    "params" => [
        "page" => "equipos",
        "action" => "query",
    ],
    "itemKey" => "codigo",
]);

$modalForm = $this->fetch('modalForm', [
    'xData' => 'modalMantenimiento',
    'form' => <<<HTML
        <input name="id_mantenimiento" hidden>

        <fieldset class="row">
            <label class="col">
                <span class="form-label">Equipo</span>
                {$querySelect}
                <small x-text="errors.codigo_equipo"></small>
            </label>

            <label class="col">
                <span class="form-label">Fecha</span>
                <input class="form-control" type="date" name="fecha" required @input.debounce="checkValidity(\$el)">
                <small x-text="errors.fecha"></small>
            </label>
        </fieldset>

        <fieldset class="row">
            <label class="col">
                <span class="form-label">Tipo de Mantenimiento</span>
                <select class="form-select" name="tipo" required @input.debounce="checkValidity(\$el)">
                    <option value="">Seleccione un tipo…</option>
                    <option value="Preventivo">Preventivo</option>
                    <option value="Correctivo">Correctivo</option>
                    <option value="Predictivo">Predictivo</option>
                    <option value="Calibración">Calibración</option>
                </select>
                <small x-text="errors.tipo"></small>
            </label>

            <label class="col">
                <span class="form-label">Costo</span>
                <input
                    class="form-control"
                    name="costo"
                    type="number"
                    step="any"
                    placeholder="0.00"
                    @input.debounce="checkValidity(\$el)"
                >
                <small x-text="errors.costo"></small>
            </label>
        </fieldset>

        <fieldset class="row">
            <label class="col">
                <span class="form-label">Trabajador asignado</span>
                {$this->fetch("select/trabajadores", ["input" => ["name" => "cedula_trabajador"]])}
                <small x-text="errors.cedula_trabajador"></small>
            </label>
        </fieldset>

        <hr>

        <fieldset class="row">
            <label class="col">
                <span class="form-label">Descripción</span>
                <textarea
                    class="form-control"
                    name="descripcion"
                    placeholder="Detalles del mantenimiento realizado"
                    rows="3"
                    @input.debounce="checkValidity(\$el)"
                ></textarea>
                <small x-text="errors.descripcion"></small>
            </label>
        </fieldset>
    HTML
]);
?>

<div x-data="mainData">
    <?= $this->insert('card', [
        'icon' => 'fa-history',
        'title' => $title,
        'body' => <<<HTML
                <main>
                    {$this->fetch('crudTable', ['xData' => 'crudMantenimiento'])}
                </main>
                {$modalForm}
            HTML,
    ]) ?>
</div>