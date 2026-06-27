<?php

namespace App\Core\Reportes;

use Fpdf\Fpdf;

class reporteProductosMasVendidos extends Fpdf
{
    // Colores corporativos adaptados del modelo base
    private array $colorPrincipal = [22, 51, 107];  // Azul Sofit Gym / UPTAEB
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
        $this->Cell(0, 8, utf8_decode("REPORTE DE PRODUCTOS MÁS VENDIDOS"), 0, 1, 'C');

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
     * Genera la estructura del reporte en base a los datos provistos por el Modelo
     *
     * @param array $datosProductos Listado obtenido de la base de datos
     * @param string|null $fechaInicio Fecha inicial del filtro (opcional)
     * @param string|null $fechaFin Fecha final del filtro (opcional)
     */
    public function crearReporte(array $datosProductos, ?string $fechaInicio = null, ?string $fechaFin = null)
    {
        $this->AliasNbPages();
        $this->AddPage('P'); // Formato Vertical (P) es ideal para listados detallados de productos

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
        $this->Cell(40, 7, utf8_decode("Total Productos: "), 0, 0, 'L');
        $this->SetFont('Arial', '', 10);
        $this->SetTextColor(50, 50, 50);
        $this->Cell(0, 7, count($datosProductos), 0, 1, 'L');

        $this->Ln(5);

        // --- ENCABEZADOS DE LA TABLA ---
        // Ancho total disponible en vertical: ~180mm (Margen de 15mm por lado en 210mm total)
        $w = [25, 65, 30, 30, 30];

        $this->SetFont('Arial', 'B', 10);
        $this->SetFillColor($this->colorPrincipal[0], $this->colorPrincipal[1], $this->colorPrincipal[2]);
        $this->SetTextColor(255, 255, 255); // Texto blanco
        $this->SetDrawColor($this->colorPrincipal[0], $this->colorPrincipal[1], $this->colorPrincipal[2]);

        $this->Cell($w[0], 8, utf8_decode('Código'), 1, 0, 'C', true);
        $this->Cell($w[1], 8, utf8_decode('Nombre Producto'), 1, 0, 'L', true);
        $this->Cell($w[2], 8, utf8_decode('Cant. Vendida'), 1, 0, 'C', true);
        $this->Cell($w[3], 8, utf8_decode('Precio Prom.'), 1, 0, 'R', true);
        $this->Cell($w[4], 8, utf8_decode('Total Recaudado'), 1, 1, 'R', true);

        // --- RENDERIZADO DE FILAS DE DATOS ---
        $this->SetFont('Arial', '', 9);
        $this->SetTextColor(50, 50, 50);
        $this->SetDrawColor($this->colorLinea[0], $this->colorLinea[1], $this->colorLinea[2]);

        $fill = false;
        $totalGeneralRecaudado = 0;
        $totalUnidadesVendidas = 0;

        foreach ($datosProductos as $row) {
            // Respaldamos coordenadas para controlar saltos de página fluidos si hay MultiCell
            $line_height = 7;

            // Alternar color de fondo
            $this->SetFillColor($this->colorFondo[0], $this->colorFondo[1], $this->colorFondo[2]);

            // Dibujar celdas normales
            $this->Cell($w[0], $line_height, utf8_decode($row['codigo_producto']), 'B', 0, 'C', $fill);
            $this->Cell($w[1], $line_height, utf8_decode($row['nombre_producto']), 'B', 0, 'L', $fill);
            $this->Cell($w[2], $line_height, number_format($row['total_vendido'], 0, ',', '.'), 'B', 0, 'C', $fill);
            $this->Cell($w[3], $line_height, number_format($row['precio_unitario_promedio'], 2, ',', '.') . ' $', 'B', 0, 'R', $fill);
            $this->Cell($w[4], $line_height, number_format($row['ingreso_total'], 2, ',', '.') . ' $', 'B', 1, 'R', $fill);

            // Sumas globales para el pie del reporte
            $totalUnidadesVendidas += $row['total_vendido'];
            $totalGeneralRecaudado += $row['ingreso_total'];

            $fill = !$fill; // Intercambiar estado del fondo
        }

        // --- FILA DE TOTALES GENERALES ---
        $this->Ln(2);
        $this->SetFont('Arial', 'B', 10);
        $this->SetTextColor($this->colorPrincipal[0], $this->colorPrincipal[1], $this->colorPrincipal[2]);

        // Celdas unificadas de etiquetas de total
        $this->Cell($w[0] + $w[1], 8, utf8_decode('TOTALES GENERALES:'), 0, 0, 'R');
        $this->Cell($w[2], 8, number_format($totalUnidadesVendidas, 0, ',', '.'), 'B', 0, 'C');
        $this->Cell($w[3], 8, '', 0, 0, 'R'); // Vacío
        $this->Cell($w[4], 8, number_format($totalGeneralRecaudado, 2, ',', '.') . ' $', 'B', 1, 'R');
    }
}
