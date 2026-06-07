<?php
$this->pushJs('pages/trabajadores/trabajadores.js');
$this->layout('layout', ['title' => 'Trabajadores']);

$modalForm = $this->fetch('modalForm', [
    'xData' => 'modalForm',
    'form' => <<<HTML
            {$this->fetch('persona/form')}

            <hr>

            <fieldset class="row">
                <label class="col form-label">Rol
                    <select class="form-select" name="id_rol" required @input.debounce="checkValidity(\$el)">
                        <option value="">Seleccione un rol</option>
                        <option value="1">Administrador</option>
                        <option value="2">Entrenador</option>
                        <option value="3">Recepcionista</option>
                    </select>
                    <small class="form-text" x-text="errors.id_rol"></small>
                </label>

                <label class="col form-label">Salario
                    <input class="form-control" type="number" name="salario" step="any" required @input.debounce="checkValidity(\$el)">
                    <small class="form-text" x-text="errors.salario"></small>
                </label>
            </fieldset>

            <fieldset class="row">
                <label class="col form-label">Fecha de contratación
                    <input class="form-control" type="date" name="fecha_contratacion" required @input.debounce="checkValidity(\$el)">
                    <small class="form-text" x-text="errors.fecha_contratacion"></small>
                </label>
            </fieldset>
        HTML,
]);
?>

<?= $this->insert('card', [
    'title' => 'Trabajadores',
    'body' => <<<HTML
            <main>
                {$this->fetch('crudTable', ['xData' => 'crudTable'])}
            </main>
            
            {$modalForm}
        HTML
]) ?>
