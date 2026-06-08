<?php
$input ??= [];
$id_rol ??= "";
?>

<?= $selectTrabajadores = $this->fetch("querySelect", [
    "input" => $input,
    "columns" => [
        ["name" => "Cédula", "id" => 'cedula'],
        ["name" => "Nombre", "id" => "nombre_completo", "computed" => '`${item.nombre} ${item.apellido}`'],
        ["name" => "Rol", "id" => 'rol']
    ],
    "params" => [
        "page" => "trabajadores",
        "action" => "query",
        "id_rol" => $id_rol,
    ],
    "itemKey" => "cedula",
]); ?>