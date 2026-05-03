<?php
if (ob_get_contents()) ob_end_clean();

session_start();
// Seguridad: Solo admin
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'administrador') {
    die("Acceso denegado.");
}

require '../vendor/autoload.php'; 
include_once("/var/www/html/resources/db/PictogramasDB.php");
include_once("/var/www/html/resources/db/CategoriasDB.php");
include_once("/var/www/html/resources/db/UsuarioDB.php"); 

$usuarioSesion = $_SESSION['usuario'] ?? "Admin";

class ADMIN_PDF extends TCPDF {
    public function Header() {
        $image_file = __DIR__ . '/../img/logo.png'; 
        if (file_exists($image_file)) {
            $this->Image($image_file, 15, 10, 30, '', 'PNG');
        }
        $this->SetFont('helvetica', 'B', 16);
        $this->SetY(15);
        $this->Cell(0, 15, 'ESTADO GLOBAL DEL SISTEMA', 0, false, 'R');
        $this->Line(15, 32, 195, 32);
    }

    public function Footer() {
        $this->SetY(-15);
        $this->SetFont('helvetica', 'I', 8);
        $this->Cell(0, 10, 'Talk-Me Intelligence - Página ' . $this->getAliasNumPage() . '/' . $this->getAliasNbPages(), 0, false, 'C');
    }
}

// Inicializar PDF
$pdf = new ADMIN_PDF('P', 'mm', 'A4', true, 'UTF-8', false);
$pdf->SetMargins(15, 40, 15);
$pdf->AddPage();

// --- PROCESAMIENTO DE DATOS (Igual que en tu Dashboard) ---
$usuariosSistema = UsuarioDB::obtenerTodosLosUsuarios();
$totalUsuarios = count($usuariosSistema);
$usuariosActivos = 0;
$conteoRoles = ['administrador' => 0, 'usuario' => 0];

foreach($usuariosSistema as $u) {
    if($u['activo']) $usuariosActivos++;
    if(isset($u['rol'])) $conteoRoles[$u['rol']]++;
}

$totalPictos = count(PictogramasDB::obtenerTodos());
$totalCategorias = count(CategoriasDB::obtenerTodas());

// --- DISEÑO DEL CONTENIDO ---
$html = '
<h2 style="color: #4e73df;">Resumen Ejecutivo</h2>
<p>Este documento presenta el estado actual de la plataforma Talk-Me al <b>' . date('d/m/Y H:i') . '</b>.</p>

<table cellpadding="10" border="0" style="background-color: #f8f9fc;">
    <tr>
        <td width="50%">
            <h4 style="color: #36b9cc;">Usuarios</h4>
            <b>Total:</b> ' . $totalUsuarios . '<br>
            <b>Activos:</b> ' . $usuariosActivos . ' (' . round(($usuariosActivos/$totalUsuarios)*100) . '%)
        </td>
        <td width="50%">
            <h4 style="color: #1cc88a;">Recursos Globales</h4>
            <b>Categorías:</b> ' . $totalCategorias . '<br>
            <b>Pictogramas:</b> ' . $totalPictos . '
        </td>
    </tr>
</table>

<h3 style="color: #4e73df; border-bottom: 1px solid #eee; padding-top: 20px;">Distribución por Roles</h3>
<table cellpadding="8" border="0.5" style="border-color: #ddd;">
    <tr style="background-color: #4e73df; color: white;">
        <th>Rol</th>
        <th align="center">Cantidad de Cuentas</th>
    </tr>
    <tr>
        <td>Administradores</td>
        <td align="center">' . $conteoRoles['administrador'] . '</td>
    </tr>
    <tr>
        <td>Usuarios Regulares</td>
        <td align="center">' . $conteoRoles['usuario'] . '</td>
    </tr>
</table>

<h3 style="color: #4e73df; border-bottom: 1px solid #eee; padding-top: 20px;">Últimos Usuarios Registrados</h3>
<table cellpadding="5" border="0.1" style="border-color: #eee; font-size: 10px;">
    <tr style="background-color: #eee; font-weight: bold;">
        <th width="40%">Nombre de Usuario</th>
        <th width="30%">Rol</th>
        <th width="30%">Estado</th>
    </tr>';

// Mostrar los últimos 10 usuarios
$ultimosUsuarios = array_slice($usuariosSistema, 0, 10);
foreach ($ultimosUsuarios as $user) {
    $estado = $user['activo'] ? 'Activo' : 'Pendiente';
    $html .= '
    <tr>
        <td>' . htmlspecialchars($user['usuario']) . '</td>
        <td>' . $user['rol'] . '</td>
        <td>' . $estado . '</td>
    </tr>';
}

$html .= '</table>';

$pdf->writeHTML($html, true, false, true, false, '');

// Salida
$pdf->Output('Reporte_General_TalkMe.pdf', 'I');