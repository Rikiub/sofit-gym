<?php
// Props
$type ??= "";
$cedula ??= "";
$id ??= "";
$elementName ??= "";
?>

<script type="module">
    import {
        fetchApi
    } from "@/js/api.js";
    import Alpine from "alpinejs";
    import dayjs from "dayjs";

    Alpine.data("personaInfo", () => ({
        id: "<?= $id ?>",
        persona: {},

        async init() {
            await this.refresh();
        },

        async handleFormSuccess({
            id
        }) {
            if (id === this.id) {
                await this.refresh();
            }
        },

        async refresh() {
            this.persona = await fetchApi({
                page: "<?= $type ?>",
                action: "find",
                id: "<?= $cedula ?>",
            });
        },

        nombreCompleto() {
            const isEmpty = Object.keys(this.persona).length === 0;

            if (isEmpty) return "Cargando...";
            return `${this.persona.nombre} ${this.persona.apellido}`;
        },

        setText(value) {
            return value ?? "Desconocido";
        },

        onlyDate(value) {
            if (!value) return value;
            return dayjs(value).format("DD/MM/YYYY");
        }
    }));
</script>

<article
    x-data="personaInfo"
    @form-success.window="handleFormSuccess($event.detail)">

    <header class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center gap-3">
            <h3 class="mb-0" x-text="nombreCompleto"></h3>
        </div>

        <div class="d-flex gap-2">
            <button
                class="btn btn-warning"
                title="Editar"
                data-bs-toggle="tooltip"
                data-bs-placement="bottom"
                @click="$dispatch('open-modal', { mode: 'edit', dataId: persona.cedula, id: '<?= $id ?>' })">
                <i class="fa-solid fa-pen-to-square"></i>
            </button>

            <button
                class="btn btn-danger"
                title="Eliminar"
                data-bs-toggle="tooltip"
                data-bs-placement="bottom"
                @click="$dispatch('open-modal', { mode: 'delete', dataId: persona.cedula, id: '<?= $id ?>' })">
                <i class="fa-solid fa-trash-can"></i>
            </button>
        </div>
    </header>

    <div class="card shadow-sm">
        <header class="card-header">
            <h3 class="card-title mb-0">Información</h3>
        </header>

        <div class="card-body">
            <div class="row mb-3">
                <div class="col-6">
                    <h5>Cédula</h5>
                    <p x-text="setText(persona.cedula)"></p>
                </div>
                <div class="col-6">
                    <h5>Telefono</h5>
                    <p x-text="setText(persona.telefono)"></p>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-6">
                    <h5>Correo</h5>
                    <p x-text="setText(persona.correo)"></p>
                </div>

                <div class="col-6">
                    <h5>Dirección</h5>
                    <p x-text="setText(persona.direccion)"></p>
                </div>
            </div>

            <div class="row">
                <div class="col-6">
                    <h5>Fecha de nacimiento</h5>
                    <p x-text="setText(onlyDate(persona.fecha_nacimiento))"></p>
                </div>

                <div class="col-6">
                    <h5>Fecha de registro</h5>
                    <p x-text="setText(onlyDate(persona.fecha_registro))"></p>
                </div>
            </div>
        </div>
    </div>
</article>

<?= $this->insert('modalForm', [
    'xData' => <<<JS
            modalForm({
                id: "{$id}",
                page: "{$type}",
                elementName: "{$elementName}",
                editDisableFields: ["cedula"],
                afterSubmit: (mode) => {
                    if (mode === "delete") {
                        location.href = `?page=inicio`;
                        return;
                    }  
                },
            })
        JS,
    'form' => $this->fetch('persona/form'),
]) ?>