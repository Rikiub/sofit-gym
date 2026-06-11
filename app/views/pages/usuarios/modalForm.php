<?php
$id ??= null;
$isAdmin ??= false;
?>

<?php ob_start() ?>
<input name="id_usuario" hidden>

<div class="row g-4 align-items-start">
    <div class="col-auto">
        <span class="form-label d-block mb-2">Foto</span>
        <div class="d-flex flex-column">
            <?= $this->insert("inputImage", ["input" => ["name" => "imagen_url"]]) ?>
            <small class="text-danger mt-1 d-block" x-text="errors.imagen_url"></small>
        </div>
    </div>

    <div class="col">
        <div class="row g-2">
            <label class="col-12 col-md-6">
                <span class="form-label">Nombre de usuario</span>
                <input class="form-control" type="text" name="nombre_usuario" required>
                <small class="text-danger mt-1 d-block" x-text="errors.nombre_usuario"></small>
            </label>

            <label class="col-12 col-md-6">
                <span class="form-label">Email</span>
                <input class="form-control" type="email" name="email">
                <small class="text-danger mt-1 d-block" x-text="errors.email"></small>
            </label>

            <template x-if="mode !== 'edit'">
                <label class="col-12 col-md-6">
                    <span class="form-label">Contraseña</span>
                    <input class="form-control" type="text" name="contrasena_hash" required>
                    <small class="text-danger mt-1 d-block" x-text="errors.contrasena_hash"></small>
                </label>
            </template>

            <?php if ($isAdmin): ?>
                <label class="col-12 col-md-6">
                    <span class="form-label">Rol</span>
                    <select class="form-select" name="id_rol" required>
                        <option value="">Seleccione un rol</option>
                        <option value="1">Administrador</option>
                        <option value="2">Entrenador</option>
                        <option value="3">Recepcionista</option>
                    </select>
                    <small class="text-danger mt-1 d-block" x-text="errors.id_rol"></small>
                </label>
            <?php endif ?>

            <label class="col-12 col-md-6">
                <span class="form-label">Fecha de creación</span>
                <input class="form-control" type="date" name="fecha_creacion" disabled>
                <small class="form-text" x-text="errors.fecha_creacion"></small>
            </label>
        </div>
    </div>
</div>
<?php $FORM = ob_get_clean() ?>

<?= $this->insert('modalForm', [
    'xData' => <<<JS
        modalForm({
            id: "{$id}",
            page: "usuarios",
            elementName: "Usuario",
            prepareAddData: {
                fecha_creacion: new Date(),
            },
            editDisableFields: ["nombre_usuario"],
        })
    JS,
    'form' => $FORM,
]);
?>