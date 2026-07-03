<?php
$this->pushJs("pages/facturacion/facturacion.js");
$this->layout("layout", ["title" => "Reporte Financiero"]);
?>

<div class="content-area">
    <div class="container py-5">

        <div class="card-panel reporte-financiero">
            <h5><i class="fas fa-file-invoice-dollar"></i> Reporte Financiero de Facturación</h5>
            <p class="text-muted small mb-4">Seleccione el período (Mes y Año) para el filtrado de pagos del gimnasio o presione descargar directamente para obtener la información seleccionada.</p>

            <form method="GET" action="" target="_blank">
                <!-- Parámetros requeridos por el enrutador de Sofit Gym -->
                <input type="hidden" name="page" value="facturacion">
                <input type="hidden" name="action" value="reporte">

                <div class="row">
                    <!-- Selector de Mes -->
                    <div class="col-6 mb-3">
                        <label for="mes" class="form-label text-start d-block">Seleccionar Mes:</label>
                        <select class="form-select form-control" id="mes" name="mes">
                            <option value="">-- Todos los Meses --</option>
                            <?php
                            $meses = [
                                "01" => "Enero",
                                "02" => "Febrero",
                                "03" => "Marzo",
                                "04" => "Abril",
                                "05" => "Mayo",
                                "06" => "Junio",
                                "07" => "Julio",
                                "08" => "Agosto",
                                "09" => "Septiembre",
                                "10" => "Octubre",
                                "11" => "Noviembre",
                                "12" => "Diciembre"
                            ];
                            $mesActual = date('m');
                            foreach ($meses as $num => $nombre):
                                $selected = ($num === $mesActual) ? 'selected' : '';
                            ?>
                                <option value="<?php echo $num; ?>" <?php echo $selected; ?>>
                                    <?php echo $nombre; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Selector de Año -->
                    <div class="col-6 mb-3">
                        <label for="anio" class="form-label text-start d-block">Año:</label>
                        <input type="number"
                            class="form-control"
                            id="anio"
                            name="anio"
                            value="<?php echo date('Y'); ?>"
                            min="2020"
                            max="<?php echo date('Y') + 5; ?>"
                            required>
                    </div>
                </div>

                <!-- Botón Principal de Descarga -->
                <div class="row mt-3">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary w-100 py-2">
                            <i class="fas fa-file-pdf"></i> Generar Reporte PDF
                        </button>
                    </div>
                </div>
            </form>

            <hr class="my-4" style="border-color: #e9ecef;">

            <!-- Enlaces y Accesos Rápidos de Descarga -->
            <div class="text-center">
                <?php
                // Calculamos de manera dinámica los parámetros del mes en curso y el año en curso
                $mesHoy = date('m');
                $anioHoy = date('Y');
                ?>
                <div class="mb-2">
                    <a href="?page=facturacion&action=reporte&mes=<?php echo $mesHoy; ?>&anio=<?php echo $anioHoy; ?>"
                        target="_blank"
                        class="btn btn-outline-primary btn-sm w-100 mb-2">
                        <i class="fas fa-calendar-day"></i> Descargar Reporte del Mes Actual (<?php echo $meses[$mesHoy]; ?>)
                    </a>
                </div>
                <div>
                    <a href="?page=facturacion&action=reporte&mes=&anio=<?php echo $anioHoy; ?>"
                        target="_blank"
                        class="btn btn-outline-secondary btn-sm w-100">
                        <i class="fas fa-calendar-alt"></i> Descargar Reporte Consolidado Anual (<?php echo $anioHoy; ?>)
                    </a>
                </div>
            </div>
        </div>

    </div>
</div>

<style>
    .card-panel {
        align-items: center;
        justify-content: center;
        margin-left: 270px;
        /* Ajuste para el menú lateral estático de Sofit Gym */
        background: #ffffff;
        padding: 30px;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    }

    .reporte-financiero {
        width: 550px;
        margin-bottom: 10px;
    }

    .reporte-financiero h5 {
        color: #16336b;
        /* Color institucional Azul Sofit Gym */
        font-weight: bold;
        margin-bottom: 15px;
    }

    .form-label {
        font-weight: 600;
        color: #495057;
        font-size: 14px;
    }

    .btn-primary {
        background-color: #16336b;
        border-color: #16336b;
    }

    .btn-primary:hover {
        background-color: #112753;
        border-color: #112753;
    }

    .btn-outline-primary {
        color: #16336b;
        border-color: #16336b;
    }

    .btn-outline-primary:hover {
        background-color: #16336b;
        color: #ffffff;
    }

    /* Responsive */
    @media (max-width: 991px) {
        .card-panel {
            margin-left: 0;
            /* Colapsar el margen si el menú lateral se oculta en móviles */
            width: 100%;
        }
    }
</style>