<fieldset class="row">
    <label class="col">
        <span class="form-label">Fecha de seguimiento</span>
        <input class="form-control" type="date" name="fecha" required>
        <small class="text-danger" x-text="errors.fecha"></small>
    </label>
</fieldset>

<fieldset class="row">
    <label class="col">
        <span class="form-label">Registrado por</span>
        <?= $this->insert("select/trabajadores", [
            "input" => ["name" => "registrado_por"],
        ]) ?>
        <small class="text-danger" x-text="errors.registrado_por"></small>
    </label>
</fieldset>

<hr>

<fieldset class="row">
    <label class="col">
        <span class="form-label">Proteínas (g)</span>
        <input class="form-control" type="number" name="proteinas_g" step="any" min="0" x-mask="999.9" placeholder="0.0">
        <small class="text-danger" x-text="errors.proteinas_g"></small>
    </label>

    <label class="col">
        <span class="form-label">Carbohidratos (g)</span>
        <input class="form-control" type="number" name="carbohidratos_g" step="any" min="0" x-mask="999.9" placeholder="0.0">
        <small class="text-danger" x-text="errors.carbohidratos_g"></small>
    </label>

    <label class="col">
        <span class="form-label">Grasas (g)</span>
        <input class="form-control" type="number" name="grasas_g" step="any" min="0" x-mask="999.9" placeholder="0.0">
        <small class="text-danger" x-text="errors.grasas_g"></small>
    </label>
</fieldset>