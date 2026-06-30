<?php
$cedula ??= null;

$this->layout('layout', ["title" => "Cliente"]);
$this->pushJs('pages/clientes/clientes.js');
?>

<?php ob_start() ?>
<div class="row g-3">
    <div class="col-md-6">
        <?= $this->insert("persona/infoCard", [
            "type" => "clientes",
            "elementName" => "Cliente",
            "cedula" => $cedula,
        ]) ?>
    </div>

    <!-- Membresía -->
    <div class="col-md-6" x-data="clienteInfo">
        <article class="card h-100 border border-light-subtle bg-body-tertiary rounded-3 shadow-none">
            <header class="card-header bg-transparent border-0 pt-3 pb-0">
                <div class="d-flex align-items-center">
                    <i class="fa-solid fa-id-badge text-primary me-2 fs-5"></i>
                    <h5 class="card-title mb-0 fw-bold tracking-wider">Membresía</h5>
                </div>
            </header>

            <div class="card-body p-4">
                <template x-if="cliente.membresia?.fecha_inicio">
                    <div class="row">
                        <div class="col-6 col-md-4">
                            <div class="d-flex align-items-center text-muted text-uppercase small fw-bold mb-1">
                                <i class="fa-solid fa-calendar-days me-1"></i>
                                <span>Inicio</span>
                            </div>
                            <span class="fs-6 fw-semibold" x-text="setText(onlyDate(cliente.membresia?.fecha_inicio))"></span>
                        </div>

                        <div class="col-6 col-md-4">
                            <div class="d-flex align-items-center text-muted text-uppercase small fw-bold mb-1">
                                <i class="fa-solid fa-calendar-xmark me-1"></i>
                                <span>Vencimiento</span>
                            </div>
                            <span class="fs-6 fw-semibold" x-text="setText(onlyDate(cliente.membresia?.fecha_fin))"></span>
                        </div>

                        <div class="col-12 col-md-4">
                            <div class="d-flex align-items-center text-muted text-uppercase small fw-bold mb-1">
                                <i class="fa-solid fa-hourglass-half me-1"></i>
                                <span>Restantes</span>
                            </div>
                            <span class="badge bg-primary-subtle text-primary px-3 py-2 rounded-pill fs-6" x-text="`${diasRestantes()} dias`"></span>
                        </div>

                        <hr class="opacity-25 my-3">

                        <div class="col-6">
                            <div class="d-flex align-items-center text-muted text-uppercase small fw-bold mb-1">
                                <i class="fa-solid fa-id-card me-1"></i>
                                <span>Plan</span>
                            </div>
                            <span class="fs-5 fw-bold" x-text="setText(cliente.membresia?.tipo)"></span>
                        </div>

                        <div class="col-6 d-flex flex-column align-items-start">
                            <div class="d-flex align-items-center text-muted text-uppercase small fw-bold mb-1">
                                <i class="fa-solid fa-circle-check me-1"></i>
                                <span>Estado</span>
                            </div>
                            <span class="badge bg-success-subtle text-success px-3 py-2 fw-semibold" x-text="setText(cliente.membresia?.estado)"></span>
                        </div>
                    </div>
                </template>

                <template x-if="!cliente.membresia?.fecha_inicio">
                    <div class="text-center py-4 text-muted my-auto">
                        <div class="mb-2">
                            <i class="fa-solid fa-credit-card fa-3x text-body-tertiary"></i>
                        </div>

                        <p class="mb-0 fw-medium text-secondary fs-6">Sin membresía</p>
                    </div>
                </template>
            </div>
        </article>
    </div>
</div>

<hr class="opacity-25 my-4">

<details class="card" open>
    <summary class="card-header">
        <h4 class="d-inline-block mb-0">Seguimiento Fisico</h4>
    </summary>

    <div class="card-body">
        <?= $this->insert('crudTable', ['xData' => 'crudFisico']) ?>
        <?= $this->insert('modalForm', [
            'xData' => 'modalFisico',
            'form' => $this->fetch('clientes/forms/seguimiento_fisico'),
        ]) ?>
    </div>
</details>

<hr>

<details class="card" open>
    <summary class="card-header">
        <h4 class="d-inline-block mb-0">Seguimiento Nutricional</h4>
    </summary>

    <div class="card-body">
        <?= $this->insert('crudTable', ['xData' => 'crudNutricion']) ?>
        <?= $this->insert('modalForm', [
            'xData' => 'modalNutricion',
            'form' => $this->fetch('clientes/forms/seguimiento_nutricional'),
        ]) ?>
    </div>
</details>
<?php $body = ob_get_clean() ?>

<?= $this->insert("card", [
    "icon" => "fa-info-circle",
    "title" => "Información del cliente",
    "header_right" => <<<HTML
        <a href="?page=clientes" class="btn btn-primary">
            <i class="fa-solid fa-arrow-left"></i>
            Volver
        </a>
    HTML,
    "body" => $body,
]) ?>