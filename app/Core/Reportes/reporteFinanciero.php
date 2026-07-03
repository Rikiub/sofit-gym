<?php

namespace App\Core\Reportes;

use Fpdf\Fpdf;

class ReporteFinanciero extends Fpdf
{
    // Colores corporativos adaptados del modelo base de Sofit Gym
    private array $colorPrincipal = [22, 51, 107];   // Azul Sofit Gym / UPTAEB
    private array $colorFondo     = [240, 240, 240]; // Gris claro para filas alternas
    private array $colorLinea     = [200, 200, 200]; // Líneas sutiles
    private array $colorDestacado = [39, 174, 96];   // Verde para montos e indicadores positivos

    // Filtros de fecha y metadata para la cabecera
    private ?string $mesReporte  = null;
    private ?string $anioReporte = null;
    private string $tipoReporte  = 'MENSUAL';

    /**
     * Configura el período a mostrar en el encabezado
     */
    public function setPeriodo(?string $mes, ?string $anio, string $tipo = 'MENSUAL')
    {
        $this->mesReporte = $mes;
        $this->anioReporte = $anio;
        $this->tipoReporte = $tipo;
    }

    /**
     * Encabezado del reporte
     */
    public function Header()
    {
        date_default_timezone_set("America/Caracas");
        $horaHoy = date("h:i A");
        $fechaHoy = date("d/m/Y");

        // 1. Título principal del Reporte
        $this->SetFont('Times', 'B', 18);
        $this->SetTextColor($this->colorPrincipal[0], $this->colorPrincipal[1], $this->colorPrincipal[2]);
        $this->SetY(15);
        $this->Cell(0, 8, utf8_decode("REPORTE FINANCIERO DE INGRESOS"), 0, 1, 'C');

        // 2. Subtítulo con el tipo y período correspondiente
        $this->SetFont('Times', 'I', 11);
        $this->SetTextColor(100, 100, 100);

        $subtitulo = "INGRESOS DE TODOS LOS TIEMPOS";
        if ($this->tipoReporte === 'MENSUAL' && $this->mesReporte && $this->anioReporte) {
            $meses = [
                '01' => 'ENERO',
                '02' => 'FEBRERO',
                '03' => 'MARZO',
                '04' => 'ABRIL',
                '05' => 'MAYO',
                '06' => 'JUNIO',
                '07' => 'JULIO',
                '08' => 'AGOSTO',
                '09' => 'SEPTIEMBRE',
                '10' => 'OCTUBRE',
                '11' => 'NOVIEMBRE',
                '12' => 'DICIEMBRE'
            ];
            $mesNombre = $meses[$this->mesReporte] ?? strtoupper($this->mesReporte);
            $subtitulo = "INGRESOS MENSUALES DE " . $mesNombre . " " . $this->anioReporte;
        } elseif ($this->anioReporte) {
            $subtitulo = "INGRESOS ANUALES - EJERCICIO " . $this->anioReporte;
        }

        $this->Cell(0, 6, utf8_decode($subtitulo), 0, 1, 'C');
        $this->Ln(4);

        // 3. Cuadro de Información de Generación (Fecha, Hora, Sistema)
        $this->SetY(15);
        $this->SetX(15);
        $this->SetFont('Arial', '', 8);
        $this->SetTextColor(80, 80, 80);
        $this->Cell(45, 4, utf8_decode("Generado: " . $fechaHoy), 0, 1, 'L');
        $this->SetX(15);
        $this->Cell(45, 4, utf8_decode("Hora: " . $horaHoy), 0, 1, 'L');

        $this->SetY(15);
        $this->SetX(-65);
        $this->Cell(50, 4, utf8_decode("Sistema: Sofit Gym"), 0, 1, 'R');
        $this->SetX(-65);
        $this->Cell(50, 4, utf8_decode("Módulo: Administración"), 0, 1, 'R');

        // Línea decorativa inferior en el header
        $this->SetY(34);
        $this->SetDrawColor($this->colorPrincipal[0], $this->colorPrincipal[1], $this->colorPrincipal[2]);
        $this->SetLineWidth(0.8);
        $this->Line(15, $this->GetY(), 195, $this->GetY());
        $this->Ln(6);
    }

