<?php

namespace App\Core\Reportes;

use Fpdf\Fpdf;

class ReporteInventario extends Fpdf
{
    // Colores corporativos adaptados del modelo base (Azul Sofit Gym / UPTAEB)
    private array $colorPrincipal = [22, 51, 107];
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
        $this->Cell(0, 8, utf8_decode("REPORTE GENERAL DE INVENTARIO"), 0, 1, 'C');

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
     * Genera la estructura de la tabla de inventario en base a los datos provistos por el Modelo
     *
     * @param array $datosProductos Listado obtenido de ProductoModel::obtenerTodos()
     */
    public function crearReporte(array $datosProductos)
    {
        $this->AliasNbPages();
        $this->AddPage('P'); // Formato Vertical (Portrait)

        // --- SECCIÓN DE METADATOS ---
        $this->SetDrawColor($this->colorLinea[0], $this->colorLinea[1], $this->colorLinea[2]);
        $this->SetLineWidth(0.2);
        $this->SetFillColor($this->colorFondo[0], $this->colorFondo[1], $this->colorFondo[2]);
        $this->SetFont('Arial', 'B', 10);
        $this->SetTextColor($this->colorPrincipal[0], $this->colorPrincipal[1], $this->colorPrincipal[2]);

        $this->Cell(40, 7, utf8_decode(" Tipo de Reporte:"), 1, 0, 'L', true);
        $this->SetFont('Arial', '', 10);
        $this->SetTextColor(50, 50, 50);
        $this->Cell(140, 7, utf8_decode(" Inventario de Catálogo General (Productos Activos)"), 1, 1, 'L');
        $this->Ln(6);

        // --- ENCABEZADOS DE LA TABLA ---
        // Ancho total disponible: 180mm (210mm de página - 30mm de márgenes)
        $w = [25, 50, 35, 25, 20, 25];

        $this->SetFont('Arial', 'B', 9);
        $this->SetFillColor($this->colorPrincipal[0], $this->colorPrincipal[1], $this->colorPrincipal[2]);
        $this->SetTextColor(255, 255, 255); // Texto Blanco
        $this->SetDrawColor($this->colorPrincipal[0], $this->colorPrincipal[1], $this->colorPrincipal[2]);

        $this->Cell($w[0], 7, utf8_decode('Código'), 1, 0, 'C', true);
        $this->Cell($w[1], 7, utf8_decode('Descripción / Producto'), 1, 0, 'L', true);
        $this->Cell($w[2], 7, utf8_decode('Categoría'), 1, 0, 'L', true);
        $this->Cell($w[3], 7, utf8_decode('Stock Actual'), 1, 0, 'C', true);
        $this->Cell($w[4], 7, utf8_decode('Unidad'), 1, 0, 'C', true);
        $this->Cell($w[5], 7, utf8_decode('Precio Unit.'), 1, 1, 'R', true);

        // --- RENDERIZADO DE LOS DATOS ---
        $this->SetFont('Arial', '', 9);
        $this->SetTextColor(50, 50, 50);
        $this->SetDrawColor($this->colorLinea[0], $this->colorLinea[1], $this->colorLinea[2]);

        $fill = false;
        $totalProductosDistintos = 0;
        $valorTotalInventario = 0;

        foreach ($datosProductos as $row) {
            // Verificar si hay saltos de página automáticos
            if ($this->GetY() + 6 > $this->PageBreakTrigger) {
                $this->AddPage($this->CurOrientation);
                // Re-renderizar cabeceras en la nueva página
                $this->SetFont('Arial', 'B', 9);
                $this->SetFillColor($this->colorPrincipal[0], $this->colorPrincipal[1], $this->colorPrincipal[2]);
                $this->SetTextColor(255, 255, 255);
                $this->Cell($w[0], 7, utf8_decode('Código'), 1, 0, 'C', true);
                $this->Cell($w[1], 7, utf8_decode('Descripción / Producto'), 1, 0, 'L', true);
                $this->Cell($w[2], 7, utf8_decode('Categoría'), 1, 0, 'L', true);
                $this->Cell($w[3], 7, utf8_decode('Stock Actual'), 1, 0, 'C', true);
                $this->Cell($w[4], 7, utf8_decode('Unidad'), 1, 0, 'C', true);
                $this->Cell($w[5], 7, utf8_decode('Precio Unit.'), 1, 1, 'R', true);

                $this->SetFont('Arial', '', 9);
                $this->SetTextColor(50, 50, 50);
            }

            // Fondo alterno sutil para filas
            $this->SetFillColor($this->colorFondo[0], $this->colorFondo[1], $this->colorFondo[2]);

            $this->Cell($w[0], 6, utf8_decode($row['codigo_producto']), 'B', 0, 'C', $fill);
            $this->Cell($w[1], 6, utf8_decode($row['nombre']), 'B', 0, 'L', $fill);
            $this->Cell($w[2], 6, utf8_decode($row['nombre_categoria'] ?? 'Sin Categoría'), 'B', 0, 'L', $fill);
            $this->Cell($w[3], 6, number_format($row['stock_actual'], 0, ',', '.'), 'B', 0, 'C', $fill);
            $this->Cell($w[4], 6, utf8_decode($row['nombre_unidad'] ?? 'Und'), 'B', 0, 'C', $fill);
            $this->Cell($w[5], 6, number_format($row['precio_venta'], 2, ',', '.') . ' $', 'B', 1, 'R', $fill);

            // Cálculos del pie de reporte
            $totalProductosDistintos++;
            $valorTotalInventario += ($row['stock_actual'] * $row['precio_venta']);

            $fill = !$fill; // Alternar estado del fondo
        }

        // --- FILA DE TOTALES GENERALES ---
        $this->Ln(3);
        $this->SetFont('Arial', 'B', 10);
        $this->SetTextColor($this->colorPrincipal[0], $this->colorPrincipal[1], $this->colorPrincipal[2]);

        // Indicadores y sumatorias totales
        $this->Cell($w[0] + $w[1] + $w[2], 7, utf8_decode("TOTAL DE PRODUCTOS EN CATÁLOGO: $totalProductosDistintos"), 0, 0, 'L');
        $this->Cell($w[3] + $w[4], 7, utf8_decode("VALOR TOTAL:"), 0, 0, 'R');
        $this->Cell($w[5], 7, number_format($valorTotalInventario, 2, ',', '.') . ' $', 0, 1, 'R');

        // Línea de cierre doble o más gruesa bajo los totales
        $this->SetDrawColor($this->colorPrincipal[0], $this->colorPrincipal[1], $this->colorPrincipal[2]);
        $this->SetLineWidth(0.4);
        $this->Line(15, $this->GetY(), $this->GetPageWidth() - 15, $this->GetY());
    }
}
