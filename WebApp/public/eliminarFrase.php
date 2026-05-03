<?
require_once("/var/www/html/resources/db/FrasesDB.php");
session_start();

$res = FrasesDB::eliminarFrase($_GET['id'], $_SESSION['id_usuario']);

if($res) {
    header("Location: gestionarFrases.php?mensaje=eliminado");
} else {
    header("Location: gestionarFrases.php?mensaje=error_db");
}