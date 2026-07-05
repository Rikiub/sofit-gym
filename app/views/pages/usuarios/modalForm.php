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
            <?= $this->insert("inputImage", [
                "endpoint" => "?page=usuarios&action=uploadImage",
                "input" => ["name" => "imagen_url"]
            ]) ?>
            <small class="text-danger" x-text="errors.imagen_url"></small>
        </div>
    </div>

    <div class="col">
        <div class="row g-2">
            <label class="col-12 col-md-6">
                <span class="form-label">Nombre de usuario</span>
                <input class="form-control" type="text" name="nombre_usuario" required>
                <small class="text-danger" x-text="errors.nombre_usuario"></small>
            </label>

            <label class="col-12 col-md-6">
                <span class="form-label">Email</span>
                <input class="form-control" type="email" name="email">
                <small class="text-danger" x-text="errors.email"></small>
            </label>

            <?php if ($isAdmin): ?>
                <label class="col-12 col-md-6">
                    <span class="form-label">Rol</span>
                    <select class="form-select" name="id_rol" required>
                        <option value="">Seleccione un rol</option>
                        <option value="1">Administrador</option>
                        <option value="2">Entrenador</option>
                        <option value="3">Recepcionista</option>
                    </select>
                    <small class="text-danger" x-text="errors.id_rol"></small>
                </label>
            <?php endif ?>

            <label class="col-12 col-md-6">
                <span class="form-label">Fecha de creación</span>
                <input class="form-control" type="date" name="fecha_creacion" disabled>
                <small class="form-text" x-text="errors.fecha_creacion"></small>
            </label>

            <template x-if="mode === 'add' || mode === 'edit' && <?= json_encode($isAdmin) ?>">
                <div>
                    <hr>

                    <fieldset x-data="{ 
                        password: '',
                        passwordOne: '',
                        passwordTwo: '',
                        
                        check(input) {
                            if (this.passwordOne !== this.passwordTwo) {
                                this.setInputValidity(this.$refs.password, false, 'Las contraseñas no coinciden')
                            } else {
                                this.password = this.passwordOne
                                this.setInputValidity(this.$refs.password, true)
                            }
                        }
                    }">
                        <input hidden name="contrasena_hash" x-model="password" x-ref="password" required>

                        <label class="col-12">
                            <span class="form-label">Nueva contraseña</span>
                            <input class="form-control" type="password" x-model="passwordOne" @input.debounce="check($el)" required>
                        </label>

                        <label class="col-12">
                            <span class="form-label">Confirmar contraseña</span>
                            <input class="form-control" type="password" x-model="passwordTwo" @input.debounce="check($el)" required>
                        </label>

                        <small class="text-danger" x-text="errors.contrasena_hash"></small>
                    </fieldset>
                </div>
            </template>
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