    /**
     * Pie de página del reporte
     */
    public function Footer()
    {
        $this->SetY(-20);
        $this->SetDrawColor($this->colorLinea[0], $this->colorLinea[1], $this->colorLinea[2]);
        $this->SetLineWidth(0.2);
        $this->Line(15, $this->GetY(), 195, $this->GetY());
        $this->Ln(2);

        $this->SetFont('Arial', 'I', 8);
        $this->SetTextColor(120, 120, 120);

        // Firma de conformidad / Nota de auditoría
        $this->Cell(90, 10, utf8_decode("Sofit Gym - Control Financiero Interno"), 0, 0, 'L');
        $this->Cell(90, 10, utf8_decode("Página " . $this->PageNo() . " de {nb}"), 0, 0, 'R');
    }

    /**
     * Genera la vista principal del reporte financiero
     * @param array $pagos Lista de pagos a procesar
     */
    public function generar(array $pagos)
    {
        $this->AliasNbPages();
        $this->AddPage();
        $this->SetMargins(15, 15, 15);

        // --- CÁLCULO DE MÉTRICAS / KPIs ---
        $totalRecaudado = 0.0;
        $numTransacciones = count($pagos);
        $metodosContador = [];

        foreach ($pagos as $pago) {
            $totalRecaudado += (float) ($pago['monto'] ?? 0);
            $metodo = $pago['metodo_pago'] ?? 'Otros';
            $metodosContador[$metodo] = ($metodosContador[$metodo] ?? 0) + 1;
        }

        $promedioTransaccion = $numTransacciones > 0 ? ($totalRecaudado / $numTransacciones) : 0.0;

        // Encontrar método de pago más utilizado
        $metodoMasUsado = 'N/A';
        if (!empty($metodosContador)) {
            arsort($metodosContador);
            $metodoMasUsado = key($metodosContador);
        }

        // --- SECCIÓN DE RESUMEN (KPI CARDS) ---
        $this->SetFont('Arial', 'B', 9);
        $this->SetTextColor($this->colorPrincipal[0], $this->colorPrincipal[1], $this->colorPrincipal[2]);
        $this->Cell(0, 6, utf8_decode("INDICADORES FINANCIEROS CLAVE"), 0, 1, 'L');
        $this->Ln(2);

        $cardWidth = 42;
        $cardHeight = 18;
        $startY = $this->GetY();

        // Card 1: Total Recaudado
        $this->SetFillColor(245, 247, 250);
        $this->Rect(15, $startY, $cardWidth, $cardHeight, 'DF');
        $this->SetXY(15, $startY + 2);
        $this->SetFont('Arial', '', 7);
        $this->SetTextColor(100, 100, 100);
        $this->Cell($cardWidth, 4, utf8_decode("TOTAL RECAUDADO"), 0, 1, 'C');
        $this->SetX(15);
        $this->SetFont('Arial', 'B', 12);
        $this->SetTextColor($this->colorDestacado[0], $this->colorDestacado[1], $this->colorDestacado[2]);
        $this->Cell($cardWidth, 8, number_format($totalRecaudado, 2, ',', '.') . " $", 0, 1, 'C');

        // Card 2: Cantidad de Pagos
        $this->Rect(15 + $cardWidth + 2, $startY, $cardWidth, $cardHeight, 'DF');
        $this->SetXY(15 + $cardWidth + 2, $startY + 2);
        $this->SetFont('Arial', '', 7);
        $this->SetTextColor(100, 100, 100);
        $this->Cell($cardWidth, 4, utf8_decode("TRANSACCIONES"), 0, 1, 'C');
        $this->SetX(15 + $cardWidth + 2);
        $this->SetFont('Arial', 'B', 12);
        $this->SetTextColor($this->colorPrincipal[0], $this->colorPrincipal[1], $this->colorPrincipal[2]);
        $this->Cell($cardWidth, 8, $numTransacciones, 0, 1, 'C');

        // Card 3: Ticket Promedio
        $this->Rect(15 + ($cardWidth * 2) + 4, $startY, $cardWidth, $cardHeight, 'DF');
        $this->SetXY(15 + ($cardWidth * 2) + 4, $startY + 2);
        $this->SetFont('Arial', '', 7);
        $this->SetTextColor(100, 100, 100);
        $this->Cell($cardWidth, 4, utf8_decode("MONTO PROMEDIO"), 0, 1, 'C');
        $this->SetX(15 + ($cardWidth * 2) + 4);
        $this->SetFont('Arial', 'B', 12);
        $this->SetTextColor($this->colorPrincipal[0], $this->colorPrincipal[1], $this->colorPrincipal[2]);
        $this->Cell($cardWidth, 8, number_format($promedioTransaccion, 2, ',', '.') . " $", 0, 1, 'C');

        // Card 4: Método de Pago Estrella
        $this->Rect(15 + ($cardWidth * 3) + 6, $startY, $cardWidth, $cardHeight, 'DF');
        $this->SetXY(15 + ($cardWidth * 3) + 6, $startY + 2);
        $this->SetFont('Arial', '', 7);
        $this->SetTextColor(100, 100, 100);
        $this->Cell($cardWidth, 4, utf8_decode("MÉTODO PREFERIDO"), 0, 1, 'C');
        $this->SetX(15 + ($cardWidth * 3) + 6);
        $this->SetFont('Arial', 'B', 11);
        $this->SetTextColor($this->colorPrincipal[0], $this->colorPrincipal[1], $this->colorPrincipal[2]);
        $this->Cell($cardWidth, 8, utf8_decode($metodoMasUsado), 0, 1, 'C');

        $this->SetY($startY + $cardHeight + 6);

        // --- TABLA DE DETALLES ---
        $this->SetFont('Arial', 'B', 9);
        $this->SetTextColor($this->colorPrincipal[0], $this->colorPrincipal[1], $this->colorPrincipal[2]);
        $this->Cell(0, 6, utf8_decode("DETALLE DE TRANSACCIONES RECIBIDAS"), 0, 1, 'L');
        $this->Ln(1);

        // Definición de anchos de columna (Total: 180mm para cumplir con márgenes de 15mm en A4 de 210mm)
        $w = [25, 25, 65, 35, 30]; // Fecha, Cédula, Nombre Cliente, Método, Monto
        $line_height = 6.5;

        // Cabecera de la Tabla
        $this->SetFont('Arial', 'B', 9);
        $this->SetFillColor($this->colorPrincipal[0], $this->colorPrincipal[1], $this->colorPrincipal[2]);
        $this->SetTextColor(255, 255, 255);
        $this->SetDrawColor($this->colorPrincipal[0], $this->colorPrincipal[1], $this->colorPrincipal[2]);

        $this->Cell($w[0], 7, utf8_decode("FECHA"), 1, 0, 'C', true);
        $this->Cell($w[1], 7, utf8_decode("CÉDULA"), 1, 0, 'C', true);
        $this->Cell($w[2], 7, utf8_decode("CLIENTE"), 1, 0, 'L', true);
        $this->Cell($w[3], 7, utf8_decode("MÉTODO DE PAGO"), 1, 0, 'C', true);
        $this->Cell($w[4], 7, utf8_decode("MONTO ($)"), 1, 1, 'R', true);

        // Cuerpo de la Tabla
        $this->SetFont('Arial', '', 8.5);
        $this->SetTextColor(50, 50, 50);
        $this->SetDrawColor($this->colorLinea[0], $this->colorLinea[1], $this->colorLinea[2]);

        $fill = false;
        if (empty($pagos)) {
            $this->Cell(array_sum($w), 10, utf8_decode("No se registraron pagos en este período."), 1, 1, 'C');
        } else {
            foreach ($pagos as $row) {
                // Validación para evitar saltos de página huérfanos
                if ($this->GetY() + $line_height > 265) {
                    $this->AddPage();
                    // Reimprimir cabecera de la tabla en nueva página
                    $this->SetFont('Arial', 'B', 9);
                    $this->SetFillColor($this->colorPrincipal[0], $this->colorPrincipal[1], $this->colorPrincipal[2]);
                    $this->SetTextColor(255, 255, 255);
                    $this->Cell($w[0], 7, utf8_decode("FECHA"), 1, 0, 'C', true);
                    $this->Cell($w[1], 7, utf8_decode("CÉDULA"), 1, 0, 'C', true);
                    $this->Cell($w[2], 7, utf8_decode("CLIENTE"), 1, 0, 'L', true);
                    $this->Cell($w[3], 7, utf8_decode("MÉTODO DE PAGO"), 1, 0, 'C', true);
                    $this->Cell($w[4], 7, utf8_decode("MONTO ($)"), 1, 1, 'R', true);
                    $this->SetFont('Arial', '', 8.5);
                    $this->SetTextColor(50, 50, 50);
                }

                $this->SetFillColor($this->colorFondo[0], $this->colorFondo[1], $this->colorFondo[2]);

                // Formatear Fecha (Y-m-d -> d/m/Y)
                $fechaFormateada = date("d/m/Y", strtotime($row['fecha_pago'] ?? $row['fecha'] ?? 'now'));

                // Cédula y Cliente
                $cedula = $row['cedula_cliente'] ?? $row['cedula'] ?? 'N/P';
                $nombreCliente = $row['nombre'] ?? $row['nombre_cliente'] ?? 'Cliente General';
                $metodoPago = $row['metodo_pago'] ?? 'Efectivo';
                $montoValor = (float) ($row['monto'] ?? 0);

                $this->Cell($w[0], $line_height, $fechaFormateada, 'B', 0, 'C', $fill);
                $this->Cell($w[1], $line_height, utf8_decode($cedula), 'B', 0, 'C', $fill);
                $this->Cell($w[2], $line_height, utf8_decode($nombreCliente), 'B', 0, 'L', $fill);
                $this->Cell($w[3], $line_height, utf8_decode($metodoPago), 'B', 0, 'C', $fill);
                $this->Cell($w[4], $line_height, number_format($montoValor, 2, ',', '.') . ' $', 'B', 1, 'R', $fill);

                $fill = !$fill; // Alternar color
            }
        }

        // --- FILA DE TOTALES GENERALES ---
        $this->Ln(2);
        $this->SetFont('Arial', 'B', 9.5);
        $this->SetTextColor($this->colorPrincipal[0], $this->colorPrincipal[1], $this->colorPrincipal[2]);

        // Sumar espacio de columnas para alinear el total
        $anchoEtiqueta = $w[0] + $w[1] + $w[2] + $w[3];
        $this->Cell($anchoEtiqueta, 7, utf8_decode("INGRESOS TOTALES RECAUDADOS:"), 0, 0, 'R');
        $this->SetTextColor($this->colorDestacado[0], $this->colorDestacado[1], $this->colorDestacado[2]);
        $this->Cell($w[4], 7, number_format($totalRecaudado, 2, ',', '.') . ' $', 0, 1, 'R');

        // Línea doble decorativa de cierre de totales
        $finalY = $this->GetY();
        $this->SetDrawColor($this->colorDestacado[0], $this->colorDestacado[1], $this->colorDestacado[2]);
        $this->SetLineWidth(0.5);
        $this->Line(15 + $anchoEtiqueta, $finalY, 195, $finalY);
        $this->Line(15 + $anchoEtiqueta, $finalY + 0.8, 195, $finalY + 0.8);
    }
}
