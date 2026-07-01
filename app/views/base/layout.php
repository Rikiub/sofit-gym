<?php

/**
 * Layout principal que incorpora la barra lateral.
 * Es el que se utiliza en la mayoria de vistas.
 *
 * Para utilizarlo, escribe:
 * $this->layout('layout')
 * En cualquier vista.
 */

// Props
$sidebar ??= true;
$title ??= null;

// Insertar layout base
$this->layout('base', ['title' => $title]);
?>

<div class="layout-root <?= $sidebar ? 'layout-sidebar' : '' ?>">
    <?php if ($sidebar): ?>
        <?= $this->insert('sidebar') ?>
    <?php endif ?>

    <div class="layout-content">
        <?= $this->section('content') ?>
    </div>
</div>

<style>
    .layout-sidebar {
        display: grid;
        grid-template-columns: auto 1fr;
    }

    .layout-root {
        --backdrop-opacity: 0.1;
        background-image:
            linear-gradient(rgba(0, 0, 0, var(--backdrop-opacity)), rgba(0, 0, 0, var(--backdrop-opacity))),
            url('<?= ASSETS_DIR . '/base/background.webp' ?>');
        background-color: #021C26;
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        background-attachment: fixed;

        height: 100%;
        min-height: 100cqh;

        .layout-content {
            padding: 1rem;
        }
    }
</style>