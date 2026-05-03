<?php
session_start();
// Reporte de errores para desarrollo
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once("/var/www/html/resources/db/FrasesDB.php");

$id_usuario = $_SESSION['id_usuario'] ?? null;
$nombre = $_POST['nombre_frase'] ?? null;
$id_picto = $_POST['id_picto'] ?? null;

// Si falta algo, detenemos el proceso y avisamos
if (!$id_usuario || !$nombre || !$id_picto) {
    die("Error crítico: Faltan datos obligatorios. Usuario: $id_usuario, Frase: $nombre, Picto: $id_picto");
}

if (FrasesDB::crearFrase($id_usuario, $nombre, $id_picto)) {
    // ÉXITO: Redirigimos con el mensaje de OK
    header("Location: gestionarFrases.php?mensaje=ok");
} else {
    // FALLO EN SQL: Redirigimos con mensaje de error
    header("Location: gestionarFrases.php?mensaje=error_db");
}
exit();