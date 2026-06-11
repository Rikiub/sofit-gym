<?php
$title = "Horarios de Clases";

$this->layout("layout", ["title" => $title]);
$this->pushJs("pages/clases/clases.js");

$selectTrabajadores = $this->fetch("select/trabajadores", [
    "input" => [
        "name" => "cedula_trabajador",
        "required" => true,
    ],
    "id_rol" => 2,
]);
$selectClientes = $this->fetch("select/clientes");

$modalForm = $this->fetch('modalForm', [
    'xData' => 'modalForm',
    'form' => <<<HTML
            <input hidden name="id_clase">

            <fieldset class="row">
                <label class="col">
                    <span class="form-label">Nombre de la clase</span>
                    <input
                        class="form-control"
                        name="nombre"
                        type="text" 
                        required
                    >
                    <small class="text-danger" x-text="errors.nombre"></small>
                </label>
            </fieldset>

            <fieldset class="row">
                <label class="col">
                    <span class="form-label">Descripción</span>
                    <textarea
                        class="form-control"
                        name="descripcion"
                        rows="2" 
                    ></textarea>
                    <small class="text-danger" x-text="errors.descripcion"></small>
                </label>
            </fieldset>

            <hr>

            <fieldset class="row">
                <label class="col">
                    <span class="form-label">Entrenador</span>
                    {$selectTrabajadores}
                    <small class="text-danger" x-text="errors.cedula_trabajador"></small>
                </label>
            </fieldset>

            <hr>

            <div
                x-data="listaClientes"
                @form-reset.window="reset()"
                @form-load.window="load(\$event.detail)"
                @form-validate.window="validate(\$event.detail)"
                @form-serialize.window="serialize(\$event.detail)"
            >
                <fieldset class="row">
                    <label class="col">
                        <span class="form-label">Capacidad Actual</span>
                        <input class="form-control" name="capacidad_actual" :value="capacidad_actual" placeholder="0" readonly>
                    </label>

                    <label class="col">
                        <span class="form-label">Capacidad Maxima</span>
                        <input class="form-control" name="capacidad_maxima" required type="number" placeholder="1" min="1">
                        <small class="text-danger" x-text="errors.capacidad_maxima"></small>
                    </label>
                </fieldset>

                <div>
                    <span class="form-label">Lista de Clientes</span>

                    <div @item-selected="handleItemSelected(\$event.detail)">
                        {$selectClientes}
                    </div>

                    <ul class="list-group">
                        <template x-for="(cliente, index) in clientes" :key="cliente.cedula">
                            <li class="list-group-item d-flex justify-content-between align-items-center view-fade-in">
                                <div>
                                    <span class="badge bg-secondary me-2" x-text="cliente.cedula"></span>
                                    <span class="text-dark" x-text='`\${cliente.nombre} \${cliente.apellido}`'></span>
                                </div>
                                
                                <button type="button" class="btn btn-sm btn-danger border-0" @click="remove(index)">
                                    Eliminar
                                </button>
                            </li>
                        </template>

                        <template x-if="clientes.length === 0">
                            <li class="list-group-item text-muted text-center py-3 bg-light">
                                No hay clientes asignados a esta clase todavía.
                            </li>
                        </template>
                    </ul>

                    <small class="text-danger" x-text="errors.clientes"></small>
                </div>
            </div>

            <hr>

            <fieldset class="row">
                <label class="col">
                    <span class="form-label">Fecha y hora de inicio</span>
                    <input
                        class="form-control"
                        type="datetime-local" 
                        name="fecha_inicio"
                        required
                    >
                    <small class="text-danger" x-text="errors.fecha_inicio"></small>
                </label>

                <label class="col">
                    <span class="form-label">Fecha y hora de fin</span>
                    <input
                        class="form-control"
                        name="fecha_fin"
                        type="datetime-local" 
                        required
                    >
                    <small class="text-danger" x-text="errors.fecha_fin"></small>
                </label>
            </fieldset>

            <fieldset class="row">
                <label class="col">
                    <span class="form-label">Estado</span>
                    <select class="form-control" name="estado" required>
                        <option value="Programado">Programado</option>
                        <option value="En curso">En curso</option>
                        <option value="Finalizado">Finalizado</option>
                        <option value="Cancelado">Cancelado</option>
                    </select>
                    <small class="text-danger" x-text="errors.estado"></small>
                </label>
            </fieldset>
        HTML,
]);
?>

<?= $this->insert('card', [
    "class" => "main",
    "icon" => "fa-calendar",
    'title' => $title,
    'body' => <<<HTML
        <main class="px-1 mb-5">
            {$this->fetch("calendar", ["xData" => "calendarClases"])}
        </main>
        
        {$modalForm}
    HTML
]) ?>

<style>
    .main {
        max-width: 1000px;
        margin: auto;
    }
</style>