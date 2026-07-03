<?php

/**
 * HTML base que heredan todas las vistas. Debe mantenerse lo más simple posible.
 * Tambien es donde se insertan las dependencias web.
 */

use App\Core\Config;

$assets = Config::get("web.assets");
$isDebug = Config::get("debug");
?>

<!DOCTYPE html>
<html lang="es" data-theme="light" data-bs-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="color-scheme" content="light dark">
    <link rel="icon" href="<?= $assets ?>/favicon.svg">

    <!-- Dependencias JavaScript -->
    <script type="importmap">
        {
            "imports": {
                "@/": "<?= $assets ?>/",

                "sharkdown": "<?= $assets ?>/lib/snarkdown/snarkdown.es.js",
                "form-data-json": "<?= $assets ?>/lib/form-data-json/form-data-json.es6.js",
                "alpinejs": "<?= $assets ?>/lib/alpinejs/alpinejs.esm.min.js",
                "gridjs": "<?= $assets ?>/lib/gridjs/gridjs.module.js",
                "dayjs": "<?= $assets ?>/lib/dayjs/dayjs.esm.js"
            }
        }
    </script>

    <?php
    $this->pushJs('lib/alpinejs/mask.min.js'); // AlpineJS: Mask Plugin
    $this->pushCss('lib/gridjs/mermaid.min.css');  // GridJS

    // Bootstrap
    $this->pushCss("lib/bootstrap/bootstrap.min.css");
    $this->pushJs("lib/bootstrap/bootstrap.bundle.min.js", false);

    // Font Awesome
    $this->pushCss("lib/font-awesome/css/all.min.css");

    // Importes globales
    $this->pushCss('base/index.css');
    $this->pushJs('base/index.js');
    ?>

    <?= $this->renderCss() ?>
    <?= $this->renderJs() ?>

    <style>
        /**
        Muestra siempre la scrollbar
        Para evitar flickering
        */
        html {
            overflow-y: scroll;
            scrollbar-width: thin;
            scrollbar-gutter: stable;
        }

        body {
            /** 
            Remueve el padding extraño que agrega
            Boostrap al abrir un modal.
            */
            padding-right: 0 !important;
        }
    </style>

    <title><?= $this->e($title ?? 'Sofit Gym') ?></title>
</head>

<body>
    <div id="app">
        <?= $this->section('content') ?>
    </div>
</body>

<script type="module">
    // Declarar variable global
    self.DEBUG = <?= json_encode($isDebug) ?>;

    // Inicializar AlpineJS
    import Alpine from 'alpinejs';
    self.Alpine = Alpine;
    Alpine.start();
</script>