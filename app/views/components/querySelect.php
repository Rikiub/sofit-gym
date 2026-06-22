<?php

use function App\Helpers\encodeToJson;
use function App\Helpers\stringifyAttributes;

/// PROPS
$input ??= []; // Atributos HTML para el input (para definir 'required' o 'name')
$placeholder ??= "Seleccionar..."; // Texto a mostrar al no tener datos seleccionados

/**
 * --- Formato:
 * 
 * [
 *  ['name' => 'Nombre de columna', 'id' => '<propiedad>']
 * ]
 * 
 * --- Propiedades
 * name: Nombre de la columna.
 * id: <propiedad> a mapear basado en los datos devueltos por el servidor.
 * computed: Si se proporciona, se usara para mapear en vez del 'id'.
 *           Puedes usar expresiones de JavaScript.
 *           Cada atributo debe empezar con: item.<propiedad>
 */
$columns ??= [];

// Configuración JavaScript
$configJson = encodeToJson([
    "params" => $params ??= [], // Parametros fetch ['page' => 'clientes', 'action' => 'query', ...]
    "searchParam" => $searchParam ??= "search", // Parametro a usar para la busqueda
    "itemKey" => $itemKey ??= "id", // Clave primaria para identificar cada item
]);

$this->pushJs("components/querySelect.js");
?>

<div x-data="querySelect(<?= $this->e($configJson) ?>)">
    <input x-ref="input" x-model="selected" hidden <?= stringifyAttributes($input) ?>>

    <button
        type="button"
        @click="togglePopover()"
        x-ref="selectButton"
        x-text="selected || '<?= $this->e($placeholder) ?>'"
        class="form-select text-start"
        :class="{ 'text-body-secondary': !selected }"></button>

    <div x-ref="popoverContainer">
        <div x-ref="popoverContent" style="display: none;">
            <search>
                <input
                    type="text"
                    class="form-control mb-2"
                    placeholder="Buscar..."
                    @input.debounce.300ms="handleSearch"
                    x-model="search">
            </search>

            <div class="overflow-y-auto" style="max-height: 300px;">
                <table class="table table-striped table-sm align-middle">
                    <thead>
                        <tr>
                            <?php foreach ($columns as $col): ?>
                                <th><?= $this->e($col['name']) ?></th>
                            <?php endforeach; ?>

                            <th class="text-end">Acción</th>
                        </tr>
                    </thead>

                    <tbody>
                        <template x-for="item in items" :key="item[itemKey]">
                            <tr>
                                <?php foreach ($columns as $col): ?>
                                    <?php if (isset($col["computed"])): ?>
                                        <td x-text="<?= $col['computed'] ?>"></td>
                                    <?php else: ?>
                                        <td x-text="item.<?= $col['id'] ?>"></td>
                                    <?php endif ?>
                                <?php endforeach; ?>

                                <td class="text-end">
                                    <button type="button" class="btn btn-sm btn-secondary" @click="setSelected(item)">
                                        Seleccionar
                                    </button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
    .select-popover {
        --bs-popover-max-width: 450px;
        width: 450px;
    }
</style>