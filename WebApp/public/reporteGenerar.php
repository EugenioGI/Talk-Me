<?php
if (ob_get_contents()) ob_end_clean();

session_start();
require '../vendor/autoload.php'; 
include_once("/var/www/html/resources/db/PictogramasDB.php");
include_once("/var/www/html/resources/db/CategoriasDB.php");

$usuarioSesion = $_SESSION['usuario'] ?? "Invitado";
$id_usuario = $_SESSION['id_usuario'] ?? null;

if (!$id_usuario) {
    die("Acceso denegado. Por favor, inicie sesión.");
}

class MYPDF extends TCPDF {
    public function Header() {
        // --- RUTA LOGO CORREGIDA ---
        // Estamos en WebApp/public/archivo.php
        // Subimos un nivel (/../) para llegar a WebApp/ y luego entramos a img/logo.png
        $image_file = __DIR__ . '/../img/logo.png'; 
        
        if (file_exists($image_file)) {
            $this->Image($image_file, 15, 10, 35, '', 'PNG');
        }

        $this->SetFont('helvetica', 'B', 18);
        $this->SetY(15);
        $this->Cell(0, 15, 'Reporte de Recursos Personales', 0, false, 'R', 0, '', 0, false, 'M', 'M');
        $this->SetLineWidth(0.5);
        $this->Line(10, 32, 200, 32);
    }

    public function Footer() {
        $this->SetY(-15);
        $this->SetFont('helvetica', 'I', 8);
        $fecha = date('d/m/Y H:i');
        $this->Cell(0, 10, 'talk-me - Generado el: '.$fecha.' - Página ' . $this->getAliasNumPage() . '/' . $this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
    }
}

$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->SetCreator('talk-me');
$pdf->SetAuthor($usuarioSesion);
$pdf->SetTitle('Mis Categorías y Pictogramas');
$pdf->SetMargins(15, 45, 15);
$pdf->SetHeaderMargin(10);
$pdf->SetFooterMargin(10);
$pdf->SetAutoPageBreak(TRUE, 25);
$pdf->SetFont('helvetica', '', 11);
$pdf->AddPage();

// 1. Información del Usuario
$htmlInfo = '
    <table cellpadding="10" style="background-color: #f8f9fc; border: 1px solid #e3e6f0;">
        <tr>
            <td>
                <h2 style="color: #4e73df; margin: 0;">Información del Usuario</h2>
                <p style="font-size: 12px; color: #333;">
                    <b>Usuario:</b> ' . htmlspecialchars($usuarioSesion) . '<br>
                    <b>ID de cuenta:</b> #' . $id_usuario . '<br>
                    <b>Fecha de reporte:</b> ' . date('d/m/Y') . '
                </p>
            </td>
        </tr>
    </table><br><br>';

$pdf->writeHTML($htmlInfo, true, false, true, false, '');

$categorias = CategoriasDB::obtenerPorUsuario($id_usuario);
$allPictogramas = PictogramasDB::obtenerPorUsuario($id_usuario);

$agrupados = [];
foreach ($allPictogramas as $p) {
    $catNombre = $p['nombre_categoria'] ?? 'Sin categoría';
    $agrupados[$catNombre][] = $p;
}

if (empty($categorias)) {
    $pdf->writeHTML("<p style='text-align:center;'>No has creado ninguna categoría todavía.</p>", true, false, true, false, '');
} else {
    foreach ($categorias as $cat) {
        $nombreCat = $cat['nombre'];
        
        $htmlCat = '
        <h3 style="background-color: #1cc88a; color: white; padding: 10px;"> &nbsp; ' . htmlspecialchars($nombreCat) . '</h3>
        <table border="0.1" cellpadding="6" cellspacing="0" width="100%" style="border-color: #ddd;">
            <thead>
                <tr style="background-color: #f2f2f2; font-weight: bold; color: #333;">
                    <th width="25%" align="center">Pictograma</th>
                    <th width="35%">Nombre</th>
                    <th width="40%">Archivo de Audio</th>
                </tr>
            </thead>
            <tbody>';

        if (isset($agrupados[$nombreCat])) {
            foreach ($agrupados[$nombreCat] as $picto) {
                
                $nombreArchivo = $picto['ruta_imagen'];
                
                // --- TRIPLE CHEQUEO DE RUTAS ---
                // 1. En WebApp/img/ (Al nivel de public)
                $ruta1 = __DIR__ . '/../img/' . $nombreArchivo;
                // 2. En WebApp/public/uploads/img/
                $ruta2 = __DIR__ . '/uploads/img/' . $nombreArchivo;
                // 3. En WebApp/public/uploads/
                $ruta3 = __DIR__ . '/uploads/' . $nombreArchivo;

                $rutaFinal = "";

                if (!empty($nombreArchivo)) {
                    if (file_exists($ruta1)) {
                        $rutaFinal = $ruta1;
                    } elseif (file_exists($ruta2)) {
                        $rutaFinal = $ruta2;
                    } elseif (file_exists($ruta3)) {
                        $rutaFinal = $ruta3;
                    }
                }

                if ($rutaFinal != "") {
                    // Usamos la ruta física encontrada
                    $imgTag = '<img src="' . $rutaFinal . '" width="45" height="45" />';
                } else {
                    // Si falla, mostramos el nombre del archivo para saber qué buscar
                    $imgTag = '<span style="color:red; font-size:7px;">No disponible:<br>' . htmlspecialchars($nombreArchivo) . '</span>';
                }

                $htmlCat .= '
                <tr>
                    <td width="25%" align="center" style="vertical-align: middle;">' . $imgTag . '</td>
                    <td width="35%" style="vertical-align: middle;"><b>' . htmlspecialchars($picto['nombre']) . '</b></td>
                    <td width="40%" style="vertical-align: middle;">' . htmlspecialchars($picto['ruta_audio']) . '</td>
                </tr>';
            }
        }else {
            $htmlCat .= '<tr><td colspan="3" align="center">Categoría vacía.</td></tr>';
        }

        $htmlCat .= '</tbody></table><br><br>';
        $pdf->writeHTML($htmlCat, true, false, true, false, '');
    }
}

$pdf->Output('talk-me_reporte_' . date('Ymd') . '.pdf', 'I');