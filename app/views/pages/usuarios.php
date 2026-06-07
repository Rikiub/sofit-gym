<?php
$title = "Usuarios";
$this->layout('layout', ['title' => $title]);
$this->pushJs('pages/usuarios/usuarios.js');

$modalForm = $this->fetch('modalForm', [
    'xData' => 'modalFormUsuarios',
    'form' => <<<HTML
            <input name="id_usuario" hidden>
            
            <fieldset class="row">
                <label class="col">
                    <span class="form-label">Nombre de usuario
                    <input class="form-control" type="text" name="nombre_usuario" required @input.debounce="checkValidity(\$el)">
                    <small class="form-text" x-text="errors.nombre_usuario"></small>
                </label>

                <label class="col form-label">Rol
                    <select class="form-select" name="id_rol" required @input.debounce="checkValidity(\$el)">
                        <option value="">Seleccione un rol</option>
                        <option value="1">Administrador</option>
                        <option value="2">Entrenador</option>
                        <option value="3">Recepcionista</option>
                    </select>
                    <small class="form-text" x-text="errors.id_rol"></small>
                </label>
            </fieldset>

            <fieldset class="row">
                <label class="col">
                    <span class="form-label">Fecha de registro
                    <input class="form-control" type="date" name="fecha_registro" required @input.debounce="checkValidity(\$el)">
                    <small class="form-text" x-text="errors.fecha_registro"></small>
                </label>
            </fieldset>
        HTML,
]);
?>

<?= $this->insert('card', [
    'title' => $title,
    'body' => <<<HTML
            <main>
                {$this->fetch('crudTable', ['xData' => 'crudTableUsuarios'])}
            </main>
            
            {$modalForm}
        HTML
]) ?>
