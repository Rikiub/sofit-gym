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
$backgroundImage = ASSETS_DIR . '/base/background.webp';
?>

<div
    class="layout-root <?= $sidebar ? 'layout-sidebar' : '' ?>"
    style="background-image: url('<?= $backgroundImage ?>'); background-color: #021C26;">

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