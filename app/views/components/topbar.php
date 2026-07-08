<?php

/** @var \App\Services\Auth\CurrentUser $sesion_usuario */

$this->pushJs("components/topbar.js");
?>

<nav class="d-flex justify-content-end">
    <div class="d-flex gap-3 align-items-center">
        <div
            class="position-relative"
            x-data="{ unreadCount: 0, show: false }"
            @update-notifications="unreadCount = $event.detail.unreaded"
            @click.outside="show = false">

            <button class="btn btn-link text-decoration-none text-white fw-semibold position-relative" @click="show = !show">
                <i class="fas fa-bell text-white fs-5 cursor-pointer hover-lift"></i>

                <!-- Badge de notificaciones no leídas -->
                <span x-show="unreadCount > 0"
                    x-text="unreadCount"
                    class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                    style="font-size: 0.6rem; padding: 0.2rem 0.4rem; margin-top: -6px; margin-left: -2px;">
                </span>
            </button>

            <div
                class="dropdown-menu end-0 shadow border-0 p-2 rounded-4"
                :class="{ show: show }" style="width: 500px;">
                <?= $this->insert("panelNotificaciones") ?>
            </div>
        </div>

        <div class="position-relative" x-data="menu('<?= $sesion_usuario->id ?>')" @click.outside="show = false" @form-success="refresh()">
            <?= $this->insert("usuarios/modalForm", ["id" => "usuarios"]) ?>

            <button class="btn p-0 border-0 rounded-circle overflow-hidden hover-lift d-flex align-items-center justify-content-center bg-dark" style="width: 50px; height: 50px;" @click="show = !show">
                <img class="w-100 h-100 object-fit-cover" :src="usuario?.imagen_url">
            </button>

            <div class="dropdown-menu end-0 shadow border-0 p-0 overflow-hidden rounded-4" :class="{ show: show }" style="width: 250px; max-width: 85vw;">
                <div class="d-flex align-items-center gap-3 p-3 border-bottom bg-light">
                    <div class="rounded-circle overflow-hidden flex-shrink-0" style="width: 50px; height: 50px;">
                        <img class="w-100 h-100 object-fit-cover" :src="usuario?.imagen_url">
                    </div>
                    <div class="d-flex flex-column lead fs-6">
                        <strong class="text-dark th-sm" x-text="usuario?.nombre_usuario"></strong>
                        <span class="text-muted small" x-text="usuario?.rol"></span>
                    </div>
                </div>

                <div class="p-2">
                    <button class="btn btn-link dropdown-item d-flex align-items-center gap-2 py-2 rounded-3" @click="$dispatch('open-modal', { id: 'usuarios', dataId: '<?= $sesion_usuario->id ?>', mode: 'edit' })">
                        <i class="fas fa-user text-danger text-center" style="width: 24px;"></i>
                        <span>Perfil</span>
                    </button>

                    <a class="dropdown-item d-flex align-items-center gap-2 py-2 rounded-3 text-danger" href="?page=login&action=logout">
                        <i class="fa-solid fa-right-from-bracket text-center" style="width: 24px;"></i>
                        <span>Cerrar sesión</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</nav>