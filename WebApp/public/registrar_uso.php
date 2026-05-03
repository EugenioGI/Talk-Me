<?php
session_start();
include_once("/var/www/html/resources/db/PictogramasDB.php");

$id_picto = $_GET['id'] ?? null;
$id_usuario = $_SESSION['id_usuario'] ?? null;

if ($id_picto && $id_usuario) {
    // Usamos el método que ya tienes en tu PictogramasDB.php
    $exito = PictogramasDB::registrarUso($id_usuario, $id_picto);
    echo json_encode(["status" => $exito ? "ok" : "error"]);
} else {
    echo json_encode(["status" => "datos_insuficientes"]);
}