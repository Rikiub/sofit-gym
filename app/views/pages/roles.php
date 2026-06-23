<?php

/** @var \App\Helpers\Auth\UsuarioSessionDTO $sesion_usuario */

$title = "Roles y Permisos";

$this->layout('layout', ['title' => $title]);
$this->pushJs('pages/roles/roles.js');

// Agrupar permisos
$permisos ??= [];
$groupedPermisos = [];

foreach ($permisos as $p) {
    $nombre = $p["nombre"];
    $parts = explode(":", $nombre);

    $modulo = $parts[0];
    $accion = $parts[1];

    $new = [
        "nombre" => $nombre,
        "modulo" => $modulo,
        "accion" => $accion,
    ];

    $groupedPermisos[$modulo][] = $new;
}
?>

<?php ob_start() ?>
<fieldset class="row" x-data="{ id_rol: null, nombre: null }">
    <input hidden name="id_rol" x-model="id_rol">
    <input hidden name="nombre" x-model="nombre">

    <h1 class="fs-4" x-text="nombre"></h1>
    <h2 class="fs-5">Permisos</h2>

    <div class="row g-2">
        <?php foreach ($groupedPermisos as $modulo => $listaPermisos): ?>
            <div class="col-12 col-md-6 col-lg-4">
                <div class="p-3 border rounded bg-light h-100">
                    <h3 class="fs-6 text-muted border-bottom pb-2 mb-3">
                        <?= ucfirst($modulo) ?>
                    </h3>

                    <?php foreach ($listaPermisos as $p): ?>
                        <div class="mb-2">
                            <label class="d-flex align-items-center gap-2 m-0" style="cursor: pointer;">
                                <input
                                    class="form-check-input"
                                    type="checkbox"
                                    name="permisos[]"
                                    value="<?= $p["nombre"] ?>"
                                    :disabled="id_rol == 1 && $el.checked">
                                <span class="form-label mb-0"><?= $p["accion"] ?></span>
                            </label>
                        </div>
                    <?php endforeach ?>
                </div>
            </div>
        <?php endforeach ?>
    </div>

    <small class="text-danger mt-2" x-text="errors['permisos[]']"></small>
</fieldset>
<?php $form = ob_get_clean() ?>

<?= $this->insert('card', [
    'title' => $title,
    'body' => <<<HTML
            <main>
                {$this->fetch('crudTable', ["xData" => "crudPermisos"])}
            </main>

            {$this->insert("modalForm", ["xData" => "modalPermisos", "form" =>$form])}
        HTML
]) ?>