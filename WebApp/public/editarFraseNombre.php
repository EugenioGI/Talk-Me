<?php
require_once 'middleware/user_only.php';
require_once("/var/www/html/resources/db/FrasesDB.php");

// Recogemos los datos de la URL (GET)
$id_frase = $_GET['id'] ?? null;
$nuevo_nombre = $_GET['nuevo_nombre'] ?? null;
$id_usuario = $_SESSION['id_usuario'] ?? null;

// Validación básica de seguridad
if (!$id_frase || !$nuevo_nombre || !$id_usuario) {
    header("Location: gestionarFrases.php?mensaje=error_datos");
    exit();
}

// Llamamos al método del modelo que creamos antes
$resultado = FrasesDB::actualizarNombre($id_frase, $id_usuario, $nuevo_nombre);

if ($resultado) {
    // Si todo salió bien, volvemos con mensaje de éxito
    header("Location: gestionarFrases.php?mensaje=editado");
} else {
    // Si la DB falló (por ejemplo, id inexistente)
    header("Location: gestionarFrases.php?mensaje=error_db");
}
exit();