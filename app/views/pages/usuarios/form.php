<input name="id_usuario" hidden>

<div class="row g-4 align-items-start">
    <div class="col-auto">
        <span class="form-label d-block mb-2">Foto</span>
        <div class="d-flex flex-column">
            <?= $this->insert("inputFile", ["input" => ["name" => "imagen_url"]]) ?>
            <small class="text-danger mt-1 d-block" x-text="errors.imagen_url"></small>
        </div>
    </div>

    <div class="col">
        <fieldset class="row g-2">
            <div class="col-12 col-md-6">
                <label class="form-label" for="nombre_usuario">Nombre de usuario</label>
                <input id="nombre_usuario" class="form-control" type="text" name="nombre_usuario" required @input.debounce="checkValidity($el)">
                <small class="text-danger mt-1 d-block" x-text="errors.nombre_usuario"></small>
            </div>

            <div class="col-12 col-md-6">
                <label class="form-label" for="id_rol">Rol</label>
                <select id="id_rol" class="form-select" name="id_rol" required @input.debounce="checkValidity($el)">
                    <option value="">Seleccione un rol</option>
                    <option value="1">Administrador</option>
                    <option value="2">Entrenador</option>
                    <option value="3">Recepcionista</option>
                </select>
                <small class="text-danger mt-1 d-block" x-text="errors.id_rol"></small>
            </div>

            <div class="col-12 col-md-6">
                <label class="form-label">Fecha de creación</label>
                <input class="form-control" type="date" name="fecha_creacion" disabled @input.debounce="checkValidity(\$el)">
                <small class="form-text" x-text="errors.fecha_creacion"></small>
            </div>
        </fieldset>
    </div>
</div>