<?php
$opciones ??= [];
$title = "Configuración del sistema";
$this->layout("layout", ["title" => $title]);

$grupos = [];
foreach ($opciones as $clave => $detalle) {
    $nombreGrupo = $detalle['grupo_nombre'] ?: 'General';
    $grupos[$nombreGrupo][$clave] = $detalle;
}
?>

<script type="module">
    import Alpine from "alpinejs";
    import {
        fetchApi
    } from "@/js/api.js";

    Alpine.data("opciones", () => ({
        success: "",

        async handleSubmit(event) {
            const form = event.target;
            const formData = new FormData(form);

            // Build a flat array of objects: [{ clave: "ai.modelo", valor: "gpt-4" }, ...]
            const payload = [];
            for (let [key, value] of formData.entries()) {
                payload.push({
                    clave: key,
                    valor: value
                });
            }
            console.log(payload)

            try {
                await fetchApi({
                    page: "opciones",
                    action: "update"
                }, {
                    method: "POST",
                    body: payload // Send the array directly
                });

                this.success = "Configuración guardada con éxito";
                setTimeout(() => {
                    this.success = "";
                }, 3000);

            } catch (error) {
                console.error("Error updating options:", error);
                alert("Ocurrió un error al guardar los cambios.");
            }
        }
    }));
</script>

<?php ob_start() ?>
<main class="Opciones px-1 mb-5">
    <form x-data="opciones" @submit.prevent="handleSubmit">
        <div class="accordion shadow-sm">
            <div x-show="success" x-text="success" class="alert alert-success mt-3" style="display: none;"></div>

            <?php
            $i = 0;
            foreach ($grupos as $grupo => $items):
                $isFirst = $i === 0;
                $collapseId = 'collapse_' . md5($grupo);
                $headingId = 'heading_' . md5($grupo);
            ?>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="<?= $headingId ?>">
                        <button class="accordion-button <?= $isFirst ? '' : 'collapsed' ?>"
                            type="button"
                            data-bs-toggle="collapse"
                            data-bs-target="#<?= $collapseId ?>"
                            aria-expanded="<?= $isFirst ? 'true' : 'false' ?>">

                            <?= $this->e($grupo) ?>
                        </button>
                    </h2>

                    <div id="<?= $collapseId ?>"
                        class="accordion-collapse collapse <?= $isFirst ? 'show' : '' ?>"
                        data-bs-parent="#opcionesAccordion">
                        <div class="accordion-body">

                            <?php foreach ($items as $clave => $opt): ?>
                                <div class="mb-3">
                                    <label for="input_<?= md5($clave) ?>" class="form-label fw-bold">
                                        <?= $this->e($opt['nombre']) ?>
                                    </label>

                                    <!-- Inputs use array syntax in name attribute to easily catch them in PHP -->
                                    <input name="<?= $this->e($clave) ?>"
                                        value="<?= $this->e($opt['valor']) ?>"
                                        class="form-control"
                                        id="input_<?= md5($clave) ?>"
                                        type="text">

                                    <?php if (!empty($opt['descripcion'])): ?>
                                        <div class="form-text text-muted">
                                            <?= $this->e($opt['descripcion']) ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>

                        </div>
                    </div>
                </div>
            <?php
                $i++;
            endforeach;
            ?>
        </div>

        <div class="mt-4 text-end">
            <button type="submit" class="btn btn-primary px-4">
                <i class="fa-solid fa-save me-2"></i> Guardar Cambios
            </button>
        </div>
    </form>
</main>
<?php $body = ob_get_clean() ?>

<?= $this->insert('card', [
    "class" => "main",
    "icon" => "fa-cogs",
    'title' => $title,
    'body' => $body
]) ?>

<style>
    .Opciones {
        .accordion-button:focus {
            box-shadow: none;
        }
    }
</style>