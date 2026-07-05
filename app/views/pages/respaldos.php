<?php
$title = "Respaldos";

$this->pushJs("pages/bitacora/bitacora.js");
$this->layout("layout", ["title" => $title]);
?>

<?php ob_start() ?>

<?php $BODY = ob_get_clean() ?>

<?= $this->insert("card", [
    "icon" => "fa-history",
    "title" => $title,
    "body" => <<<HTML
        <main>
            {$BODY}
        </main>
    HTML,
]) ?>