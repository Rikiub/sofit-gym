<?php
$this->pushJs("pages/asistencia/asistencia.js");
$this->layout("layout", ["title" => "Reporte de Asistencias"]);
?>

<div class="content-area">
    <div class="container py-5">

        <div class="card-panel reporte-asistencias">
            <h5><i class="fas fa-calendar-check"></i> Reporte Histórico de Asistencias</h5>
            <p class="text-muted small mb-4">Seleccione un rango de fechas opcional para filtrar los ingresos de los clientes al gimnasio o presione descargar directamente para obtener el historial completo.</p>

            <form method="GET" action="" target="_blank">
                <input type="hidden" name="page" value="asistencia">
                <input type="hidden" name="action" value="generarReporte">

                <div class="row">
                    <div class="col-6 mb-3">
                        <label for="inicio" class="form-label text-start d-block">Fecha Inicio:</label>
                        <input type="date" class="form-control" id="inicio" name="inicio">
                    </div>
                    <div class="col-6 mb-3">
                        <label for="fin" class="form-label text-start d-block">Fecha Fin:</label>
                        <input type="date" class="form-control" id="fin" name="fin">
                    </div>
                </div>

                <div class="d-flex gap-2 mt-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-file-pdf"></i> Descargar Reporte PDF
                    </button>
                </div>
            </form>

            <hr class="my-4">

            <div class="text-center">
                <?php
                // Calculamos las fechas del semestre actual dinámicamente para el botón rápido
                $primerDiaSemestre = date('Y-m-d', strtotime('-6 months'));
                $hoy = date('Y-m-d');
                ?>
                <a href="?page=asistencia&action=generarReporte&inicio=<?php echo $primerDiaSemestre; ?>&fin=<?php echo $hoy; ?>"
                    target="_blank" \
                    class="btn btn-outline-primary btn-sm w-100">
                    <i class="fas fa-calendar-alt"></i> Descargar Reporte Semestral Automático
                </a>
            </div>
        </div>

    </div>
</div>

<style>
    .card-panel {
        align-items: center;
        justify-content: center;
        margin-left: 270px;
        /* Ajuste del menú lateral Sofit Gym */
        background: #ffffff;
        padding: 30px;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    }

    .reporte-asistencias {
        width: 550px;
        margin-bottom: 10px;
    }

    .reporte-asistencias h5 {
        color: #16336b;
        /* Color corporativo de Sofit Gym */
        font-weight: bold;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .btn-primary {
        background-color: #16336b;
        border-color: #16336b;
    }

    .btn-primary:hover {
        background-color: #112752;
        border-color: #112752;
    }

    .btn-outline-primary {
        color: #16336b;
        border-color: #16336b;
    }

    .btn-outline-primary:hover {
        background-color: #16336b;
        color: #ffffff;
    }
</style>