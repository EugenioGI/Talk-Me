<?php
session_start();
include_once("/var/www/html/resources/db/ConexionDB.php");

$id_usuario = $_SESSION['id_usuario'];
$id_picto = $_GET['id'];

$db = Conexion::getInstancia()->getDbh();
$sql = "DELETE FROM Favoritos WHERE id_usuario = ? AND id_pictograma = ?";
$stmt = $db->prepare($sql);
$resultado = $stmt->execute([$id_usuario, $id_picto]);

echo json_encode(['status' => $resultado ? 'success' : 'error']);