<?php

use function App\Helpers\escapeJs;

$form ??= null;
$xData ??= 'modalForm';

$this->pushJs('components/modalForm.js');
?>

<div
    class="ModalComponent modal fade"
    tabindex="-1"
    x-ref="modal"
    x-data="<?= escapeJs($xData) ?>"
    x-id="['form']"
    :closedby="loading ? 'none' : 'any'"
    @open-modal.window="handleOpenModal($event.detail)">
    <div class="modal-dialog" :class="mode == 'delete' ? 'modal-sm' : 'modal-lg'">
        <article class="modal-content">
            <header class="modal-header">
                <template x-if="mode == 'add'">
                    <h4 class="modal-title fw-semibold">
                        <i class="fa-solid fa-square-plus"></i>
                        Crear
                        <span x-text="elementName"></span>
                    </h4>
                </template>

                <template x-if="mode == 'edit'">
                    <h4 class="modal-title fw-semibold">
                        <i class="fa-solid fa-pen-to-square"></i>
                        Editar
                        <span x-text="elementName"></span>
                    </h4>
                </template>

                <template x-if="mode == 'delete'">
                    <h4 class="modal-title fw-semibold">
                        <i class="fa-solid fa-trash-can"></i>
                        Eliminar
                        <span x-text="elementName"></span>
                    </h4>
                </template>

                <button type="button" class="btn-close" @click="closeModal()" aria-label="Close"></button>
            </header>

            <div class="modal-body">
                <div x-show="mode == 'delete'">
                    <div class="text-center">
                        <i class="fas fa-exclamation-triangle fa-3x text-danger mb-3"></i>
                        <p class="fw-bold">¿Está seguro de eliminarlo?</p>
                    </div>
                </div>

                <form
                    x-show="mode !== 'delete'" x-ref="form"
                    @submit.prevent="handleSubmit"
                    :id="$id('form')"
                    novalidate>
                    <?= $form ?>
                </form>
            </div>

            <footer class="modal-footer">
                <button
                    class="btn btn-primary"
                    x-show="mode == 'add'"
                    :form="$id('form')"
                    :aria-busy="loading"
                    :disabled="loading">Crear</button>

                <button
                    class="btn btn-primary"
                    x-show="mode == 'edit'"
                    :form="$id('form')"
                    :aria-busy="loading"
                    :disabled="loading">Guardar Cambios</button>

                <div x-show="mode == 'delete'">
                    <button class="btn btn-secondary" @click="closeModal()">No</button>
                    <button
                        class="btn btn-danger"
                        :form="$id('form')"
                        :aria-busy="loading"
                        :disabled="loading">Si</button>
                </div>
            </footer>
        </article>
    </div>
</div>

<style>
    .ModalComponent {
        .modal-header {
            background-color: #C62828;
            color: white;
        }
    }
</style>