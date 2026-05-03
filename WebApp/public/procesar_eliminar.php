<?php
include_once("/var/www/html/resources/db/PictogramasDB.php");

header('Content-Type: application/json');

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    // Aquí podrías añadir unlink() para borrar los archivos físicos si lo deseas
    $res = PictogramasDB::eliminar($id);

    if ($res) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'ID no proporcionado']);
}