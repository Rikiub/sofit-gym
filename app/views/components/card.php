<?php
// Diseño de tarjeta reutilizable

// Props
$class ??= "";

$icon ??= 'fa-dumbbell';
$title ??= '';
$header_right ??= '';
$body ??= '';
?>

<div class="CardComponent <?= $class ?>">
    <div class="card">
        <header class="card-header d-flex justify-content-between">
            <h1 class="card-title fs-3 fw-semibold mb-0">
                <i class="fa <?= $icon ?>"></i>
                <?= $this->e($title) ?>
            </h1>

            <?= $header_right ?>
        </header>

        <div class="card-body">
            <?= $body ?>
        </div>
    </div>
</div>

<style>
    .CardComponent {
        >.card {
            --bs-gutter-x: 0;
            max-width: 1100px;
            margin: 0 auto;
            background: white;
            border-radius: 28px;
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            border: none;
        }

        >.card>.card-header {
            background: var(--primary-bg);
            color: white;
            padding: 1.2rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }
    }
</style>