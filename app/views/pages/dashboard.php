<?php

$this->layout('layout', ['title' => 'Inicio']);

$this->pushJs('https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js', false);
$this->pushCss('pages/inicio/inicio.css');
$this->pushJs('pages/inicio/inicio.js');

// Props
/** @var \App\Helpers\Auth\UsuarioSessionDTO $usuario */
?>

<script type="module">
    import Alpine from "alpinejs";
    import {
        fetchApi
    } from "@/js/api.js";

    Alpine.data("menu", () => ({
        usuario: null,

        async init() {
            this.refresh();
        },

        async refresh() {
            this.usuario = await fetchApi({
                page: "usuarios",
                action: "find",
                id: "<?= $usuario->id ?>"
            })
        }
    }));
</script>

<div class="Inicio">
    <main class="main-content">
        <div class="content-wrapper">
            <nav class="top-nav">
                <div class="top-links">
                    <div class="reporte-dropdown">
                        <a href="#" id="reporteTrigger">Generar reporte</a>
                        <div class="reporte-menu" id="reporteMenu">
                            <a href="#" data-reporte="asistencia"><i class="fas fa-clipboard-list"></i> Reporte de asistencia</a>
                            <a href="#" data-reporte="financiero"><i class="fas fa-chart-line"></i> Reporte financiero</a>
                            <a href="#" data-reporte="clientes"><i class="fas fa-users"></i> Reporte de clientes</a>
                            <a href="#" data-reporte="stock"><i class="fas fa-boxes"></i> Reporte de inventario</a>
                            <div class="divider"></div>
                            <a href="#" data-reporte="personalizado"><i class="fas fa-calendar-alt"></i> Reporte personalizado</a>
                        </div>
                    </div>

                    <i class="fas fa-bell"></i>

                    <div class="profile-dropdown" x-data="menu" @form-success="refresh()">
                        <?= $this->insert("usuarios/modalForm", ["id" => "usuarios"]) ?>

                        <div class="ratio ratio-1x1 rounded-circle" style="width: 50px; height: 50px; background-color: black;" id="profileIcon">
                            <img class="img-fluid rounded-circle" :src="usuario.imagen_url">
                        </div>

                        <div class="profile-menu" id="profileMenu">
                            <div class="profile-header">
                                <div class="ratio ratio-1x1" style="width: 60px; height: 60px;">
                                    <img class="img-fluid rounded-circle" :src="usuario.imagen_url">
                                </div>

                                <div>
                                    <strong x-text="usuario.nombre_usuario"></strong>
                                    <span x-text="usuario.rol"></span>
                                </div>
                            </div>

                            <button @click="$dispatch('open-modal', { id: 'usuarios', dataId: '<?= $usuario->id ?>', mode: 'edit' })">
                                <i class="fas fa-user"></i> Perfil
                            </button>

                            <div class="pb-3">
                                <a href="?page=login&action=logout">
                                    <i class="fa-solid fa-right-from-bracket"></i> <span>Cerrar sesión</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </nav>

            <h2 class="panel-title"><i class="fas fa-chalkboard"></i> Panel de control </h2>

            <div class="stats-row">
                <div class="stat-card panel-card">
                    <h4>Progreso general</h4>
                    <div class="big-number">74%</div><span>+12% esta semana</span>
                </div>
                <div class="stat-card panel-card">
                    <h4>Atletas activos</h4>
                    <div class="big-number">187</div><span>+12 este mes</span>
                </div>
                <div class="stat-card panel-card">
                    <h4>Ingresos mensuales</h4>
                    <div class="big-number">$280</div><span>Meta $5k</span>
                </div>
                <div class="stat-card panel-card">
                    <h4>Asistencias hoy</h4>
                    <div class="big-number">102</div><span>Registradas</span>
                </div>
            </div>

            <div class="two-columns">
                <div class="card glass-card">
                    <h3><i class="fas fa-chart-line"></i> Progreso esta semana</h3>
                    <canvas id="weeklyProgress" height="200"></canvas>
                </div>
                <div class="card glass-card">
                    <h3><i class="fas fa-calendar-alt"></i> Calendario 2026</h3>
                    <div class="calendar-nav">
                        <button id="prevMonthBtn"><i class="fa-chevron-left fas"></i></button>
                        <span id="monthYearDisplay"></span>
                        <button id="nextMonthBtn"><i class="fa-chevron-right fas"></i></button>
                    </div>
                    <div class="mini-calendar">
                        <div class="cal-weekdays"><span>Lun</span><span>Mar</span><span>Mié</span><span>Jue</span><span>Vie</span><span>Sáb</span><span>Dom</span></div>
                        <div class="cal-days" id="calendarDays"></div>
                    </div>
                    <ul class="event-list">
                        <li><i class="fas fa-chalkboard"></i> 28/04 - Capacitación instructores</li>
                        <li><i class="fas fa-wrench"></i> 30/04 - Mantenimiento cintas</li>
                        <li><i class="fas fa-robot"></i> 05/05 - Demostración IA</li>
                    </ul>
                </div>
            </div>
        </div>
    </main>
</div>