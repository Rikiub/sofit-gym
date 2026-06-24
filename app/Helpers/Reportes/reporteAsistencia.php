<?php

namespace App\Helpers\Reportes;

use Fpdf\Fpdf;

class reporteAsistencia extends Fpdf
{
    // Colores corporativos adaptados del modelo base (Sofit Gym)
    private array $colorPrincipal = [22, 51, 107];  // Azul Sofit Gym
    private array $colorFondo     = [240, 240, 240]; // Gris claro para filas alternas
    private array $colorLinea     = [200, 200, 200]; // Líneas sutiles

    public function Header()
    {
        date_default_timezone_set("America/Caracas");
        $horaHoy = date("h:i A");
        $fechaHoy = date("d/m/Y");
        $margin = 15; // Margen en mm

        // 1. Título principal del Reporte
        $this->SetFont('Times', 'B', 18);
        $this->SetTextColor($this->colorPrincipal[0], $this->colorPrincipal[1], $this->colorPrincipal[2]);
        $this->SetY(15);
        $this->Cell(0, 8, utf8_decode("REPORTE HISTÓRICO DE ASISTENCIAS"), 0, 1, 'C');

        // 2. Subtítulo con fecha y hora de generación
        $this->SetFont('Times', '', 9);
        $this->SetTextColor(120, 120, 120);
        $this->SetY(24);
        $this->Cell(0, 5, utf8_decode("Generado: $fechaHoy a las $horaHoy"), 0, 1, 'R');

        // 3. Línea Separadora decorativa
        $this->SetDrawColor($this->colorPrincipal[0], $this->colorPrincipal[1], $this->colorPrincipal[2]);
        $this->SetLineWidth(0.5);
        $this->Line($margin, $this->GetY() + 2, $this->GetPageWidth() - $margin, $this->GetY() + 2);

        $this->Ln(8);
    }

    public function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->SetTextColor(100, 100, 100);
        $this->Cell(0, 10, utf8_decode('Página ' . $this->PageNo() . '/{nb}'), 0, 0, 'C');
    }

    /**
     * Genera la estructura del reporte de asistencias en base a los datos provistos
     *
     * @param array $datosAsistencia Listado obtenido de la base de datos (JOIN de asistencia, cliente y persona)
     * @param string|null $fechaInicio Fecha inicial del filtro (opcional)
     * @param string|null $fechaFin Fecha final del filtro (opcional)
     */
    public function crearReporte(array $datosAsistencia, ?string $fechaInicio = null, ?string $fechaFin = null)
    {
        $this->AliasNbPages();
        $this->AddPage('P'); // Formato Vertical (P)

        // --- SECCIÓN DE FILTROS/METADATOS ---
        $this->SetDrawColor($this->colorLinea[0], $this->colorLinea[1], $this->colorLinea[2]);
        $this->SetLineWidth(0.2);
        $this->SetFillColor($this->colorFondo[0], $this->colorFondo[1], $this->colorFondo[2]);

        $this->SetFont('Arial', 'B', 10);
        $this->SetTextColor($this->colorPrincipal[0], $this->colorPrincipal[1], $this->colorPrincipal[2]);

        $rangoFecha = "Todos los registros";
        if (!empty($fechaInicio) && !empty($fechaFin)) {
            $rangoFecha = date("d/m/Y", strtotime($fechaInicio)) . " hasta " . date("d/m/Y", strtotime($fechaFin));
        }

        $this->Cell(40, 7, utf8_decode("Período de Filtro: "), 0, 0, 'L');
        $this->SetFont('Arial', '', 10);
        $this->SetTextColor(50, 50, 50);
        $this->Cell(0, 7, utf8_decode($rangoFecha), 0, 1, 'L');

        $this->SetFont('Arial', 'B', 10);
        $this->SetTextColor($this->colorPrincipal[0], $this->colorPrincipal[1], $this->colorPrincipal[2]);
        $this->Cell(40, 7, utf8_decode("Total Asistencias: "), 0, 0, 'L');
        $this->SetFont('Arial', '', 10);
        $this->SetTextColor(50, 50, 50);
        $this->Cell(0, 7, count($datosAsistencia), 0, 1, 'L');

        $this->Ln(5);

        // --- ENCABEZADOS DE LA TABLA ---
        // Ancho total disponible en vertical: ~180mm (Margen de 15mm por lado en 210mm total)
        // Distribución optimizada: Cédula (30mm), Cliente/Nombre completo (75mm), Fecha (35mm), Hora Entrada (40mm)
        $w = [30, 75, 35, 40];

        $this->SetFont('Arial', 'B', 10);
        $this->SetFillColor($this->colorPrincipal[0], $this->colorPrincipal[1], $this->colorPrincipal[2]);
        $this->SetTextColor(255, 255, 255); // Texto blanco
        $this->SetDrawColor($this->colorPrincipal[0], $this->colorPrincipal[1], $this->colorPrincipal[2]);

        $this->Cell($w[0], 8, utf8_decode('Cédula'), 1, 0, 'C', true);
        $this->Cell($w[1], 8, utf8_decode('Cliente'), 1, 0, 'L', true);
        $this->Cell($w[2], 8, utf8_decode('Fecha'), 1, 0, 'C', true);
        $this->Cell($w[3], 8, utf8_decode('Hora Entrada'), 1, 1, 'C', true);

        // --- RENDERIZADO DE FILAS DE DATOS ---
        $this->SetFont('Arial', '', 9);
        $this->SetTextColor(50, 50, 50);
        $this->SetDrawColor($this->colorLinea[0], $this->colorLinea[1], $this->colorLinea[2]);

        $fill = false;

        foreach ($datosAsistencia as $row) {
            $line_height = 7;

            // Alternar color de fondo
            $this->SetFillColor($this->colorFondo[0], $this->colorFondo[1], $this->colorFondo[2]);

            // Respaldo de compatibilidad de índices según AsistenciaModel.php
            $cedula = isset($row['cedula_cliente']) ? $row['cedula_cliente'] : (isset($row['cedula']) ? $row['cedula'] : 'N/A');

            // Si el modelo ya trae el nombre concatenado como 'nombre_cliente' o individual
            if (isset($row['nombre_cliente'])) {
                $nombreCompleto = $row['nombre_cliente'];
            } else {
                $nombreCompleto = (isset($row['nombre']) ? $row['nombre'] : '') . ' ' . (isset($row['apellido']) ? $row['apellido'] : '');
            }

            // Formatear la fecha y hora desde el timestamp nativo 'fecha' de la tabla asistencia_gimnasio
            $timestamp = isset($row['fecha']) ? strtotime($row['fecha']) : time();
            $fechaFormateada = date("d/m/Y", $timestamp);
            $horaFormateada = date("h:i A", $timestamp);

            // Dibujar las celdas de la fila de asistencia
            $this->Cell($w[0], $line_height, utf8_decode($cedula), 'B', 0, 'C', $fill);
            $this->Cell($w[1], $line_height, utf8_decode(trim($nombreCompleto)), 'B', 0, 'L', $fill);
            $this->Cell($w[2], $line_height, $fechaFormateada, 'B', 0, 'C', $fill);
            $this->Cell($w[3], $line_height, $horaFormateada, 'B', 1, 'C', $fill);

            $fill = !$fill; // Intercambiar estado del fondo
        }
    }
}
