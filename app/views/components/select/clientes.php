<?php
$input ??= [];
?>

<?= $selectClientes = $this->fetch("querySelect", [
    "input" => $input,
    "columns" => [
        ["name" => "Cédula", "id" => 'cedula'],
        ["name" => "Nombre", "id" => "nombre_completo", "computed" => '`${item.nombre} ${item.apellido}`'],
    ],
    "params" => [
        "page" => "clientes",
        "action" => "query",
    ],
    "itemKey" => "cedula",
]); ?>