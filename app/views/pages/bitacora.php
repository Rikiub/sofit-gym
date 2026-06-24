<?php
$title = "Bitacora";

$this->pushJs("pages/bitacora/bitacora.js");
$this->layout("layout", ["title" => $title]);
?>

<script type="module">
    import {
        fetchApi
    } from "@/js/api.js";
    import Alpine from "alpinejs";

    Alpine.data("bitacora", () => ({
        dias: 30,

        async cleanRegistros() {
            await fetchApi({
                page: "bitacora",
                action: "limpiarRegistros",
                dias: this.dias,
            });
            this.$dispatch("form-success");
        },
    }));
</script>

<?= $this->insert("card", [
    "icon" => "fa-history",
    "title" => $title,
    "body" => <<<HTML
        <main>
            {$this->fetch("crudTable")}
        </main>
    HTML,
    "header_right" => <<<HTML
        <button class="btn btn-warning" x-data="bitacora" @click="cleanRegistros()">
            Limpiar registros (Ultimos <span x-text="dias"></span> dias)
        </button>
    HTML,
]) ?>