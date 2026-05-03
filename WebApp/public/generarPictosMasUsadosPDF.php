<?php
if (ob_get_contents()) ob_end_clean();

session_start();
// Seguridad: Solo admin
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'administrador') {
    die("Acceso denegado.");
}

require '../vendor/autoload.php'; 
include_once("/var/www/html/resources/db/PictogramasDB.php");

$usuarioSesion = $_SESSION['usuario'] ?? "Admin";

class MYPDF extends TCPDF {
    public function Header() {
        $image_file = __DIR__ . '/../img/logo.png'; 
        if (file_exists($image_file)) {
            $this->Image($image_file, 15, 10, 35, '', 'PNG');
        }

        $this->SetFont('helvetica', 'B', 18);
        $this->SetY(15);
        $this->Cell(0, 15, 'Reporte de Uso Global', 0, false, 'R', 0, '', 0, false, 'M', 'M');
        $this->SetLineWidth(0.5);
        $this->Line(10, 32, 200, 32);
    }

    public function Footer() {
        $this->SetY(-15);
        $this->SetFont('helvetica', 'I', 8);
        $fecha = date('d/m/Y H:i');
        $this->Cell(0, 10, 'talk-me Admin - Generado el: '.$fecha.' - Página ' . $this->getAliasNumPage() . '/' . $this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
    }
}

$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->SetCreator('talk-me');
$pdf->SetTitle('Ranking Global de Pictogramas');
$pdf->SetMargins(15, 45, 15);
$pdf->SetAutoPageBreak(TRUE, 25);
$pdf->AddPage();

// 1. Encabezado del Reporte
$htmlHeader = '
    <h2 style="color: #4e73df; text-align: center;">Ranking de Pictogramas más Utilizados</h2>
    <p style="text-align: center; color: #555;">Este reporte muestra la popularidad de los recursos en toda la plataforma.</p>
    <br>';
$pdf->writeHTML($htmlHeader, true, false, true, false, '');

// 2. Obtener los datos del ranking
$pictogramas = PictogramasDB::obtenerMasUsadosDetallado();

// 3. Tabla de Ranking
$htmlTabla = '
    <table border="0.1" cellpadding="8" cellspacing="0" width="100%" style="border-color: #ccc;">
        <thead>
            <tr style="background-color: #4e73df; color: white; font-weight: bold;">
                <th width="10%" align="center">Rank</th>
                <th width="40%">Nombre del Pictograma</th>
                <th width="30%">Categoría</th>
                <th width="20%" align="center">Total Usos</th>
            </tr>
        </thead>
        <tbody>';

$rank = 1;
foreach ($pictogramas as $p) {
    // Alternar color de filas para legibilidad
    $bgColor = ($rank % 2 == 0) ? '#f8f9fc' : '#ffffff';
    
    $htmlTabla .= '
        <tr style="background-color: '.$bgColor.';">
            <td width="10%" align="center"><b>#' . $rank . '</b></td>
            <td width="40%">' . htmlspecialchars($p['nombre']) . '</td>
            <td width="30%">' . htmlspecialchars($p['categoria'] ?? 'Sin categoría') . '</td>
            <td width="20%" align="center" style="font-weight: bold; color: #1cc88a;">' . $p['total_usos'] . '</td>
        </tr>';
    $rank++;
}

$htmlTabla .= '</tbody></table>';

$pdf->writeHTML($htmlTabla, true, false, true, false, '');

// Salida del PDF
$pdf->Output('Ranking_Global_' . date('Ymd') . '.pdf', 'I');