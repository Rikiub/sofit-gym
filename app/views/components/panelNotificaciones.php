<?php
$this->pushJs("components/panelNotificaciones.js");
?>

<div x-data="panelNotificaciones" class="notifications-panel">
    <!-- Contenedor con altura máxima y scroll -->
    <div class="notifications-list" style="max-height: 360px; overflow-y: auto;">
        <ul class="list-unstyled mb-0">
            <template x-for="notif in notificaciones" :key="notif.id_notificacion">
                <li class="d-flex justify-content-between align-items-start py-2 px-3 border-bottom"
                    :class="{ 'bg-light fw-bold': !notif.leido, 'opacity-75': notif.leido }">

                    <div class="me-2 flex-grow-1" style="min-width: 0;">
                        <div class="d-flex align-items-center justify-content-between mb-1">
                            <h3 class="fs-5 fw-semibold text-dark text-truncate"
                                x-text="notif.titulo"
                                style="font-size: 0.875rem; line-height: 1.3;">
                            </h3>

                            <i
                                class="fa-regular fa-circle-check me-1 fs-5 flex-shrink-0"
                                :class="notif.leido ? 'text-success' : 'text-muted'"
                                :title="notif.leido ? 'Leído' : 'Marcar como leída'"
                                @click="!notif.leido && marcarLeida(notif.id_notificacion, notif.leido)"
                                style="font-size: 0.9rem;"
                                :style="notif.leido ? 'cursor: default;' : 'cursor: pointer;'"></i>
                        </div>

                        <!-- Vista colapsada (truncada) -->
                        <p class="mb-1 text-muted text-truncate"
                            x-show="!notif.expanded"
                            x-text="notif.contenido"
                            style="font-size: 0.8rem; line-height: 1.4;"></p>

                        <!-- Vista expandida (recuadro legible) -->
                        <div x-show="notif.expanded"
                            style="max-height: 180px; overflow-y: auto; overflow-x: hidden; font-size: 0.8rem; line-height: 1.5;">
                            <p class="mb-2 text-dark"
                                style="white-space: pre-wrap; word-break: break-word;"
                                x-text="notif.contenido"></p>

                            <button class="btn btn-link btn-sm p-0 text-decoration-none"
                                @click="notif.expanded = false"
                                style="font-size: 0.75rem;">
                                Mostrar menos
                            </button>
                        </div>

                        <!-- Botón "Leer más" (solo colapsado) -->
                        <button x-show="!notif.expanded"
                            class="btn btn-link btn-sm p-0 text-decoration-none mb-1"
                            @click="notif.expanded = true"
                            style="font-size: 0.75rem;">
                            Leer más...
                        </button>

                        <small class="text-muted d-block" x-text="notif.fecha_creacion" style="font-size: 0.7rem;"></small>
                    </div>
                </li>
            </template>

            <!-- Mensaje cuando no hay notificaciones -->
            <li x-show="notificaciones.length === 0" class="py-3 px-3 text-muted text-center">
                <i class="fa-regular fa-bell-slash me-2"></i> No hay notificaciones
            </li>
        </ul>
    </div>

    <!-- Separador y acciones -->
    <div class="border-top mt-1">
        <div class="d-flex justify-content-between align-items-center px-3 py-2">
            <div x-show="!unreadCount"></div>
            <button class=" btn btn-sm btn-outline-primary"
                @click="marcarTodasLeidas()"
                x-show="unreadCount > 0">
                <i class="fa-solid fa-check-double me-1"></i> Marcar todas leídas
            </button>

            <button class="btn btn-sm btn-outline-secondary" @click="refresh()">
                <i class="fa-solid fa-arrows-rotate"></i>
            </button>
        </div>
    </div>
</div>