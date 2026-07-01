<?php

/** @var \App\Core\Auth\UsuarioSessionDTO $usuario */

// Props
$asistencias ??= [];
$clientesMensuales ??= [];
$ingresosMensuales ??= [];

$this->layout('layout', ['title' => 'Inicio']);
$this->pushJs('lib/chart.js/chart.umd.min.js');
$this->pushJs('pages/dashboard/dashboard.js');

function headerTitle(string $icon = "", string $titulo = "")
{
    return <<<HTML
        <h2 class="fs-3 fw-bold text-white mb-4 d-flex align-items-center gap-2" style="text-shadow: 0 1px 3px rgba(0,0,0,0.3);">
            <i class="fas {$icon}"></i>
            {$titulo}
        </h2>
    HTML;
}

function cardAcceso(
    string $page,
    string $icon = "",
    string $iconClass = "",
    string $titulo = "",
    string $descripcion = ""
): string {
    return <<<HTML
        <div class="col-12 col-sm-6 col-md-3">
            <a href="?page={$page}" class="glass-card d-flex align-items-start gap-3 p-3 rounded-4 text-decoration-none text-white h-100 hover-lift">
                <div class="p-3 bg-opacity-25 rounded-3 text-info fs-3 d-flex align-items-center justify-content-center {$iconClass}" style="width: 56px; height: 56px;">
                    <i class="fas {$icon}"></i>
                </div>

                <div>
                    <h4 class="fs-6 fw-bold mb-1 text-white">{$titulo}</h4>
                    <span class="small text-white-50">{$descripcion}</span>
                </div>
            </a>
        </div>
    HTML;
}

function cardEstadistica(string $titulo, int|string $valor, string $footer)
{
    return <<<HTML
        <div class="col-12 col-sm-6 col-md-4">
            <div class="glass-card d-block p-4 rounded-4 text-center text-decoration-none text-white h-100 hover-lift">
                <h4 class="fs-6 fw-semibold text-white mb-2">{$titulo}</h4>
                <div class="display-6 fw-bold text-danger my-2">{$valor}</div>
                <span class="small text-white-50">{$footer}</span>
            </div>
        </div>
    HTML;
}
?>

<div class="container-fluid p-4">
    <main class="main-content">
        <?= $this->insert("topbar") ?>

        <section>
            <?= headerTitle("fa-rocket", "Accesos rápidos") ?>

            <div class="row g-3 mb-5">
                <?= cardAcceso(
                    page: "asistencia",
                    icon: "fa-fingerprint",
                    iconClass: "bg-info text-info",
                    titulo: "Registro de asistencias",
                    descripcion: "Control de entradas al gimnasio",
                ) ?>

                <?= cardAcceso(
                    page: "facturacion",
                    icon: "fa-coins",
                    iconClass: "bg-success text-success",
                    titulo: "Facturación y pagos",
                    descripcion: "Control de membresias y vencimientos",
                ) ?>

                <?= cardAcceso(
                    page: "clientes",
                    icon: "fa-id-card",
                    iconClass: "bg-warning text-warning",
                    titulo: "Gestión de clientes",
                    descripcion: "Información y seguimiento biometrico",
                ) ?>

                <?= cardAcceso(
                    page: "clasesGrupales",
                    icon: "fa-calendar-alt",
                    iconClass: "bg-danger text-danger",
                    titulo: "Clases grupales",
                    descripcion: "Calendario y horarios de clases",
                ) ?>
            </div>
        </section>

        <section class="row g-1">
            <?= headerTitle("fa-chart-area", "Estadisticas") ?>

            <div class="row g-3">
                <?= cardEstadistica(
                    titulo: "Asistencias de hoy",
                    valor: count($asistencias),
                    footer: "Registradas",
                ) ?>

                <?= cardEstadistica(
                    titulo: "Clientes del mes",
                    valor: count($clientesMensuales),
                    footer: "Membresías activas",
                ) ?>

                <?= cardEstadistica(
                    titulo: "Ingresos mensuales",
                    valor: "$" . round($ingresosMensuales["total_ingresado"]),
                    footer: "Ganancias totales",
                ) ?>
            </div>

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
        </section>
    </main>
</div>

<style>
    .glass-card {
        background: rgba(15, 23, 42, 0.20);
        backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.15);
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