<?php

namespace App\Core\Reportes;

use Fpdf\Fpdf;

class reporteClientes extends Fpdf
{
    // Colores corporativos adaptados del modelo base (Azul Sofit Gym)
    private array $colorPrincipal = [22, 51, 107];  // Azul institucional
    private array $colorFondo     = [240, 240, 240]; // Gris claro para filas alternas
    private array $colorLinea     = [200, 200, 200]; // Líneas sutiles de separación

    public function Header()
    {
        date_default_timezone_set("America/Caracas");
        $horaHoy = date("h:i A");
        $fechaHoy = date("d/m/Y");
        $margin = 15; // Margen general en mm

        // 1. Título principal del Reporte
        $this->SetFont('Times', 'B', 18);
        $this->SetTextColor($this->colorPrincipal[0], $this->colorPrincipal[1], $this->colorPrincipal[2]);
        $this->SetY(15);
        $this->Cell(0, 8, utf8_decode("REPORTE GENERAL DE CLIENTES"), 0, 1, 'C');

        // 2. Subtítulo con metadatos de emisión
        $this->SetFont('Times', '', 9);
        $this->SetTextColor(120, 120, 120);
        $this->SetY(24);
        $this->Cell(0, 5, utf8_decode("Generado: $fechaHoy a las $horaHoy"), 0, 1, 'R');

        // 3. Línea Decorativa Separadora
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
     * Construye y renderiza la estructura de la tabla con la información de los clientes.
     * Al estar en orientación Horizontal ('L'), se aprovecha mejor el espacio para los datos.
     *
     * @param array $clientes Arreglo de objetos ClienteDTO o arrays asociativos provenientes del modelo
     */
    public function crearReporte(array $clientes)
    {
        $this->AliasNbPages();
        // Se define en Horizontal ('L') para dar holgura a las múltiples columnas de información personal
        $this->AddPage('L');

        // --- ENCABEZADOS DE LA TABLA ---
        $this->SetFont('Arial', 'B', 10);
        $this->SetTextColor(255, 255, 255); // Texto blanco
        $this->SetFillColor($this->colorPrincipal[0], $this->colorPrincipal[1], $this->colorPrincipal[2]); // Fondo azul

        // Anchos proporcionales de columnas (Suman 267mm, óptimo para márgenes de 15mm en A4 Horizontal)
        $w = [25, 60, 55, 32, 45, 50];

        $this->Cell($w[0], 7, utf8_decode('Cédula'), 1, 0, 'C', true);
        $this->Cell($w[1], 7, utf8_decode('Nombre Completo'), 1, 0, 'L', true);
        $this->Cell($w[2], 7, utf8_decode('Correo Electrónico'), 1, 0, 'L', true);
        $this->Cell($w[3], 7, utf8_decode('Teléfono'), 1, 0, 'C', true);
        $this->Cell($w[4], 7, utf8_decode('Membresía Activa'), 1, 0, 'L', true);
        $this->Cell($w[5], 7, utf8_decode('Estado Membresía'), 1, 1, 'C', true);

        // --- CUERPO DE DATOS ---
        $this->SetFont('Arial', '', 9);
        $this->SetTextColor(50, 50, 50); // Color de texto oscuro suave

        $fill = false; // Alternancia de fondo
        $line_height = 6.5;

        // Contadores estadísticos para el pie de tabla
        $totalClientes = 0;
        $clientesActivos = 0;

        foreach ($clientes as $cliente) {
            $membresia = (array)$cliente->membresia;
            $cliente = (array)$cliente;

            $cedula = $cliente['cedula'] ?? "";
            $nombre = ($cliente['nombre'] ?? '') . ' ' . ($cliente['apellido'] ?? '');
            $correo = $cliente['correo'] ?? 'N/A';
            $telefono = $cliente['telefono'] ?? 'N/A';

            $membresiaTipo = $membresia["tipo"] ?? 'Ninguna';
            $membresiaEstado = $membresia["estado"] ?? 'Inactivo';

            // Fondo alterno sutil
            if ($fill) {
                $this->SetFillColor($this->colorFondo[0], $this->colorFondo[1], $this->colorFondo[2]);
            } else {
                $this->SetFillColor(255, 255, 255);
            }

            // Renderizado de las celdas de datos con tratamiento de bordes e intercambio de filas
            $this->Cell($w[0], $line_height, utf8_decode($cedula), 'B', 0, 'C', true);
            $this->Cell($w[1], $line_height, utf8_decode($nombre), 'B', 0, 'L', true);
            $this->Cell($w[2], $line_height, utf8_decode($correo), 'B', 0, 'L', true);
            $this->Cell($w[3], $line_height, utf8_decode($telefono), 'B', 0, 'C', true);
            $this->Cell($w[4], $line_height, utf8_decode($membresiaTipo), 'B', 0, 'L', true);

            // Resaltado condicional para estados visuales del estatus
            if (strtolower($membresiaEstado) === 'activo' || strtolower($membresiaEstado) === 'vigente') {
                $this->SetTextColor(34, 139, 34); // Verde Forest
                $clientesActivos++;
            } else {
                $this->SetTextColor(178, 34, 34); // Rojo fuego
            }

            $this->Cell($w[5], $line_height, utf8_decode(strtoupper($membresiaEstado)), 'B', 1, 'C', true);

            // Restaurar color base de texto
            $this->SetTextColor(50, 50, 50);

            $totalClientes++;
            $fill = !$fill;
        }

        // --- RESUMEN FINAL / TOTALES ---
        $this->Ln(4);
        $this->SetFont('Arial', 'B', 10);
        $this->SetTextColor($this->colorPrincipal[0], $this->colorPrincipal[1], $this->colorPrincipal[2]);

        // Cuadro de estadísticas del gimnasio
        $this->Cell(100, 7, utf8_decode("Total Clientes Registrados: " . $totalClientes), 0, 0, 'L');
        $this->Cell(100, 7, utf8_decode("Clientes con Membresía Activa: " . $clientesActivos), 0, 1, 'L');
    }
}
