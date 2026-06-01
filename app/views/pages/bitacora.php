<?php
$title = "Bitacora";

$this->pushJs("pages/bitacora/bitacora.js");
$this->layout("layout", ["title" => $title]);
?>

<?= $this->insert("card", [
    "icon" => "fa-history",
    "title" => $title,
    "body" => <<<HTML
        <main>
            {$this->fetch("crudTable")}
        </main>
    HTML,
]) ?>
