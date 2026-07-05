<?php
$title = "Respaldos";

$this->pushJs("pages/respaldos/respaldos.js");
$this->layout("layout", ["title" => $title]);
?>

<?php ob_start() ?>
<div x-data="respaldos" @crud-respaldo="openModal($event.detail.dataId)">
    <div class="container">
        <button
            class="btn btn-primary"
            @click="handleBackup()"
            :disabled="loading"
            :aria-busy="loading"
            x-text="loading ? 'Respaldando...' : 'Respaldar'"></button>
    </div>

    <?= $this->fetch('crudTable', ['xData' => 'crudRespaldos']) ?>

    <!-- Modal desactivado -->
    <div
        class="ModalComponent modal fade"
        tabindex="-1"
        x-ref="modal"
        :closedby="loading ? 'none' : 'any'">
        <div class="modal-dialog">
            <article class="modal-content">
                <header class="modal-header">
                    <h4 class="modal-title fw-semibold">
                        <i class="fa-solid fa-history"></i>
                        Restaurar desde respaldo
                    </h4>

                    <button type="button" class="btn-close" @click="closeModal()" aria-label="Close"></button>
                </header>

                <div class="modal-body text-center">
                    <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>

                    <p><strong>Esta acción es irreversible</strong></p>
                    <p>Todos los datos del sistema seran restaurados hasta la fecha:</p>

                    <div class="card my-3">
                        <div class="card-body bg-body-tertiary">
                            <p class="fw-semibold fs-3" x-text="data.datetimeHuman"></p>
                            <p class="fw-semibold text-muted fs-5" x-text="data.datetime"></p>
                        </div>
                    </div>

                    <p><strong>¿Continuar?</strong></p>
                </div>

                <footer class="modal-footer">
                    <button class="btn btn-secondary" @click="closeModal()">No</button>

                    <form @submit.prevent="handleRestore()">
                        <button
                            class="btn btn-danger"
                            :aria-busy="loading"
                            :disabled="loading">Si</button>
                    </form>
                </footer>
            </article>
        </div>
    </div>
</div>
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

<style>
    .ModalComponent {
        .modal-header {
            background-color: #C62828;
            color: white;
        }

        .small-modal {
            max-width: 400px;
        }
    }
</style>