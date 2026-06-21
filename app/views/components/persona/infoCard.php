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

    <div class="card h-100 border border-light-subtle bg-body-tertiary rounded-3 shadow-none">
        <header class="card-header bg-transparent border-0 pt-3 pb-0 d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <i class="fa-solid fa-circle-user text-primary me-2 fs-5"></i>
                <h5 class="card-title mb-0 fw-bold fs-5" x-text="nombreCompleto"></h5>
            </div>

            <div class="d-flex gap-1">
                <button
                    class="btn btn-sm btn-warning"
                    title="Editar"
                    data-bs-toggle="tooltip"
                    data-bs-placement="bottom"
                    @click="$dispatch('open-modal', { mode: 'edit', dataId: persona.cedula, id: '<?= $id ?>' })">
                    <i class="fa-solid fa-pen-to-square"></i>
                </button>

                <button
                    class="btn btn-sm btn-danger"
                    title="Eliminar"
                    data-bs-toggle="tooltip"
                    data-bs-placement="bottom"
                    @click="$dispatch('open-modal', { mode: 'delete', dataId: persona.cedula, id: '<?= $id ?>' })">
                    <i class="fa-solid fa-trash-can"></i>
                </button>
            </div>
        </header>

        <div class="card-body p-4">
            <div class="row g-3">
                <div class="col-6 col-md-4">
                    <div class="d-flex align-items-center text-muted small text-uppercase fw-bold mb-1">
                        <i class="fa-solid fa-id-card me-1"></i>
                        <span>Cédula</span>
                    </div>
                    <span class="fs-6 fw-semibold" x-text="setText(persona.cedula)"></span>
                </div>

                <div class="col-6 col-md-4">
                    <div class="d-flex align-items-center text-muted small text-uppercase fw-bold mb-1">
                        <i class="fa-solid fa-phone me-1"></i>
                        <span>Teléfono</span>
                    </div>
                    <span class="fs-6 fw-semibold" x-text="setText(persona.telefono)"></span>
                </div>

                <div class="col-12 col-md-4">
                    <div class="d-flex align-items-center text-muted small text-uppercase fw-bold mb-1">
                        <i class="fa-solid fa-envelope me-1"></i>
                        <span>Correo</span>
                    </div>
                    <span class="fs-6 fw-semibold" x-text="setText(persona.correo)"></span>
                </div>

                <div class="col-12 col-md-4">
                    <div class="d-flex align-items-center text-muted small text-uppercase fw-bold mb-1">
                        <i class="fa-solid fa-location-dot me-1"></i>
                        <span>Dirección</span>
                    </div>
                    <span class="fs-6 fw-semibold" x-text="setText(persona.direccion)"></span>
                </div>

                <div class="col-6 col-md-4">
                    <div class="d-flex align-items-center text-muted small text-uppercase fw-bold mb-1">
                        <i class="fa-solid fa-cake-candles me-1"></i>
                        <span>Nacimiento</span>
                    </div>
                    <span class="fs-6 fw-semibold" x-text="setText(onlyDate(persona.fecha_nacimiento))"></span>
                </div>

                <div class="col-6 col-md-4">
                    <div class="d-flex align-items-center text-muted small text-uppercase fw-bold mb-1">
                        <i class="fa-solid fa-calendar-plus me-1"></i>
                        <span>Registro</span>
                    </div>
                    <span class="fs-6 fw-semibold" x-text="setText(onlyDate(persona.fecha_creacion))"></span>
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