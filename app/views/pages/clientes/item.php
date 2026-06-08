<?php
$cedula ??= null;

$this->layout('layout', ["title" => "Cliente"]);
$this->pushJs('pages/clientes/clientes.js');
?>

<?php ob_start() ?>
<div class="row mb-4">
    <div class="col-md-6">
        <?= $this->insert("persona/infoCard", [
            "type" => "clientes",
            "elementName" => "Cliente",
            "cedula" => $cedula,
        ]) ?>
    </div>

    <!-- Membresía -->
    <div class="col-md-6" x-data="clienteInfo">
        <div class="card shadow-sm">
            <header class="card-header">
                <h3 class="card-title mb-0">Membresia</h3>
            </header>

            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-6">
                        <h5>Fecha de inicio</h5>
                        <p x-text="setText(onlyDate(cliente.membresia?.fecha_inicio))"></p>
                    </div>

                    <div class="col-6">
                        <h5>Fecha de vencimiento</h5>
                        <p x-text="setText(onlyDate(cliente.membresia?.fecha_fin))"></p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-6">
                        <h5>Tipo</h5>
                        <p x-text="setText(cliente.membresia?.tipo)"></p>
                    </div>

                    <div class="col-6">
                        <h5>Estado</h5>
                        <p x-text="setText(cliente.membresia?.estado)"></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<hr>

<details class="card" open>
    <summary class="card-header">
        <h4 class="d-inline-block mb-0">Seguimiento Fisico</h4>
    </summary>

    <div class="card-body">
        <?= $this->insert('crudTable', ['xData' => 'crudSegFisico']) ?>
        <?= $this->insert('modalForm', [
            'xData' => 'modalSegFisico',
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
        <?= $this->insert('crudTable', ['xData' => 'crudSegNutricional']) ?>
        <?= $this->insert('modalForm', [
            'xData' => 'modalSegNutricional',
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