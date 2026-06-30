<?php
$title = 'Inventario de equipos';

$this->layout('layout', ['title' => $title]);
$this->pushJs('pages/equipos/equipos.js');

$modalForm = $this->fetch('modalForm', [
    'xData' => 'modalEquipos',
    'form' => <<<HTML
            <fieldset class="row">
                <label class="col">
                    <span class="form-label">Código</span>
                    <input class="form-control" type="text" name="codigo_equipo" required placeholder="Código del equipo">
                    <small class="text-danger" x-text="errors.codigo_equipo"></small>
                </label>

                <label class="col">
                    <span class="form-label">Nombre</span>
                    <input class="form-control" type="text" name="nombre" required placeholder="Nombre del equipo">
                    <small class="text-danger" x-text="errors.nombre"></small>
                </label>
            </fieldset>

            <fieldset class="row">
                <label class="col">
                    <span class="form-label">Tipo</span>
                    <input class="form-control" type="text" name="tipo" placeholder="Ej. Diagnóstico, Soporte vital">
                    <small class="text-danger" x-text="errors.tipo"></small>
                </label>

                <label class="col">
                    <span class="form-label">Ubicación</span>
                    <input class="form-control" type="text" name="ubicacion" placeholder="Área o sala">
                    <small class="text-danger" x-text="errors.ubicacion"></small>
                </label>
            </fieldset>

            <fieldset class="row">
                <label class="col">
                    <span class="form-label">Estado</span>
                    <select class="form-select" name="estado" required>
                        <option value="">Seleccione un estado…</option>
                        <option value="Operativo">Operativo</option>
                        <option value="Mantenimiento">Mantenimiento</option>
                        <option value="Fuera de Servicio">Fuera de Servicio</option>
                    </select>
                    <small class="text-danger" x-text="errors.estado"></small>
                </label>
            </fieldset>
        HTML,
]);
?>

<?= $this->insert('card', [
    'title' => $title,
    'icon' => 'fa-tools',
    'body' => <<<HTML
            <main>
                {$this->fetch('crudTable', ['xData' => 'crudEquipos'])}
            </main>
            {$modalForm}
        HTML,
]) ?>
