<?php
// Añadimos un query string con marca de tiempo o versión estática para romper la caché del navegador
$this->pushJs("pages/clientes/clientes.js?v=" . time());

$this->layout("layout", [["title" => "Reporte de Clientes"]]);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reportes | Clientes</title>
</head>

<body>
    <div class="content-area">
        <div class="container py-5">

            <div class="card-panel reporte-clientes">
                <h5><i class="fas fa-users"></i> Reporte General de Clientes</h5>
                <p class="text-muted small mb-4">Seleccione un estado de membresía específico si desea filtrar los resultados o presione descargar directamente para obtener el listado completo.</p>
                
                <form method="GET" action="" target="_blank">
                    <input type="hidden" name="page" value="clientes">
                    <input type="hidden" name="action" value="reporteGeneral">

                    <div class="row">
                        <div class="col-12 mb-4">
                            <label for="estado" class="form-label text-start d-block">Filtrar por Estado de Membresía:</label>
                            <select class="form-select" id="estado" name="estado">
                                <option value="">-- Todos los Clientes --</option>
                                <option value="Activo">Solo Activos / Vigentes</option>
                                <option value="Inactivo">Solo Inactivos / Vencidos</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary w-100 mb-2">
                                <i class="fas fa-file-pdf"></i> Generar Reporte PDF
                            </button>
                        </div>
                    </div>
                </form>

                <hr class="text-muted">

                <div class="text-center">
                    <a href="?page=clientes&action=reporteGeneral&estado=Activo" 
                       target="_blank" 
                       class="btn btn-outline-success btn-sm w-100">
                        <i class="fas fa-user-check"></i> Descargar Reporte de Clientes Activos
                    </a>
                </div>
            </div>

        </div>
    </div>

    <style>
        .card-panel {
            align-items: center;
            justify-content: center;
            margin-left: 270px; /* Ajuste milimétrico para el menú lateral de SOFIT GYM */
            background: #ffffff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }

        .reporte-clientes {
            width: 550px;
            margin-bottom: 10px;
        }

        .reporte-clientes h5 {
            color: #16336b; /* Color corporativo Azul de Sofit Gym */
            font-weight: bold;
            margin-bottom: 15px;
        }

        .form-label {
            font-weight: 600;
            color: #495057;
        }
    </style>
</body>

</html>