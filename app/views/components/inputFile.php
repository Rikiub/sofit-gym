<?php

use function App\Helpers\stringifyAttributes;

$input ??= [];
?>

<script type="module">
    import Alpine from "alpinejs";

    Alpine.data("inputFile", () => ({
        image: null,

        async sendImage(input) {
            const file = input.files[0];
            if (!file) return;

            const formData = new FormData();
            formData.append('image', file);

            const query = new URLSearchParams({
                page: "upload",
                action: "uploadImageTemp",
            }).toString();
            const url = `?${query}`;

            try {
                const res = await fetch(url, {
                    method: 'POST',
                    body: formData,
                });
                const json = await res.json();

                if (res.ok) {
                    this.image = json.temp_filename;

                    if (self.DEBUG) {
                        console.log('Imagen subida temporalmente:', json.temp_filename);
                    }
                } else {
                    alert('Error: ' + json.message);
                }
            } catch (error) {
                console.error('Error en la precarga:', error);
            }
        },
    }));
</script>

<div x-data="inputFile" class="d-inline-block">
    <div
        @click="$refs.input.click()"
        class="image-select-card border border-2 rounded overflow-hidden position-relative"
        style="width: 120px; height: 120px; cursor: pointer;">

        <div x-show="image">
            <img :src="image" class="w-100 h-100" style="object-fit: cover;" alt="Seleccionar imagen">
        </div>
        <div x-show="!image">
            <img src="<?= BASE_DIR ?>/assets/default-profile.png" class="w-100 h-100" style="object-fit: cover;" alt="Seleccionar imagen">
        </div>

        <div class="hover-overlay position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center bg-dark bg-opacity-25 opacity-0">
            <span class="text-white small fw-bold">Cambiar</span>
        </div>
    </div>

    <input x-ref="input" hidden class="form-control" type="file" @change="sendImage($el)">
    <input hidden x-model="image" <?= stringifyAttributes($input) ?>>
</div>

<style>
    .image-select-card {
        border-color: #dee2e6;
        /* Default Bootstrap gray border */
        transition: all 0.25s ease-in-out;
    }

    /* Hover States */
    .image-select-card:hover {
        border-color: #0d6efd !important;
        /* Bootstrap primary blue */
        box-shadow: 0 4px 12px rgba(13, 110, 253, 0.15);
        transform: translateY(-2px);
        /* Subtle lift effect */
    }

    .image-select-card:hover .hover-overlay {
        opacity: 1 !important;
        transition: opacity 0.2s ease-in-out;
    }
</style>