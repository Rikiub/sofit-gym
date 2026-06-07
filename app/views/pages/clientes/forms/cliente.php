<?php

/** @var array $formMeta */
?>

<?= $this->fetch("persona/form") ?>

<hr>

<fieldset class="row">
    <label class="col form-label">Tipo de membresia
        <select class="form-select" required name="membresia[id_tipo]">
            <?php foreach ($formMeta['tipos'] as $item): ?>
                <option value="<?= $item['id_tipo'] ?>">
                    <?= $item['nombre'] ?>
                </option>
            <?php endforeach ?>
        </select>
        <small class="form-text" x-text="errors['membresia[id_tipo]']"></small>
    </label>

    <label class="col form-label">Estado de membresia
        <select class="form-select" required name="membresia[id_estado]">
            <?php foreach ($formMeta['estados'] as $item): ?>
                <option value="<?= $item['id_estado'] ?>">
                    <?= $item['nombre'] ?>
                </option>
            <?php endforeach ?>
        </select>
        <small class="form-text" x-text="errors['membresia[id_estado]']"></small>
    </label>
</fieldset>

<fieldset class="row">
    <label class="col form-label">Fecha de inicio de membresía
        <input class="form-control" required name="membresia[fecha_inicio]" type="date" @input.debounce="checkValidity($el)">
        <small class="form-text" x-text="errors['membresia[fecha_inicio]']"></small>
    </label>

    <label class="col form-label">Fecha de fin de membresía
        <input class="form-control" required name="membresia[fecha_fin]" type="date" @input.debounce="checkValidity($el)">
        <small class="form-text" x-text="errors['membresia[fecha_fin]']"></small>
    </label>
</fieldset>