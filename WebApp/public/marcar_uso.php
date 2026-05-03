<?php
session_start();
include_once("../resources/db/PictogramasDB.php");

$id_usuario = $_SESSION['id_usuario'] ?? null;
$id_pictograma = $_GET['id'] ?? null;

if ($id_usuario && $id_pictograma) {
    PictogramasDB::registrarUso($id_usuario, $id_pictograma);
    echo json_encode(["status" => "ok"]);
}