<?php
// Añadimos un query string con marca de tiempo o versión estática para romper la caché del navegador
$this->pushJs("pages/productos/productos.js?v=" . time());

$this->layout("layout", ["title" => "Reporte"]);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reportes | Productos Más Vendidos</title>
</head>

<body>
    <div class="content-area">
        <div class="container py-5">

            <div class="card-panel reporte-productos">
                <h5><i class="fas fa-box-open"></i> Reporte de Productos Más Vendidos</h5>
                <p class="text-muted small mb-4">Seleccione un rango de fechas opcional para el filtrado semestral o presione descargar directamente para obtener el historial completo.</p>
                
                <form method="GET" action="" target="_blank">
                    <input type="hidden" name="page" value="productos">
                    <input type="hidden" name="action" value="generarReporteMasVendidos">

                    <div class="row">
                        <div class="col-6 mb-3">
                            <label for="fecha_inicio" class="form-label text-start d-block">Fecha Inicio:</label>
                            <input type="date" class="form-control" name="fecha_inicio" id="fecha_inicio">
                        </div>
                        <div class="col-6 mb-3">
                            <label for="fecha_fin" class="form-label text-start d-block">Fecha Fin:</label>
                            <input type="date" class="form-control" name="fecha_fin" id="fecha_fin">
                        </div>
                    </div>

                    <div class="d-grid gap-2 mt-3">
                        <button type="submit" class="btn btn-danger btn-sm">
                            <i class="fas fa-file-pdf"></i> Generar y Descargar Reporte
                        </button>
                    </div>
                </form>

                <div class="separator-or my-3 text-center text-muted small">Ó</div>
                
                <div class="text-center">
                    <?php 
                        // Calculamos las fechas del semestre actual dinámicamente para el botón rápido
                        $primerDiaSemestre = date('Y-m-d', strtotime('-6 months'));
                        $hoy = date('Y-m-d');
                    ?>
                    <a href="?page=productos&action=generarReporteMasVendidos&fecha_inicio=<?php echo $primerDiaSemestre; ?>&fecha_fin=<?php echo $hoy; ?>" 
                       target="_blank" 
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
            margin-left: 270px; /* Ajuste del menú lateral Sofit Gym */
            background: #ffffff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }

        .reporte-productos {
            width: 550px;
            margin-bottom: 10px;
        }

        .reporte-productos h5 {
            color: #16336b; /* Tu color corporativo de Sofit Gym */
            font-weight: bold;
            margin-bottom: 10px;
        }

        .separator-or {
            position: relative;
        }
    </style>
</body>

</html>