<?php
session_start();
include_once("/var/www/html/resources/db/FavoritosDB.php");

$id_usuario = $_SESSION['id_usuario'] ?? null;
$id_picto = $_GET['id'] ?? null;

if (!$id_usuario || !$id_picto) {
    echo json_encode(['status' => 'error', 'message' => 'Sesión o ID faltante']);
    exit();
}

// Llamamos al método de la clase de BD
$resultado = FavoritosDB::agregar($id_usuario, $id_picto);

echo json_encode(['status' => $resultado]);