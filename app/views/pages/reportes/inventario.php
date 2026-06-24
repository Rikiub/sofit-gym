<?php
// Añadimos un query string con marca de tiempo o versión estática para romper la caché del navegador
$this->pushJs("pages/productos/productos.js?v=" . time());

$this->layout("layout", ["title" => "Reporte de Inventario"]);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reportes | Reporte de Inventario</title>
</head>

<body>
    <div class="content-area">
        <div class="container py-5">

            <div class="card-panel reporte-productos">
                <h5><i class="fas fa-boxes"></i> Reporte de Inventario General</h5>
                <p class=\"text-muted small mb-4\">Genera de forma inmediata un listado completo del catálogo de productos del sistema. El reporte incluye descripciones, categorías registradas, existencias en tiempo real y la valorización financiera total de los activos en stock.</p>
                
                <form method="GET" action="" target="_blank">
                    <input type="hidden" name="page" value="productos">
                    <input type="hidden" name="action" value="reporteInventario">

                    <div class="d-grid gap-2 mt-4">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-file-pdf"></i> Generar Reporte General (PDF)
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>

    <style>
        .card-panel {
            align-items: center;
            justify-content: center;
            margin-left: 270px; /* Ajuste del menú lateral Sofit Gym para la consistencia visual */
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
            color: #16336b; /* Color institucional Azul Sofit Gym extraído del modelo base */
            font-weight: bold;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        /* Soporte adaptativo para resoluciones móviles y pantallas pequeñas */
        @media (max-width: 992px) {
            .card-panel {
                margin-left: 0;
                width: 100%;
            }
            .reporte-productos {
                width: 100%;
            }
        }
    </style>
</body>

</html>