<?php

/** @var \App\Helpers\Auth\UsuarioSessionDTO $usuario */

// Props
$asistencias ??= [];
$clientesMensuales ??= [];
$ingresosMensual ??= [];

$this->layout('layout', ['title' => 'Dashboard']);
$this->pushJs('lib/chart.js/chart.umd.min.js');
$this->pushJs('pages/dashboard/dashboard.js');
?>

<div class="container-fluid p-4">
    <main class="main-content">

        <nav class="d-flex justify-content-end mb-4">
            <div class="d-flex gap-3 align-items-center">

                <div class="position-relative" x-data="{ show: false }" @click.outside="show = false">
                    <button class="btn btn-link text-decoration-none text-white fw-semibold" @click="show = !show">
                        Generar reporte
                    </button>

                    <div class="dropdown-menu end-0 shadow border-0 p-2 rounded-4" :class="{ show: show }" style="width: 240px; max-width: 85vw;">
                        <a class="dropdown-item d-flex align-items-center gap-2 py-2 rounded-3" href="#">
                            <i class="fas fa-clipboard-list text-danger fs-5 text-center" style="width: 24px;"></i>
                            Reporte de asistencia
                        </a>

                        <a class="dropdown-item d-flex align-items-center gap-2 py-2 rounded-3" href="#">
                            <i class="fas fa-chart-line text-danger fs-5 text-center" style="width: 24px;"></i>
                            Reporte financiero
                        </a>

                        <a class="dropdown-item d-flex align-items-center gap-2 py-2 rounded-3" href="#">
                            <i class="fas fa-users text-danger fs-5 text-center" style="width: 24px;"></i>
                            Reporte de clientes
                        </a>

                        <a class="dropdown-item d-flex align-items-center gap-2 py-2 rounded-3" href="#">
                            <i class="fas fa-boxes text-danger fs-5 text-center" style="width: 24px;"></i>
                            Reporte de inventario
                        </a>
                    </div>
                </div>

                <i class="fas fa-bell text-white fs-5 cursor-pointer hover-lift"></i>

                <div class="position-relative" x-data="menu('<?= $usuario->id ?>')" @click.outside="show = false" @form-success="refresh()">
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
                            <button class="btn btn-link dropdown-item d-flex align-items-center gap-2 py-2 rounded-3" @click="$dispatch('open-modal', { id: 'usuarios', dataId: '<?= $usuario->id ?>', mode: 'edit' })">
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

        <h2 class="fs-3 fw-bold text-white mb-4 d-flex align-items-center gap-2" style="text-shadow: 0 1px 3px rgba(0,0,0,0.3);">
            <i class="fas fa-chalkboard"></i> Panel de control
        </h2>

        <div class="row g-3 mb-5">
            <div class="col-12 col-sm-6 col-md-4">
                <a class="glass-card d-block p-4 rounded-4 text-center text-decoration-none text-white h-100 hover-lift" href="?page=clientes">
                    <h4 class="fs-6 fw-semibold text-white mb-2">Clientes del mes</h4>
                    <div class="display-6 fw-bold text-danger my-2"><?= count($clientesMensuales) ?></div>
                    <span class="small text-white-50">Membresías activas</span>
                </a>
            </div>

            <div class="col-12 col-sm-6 col-md-4">
                <a class="glass-card d-block p-4 rounded-4 text-center text-decoration-none text-white h-100 hover-lift" href="?page=asistencia">
                    <h4 class="fs-6 fw-semibold text-white mb-2">Asistencias de hoy</h4>
                    <div class="display-6 fw-bold text-danger my-2"><?= count($asistencias) ?></div>
                    <span class="small text-white-50">Registradas</span>
                </a>
            </div>

            <div class="col-12 col-sm-6 col-md-4">
                <a
                    class="glass-card d-block p-4 rounded-4 text-center text-decoration-none text-white h-100 hover-lift" href="?page=facturacion&tab=tab-lista">
                    <h4 class="fs-6 fw-semibold text-white mb-2">Ingresos mensuales</h4>
                    <div class="display-6 fw-bold text-danger my-2">$<?= round($ingresosMensual["total_ingresado"]) ?></div>
                    <span class="small text-white-50">Ganancias totales</span>
                </a>
            </div>
        </div>

        <h2 class="fs-3 fw-bold text-white mb-4 d-flex align-items-center gap-2" style="text-shadow: 0 1px 3px rgba(0,0,0,0.3);">
            <i class="fas fa-chalkboard"></i> Estadísticas
        </h2>

        <div class="row g-3">
            <div class="col-12 col-md">
                <div class="glass-card p-4 rounded-4 h-100 hover-lift" x-data="asistenciasChart">
                    <h3 class="fs-5 text-white mb-3">
                        <i class="fas fa-chart-line text-danger me-2"></i>
                        Asistencias esta semana
                    </h3>
                    <canvas class="bg-white rounded-3 p-2 w-100" x-ref="canvas"></canvas>
                </div>
            </div>

            <div class="col-12 col-md">
                <div class="glass-card p-4 rounded-4 h-100 hover-lift" x-data="asistenciasChart">
                    <h3 class="fs-5 text-white mb-3">
                        <i class="fas fa-chart-line text-danger me-2"></i>
                        Ingresos esta semana
                    </h3>
                    <canvas class="bg-white rounded-3 p-2 w-100" x-ref="canvas"></canvas>
                </div>
            </div>
        </div>
    </main>
</div>

<style>
    /* CSS ESTRICTO: Únicamente lo que Bootstrap NO provee en sus utilidades básicas */
    .glass-card {
        background: rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.3);
    }

    .hover-lift {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .hover-lift:hover {
        transform: translateY(-3px) !important;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2) !important;
    }

    .cursor-pointer {
        cursor: pointer;
    }

    .dropdown-item:hover {
        background-color: #fef2f2 !important;
    }
</style>