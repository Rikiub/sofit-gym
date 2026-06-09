<?php
// Cada vista debe empezar con esto:
$this->layout('layout', ['title' => 'Inicio']);
// Esto ya incluye las dependencias y la barra lateral.

// TODO: Esto deberia guardarse como archivos en la carpeta "lib"
$this->pushJs('https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js', false);

// Las dependencias se resuelven automaticamente como:
// /assets/pages/inicio/inicio.css
$this->pushCss('pages/inicio/inicio.css');
$this->pushJs('pages/inicio/inicio.js');
?>

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
                    <div class="profile-dropdown">
                        <i class="fas fa-user-circle" id="profileIcon"></i>
                        <div class="profile-menu" id="profileMenu">
                            <div class="profile-header">
                                <i class="fas fa-user-circle"></i>
                                <div>
                                    <strong>Carlo Williams</strong>
                                    <small>Administrador</small>
                                </div>
                            </div>
                            <a href="#"><i class="fas fa-user"></i> Perfil</a>
                            <div class="pb-3">
                                <a href="?page=login&action=logout">
                                    <i class="fa-solid fa-right-from-bracket"></i> <span>Cerrar sesión</span>
                                </a>
                            </div>
                        </div>
                    </div>
            </nav>

            <h2 class="panel-title"><i class="fas fa-chalkboard"></i> Dashboard </h2>

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