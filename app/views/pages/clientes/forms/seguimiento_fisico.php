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
        <span class="form-label">Altura (cm)</span>
        <input class="form-control" type="number" name="altura_cm" step="any" min="100" max="230" x-mask="999" placeholder="000">
        <small class="text-danger" x-text="errors.altura_cm"></small>
    </label>

    <label class="col">
        <span class="form-label">Peso (kg)</span>
        <input class="form-control" type="number" name="peso_kg" step="any" x-mask="99.9" placeholder="0.0">
        <small class="text-danger" x-text="errors.peso_kg"></small>
    </label>
</fieldset>

<fieldset class="row">
    <label class="col">
        <span class="form-label">Cintura (cm)</span>
        <input class="form-control" type="number" name="cintura_cm" step="any" x-mask="99.9" placeholder="0.0">
        <small class="text-danger" x-text="errors.cintura_cm"></small>
    </label>

    <label class="col">
        <span class="form-label">Cadera (cm)</span>
        <input class="form-control" type="number" name="cadera_cm" step="any" x-mask="99.9" placeholder="0.0">
        <small class="text-danger" x-text="errors.cadera_cm"></small>
    </label>
</fieldset>

<fieldset class="row">
    <label class="col">
        <span class="form-label">Pecho (cm)</span>
        <input class="form-control" type="number" name="pecho_cm" step="any" x-mask="99.9" placeholder="0.0">
        <small class="text-danger" x-text="errors.pecho_cm"></small>
    </label>

    <label class="col">
        <span class="form-label">Muslo (cm)</span>
        <input class="form-control" type="number" name="muslo_cm" step="any" x-mask="99.9" placeholder="0.0">
        <small class="text-danger" x-text="errors.muslo_cm"></small>
    </label>
</fieldset>

<fieldset class="row">
    <label class="col">
        <span class="form-label">Hombros (cm)</span>
        <input class="form-control" type="number" name="hombros_cm" step="any" x-mask="99.9" placeholder="0.0">
        <small class="text-danger" x-text="errors.hombros_cm"></small>
    </label>

    <label class="col">
        <span class="form-label">Pantorrilla (cm)</span>
        <input class="form-control" type="number" name="pantorrilla_cm" step="any" x-mask="99.9" placeholder="0.0">
        <small class="text-danger" x-text="errors.pantorrilla_cm"></small>
    </label>
</fieldset>