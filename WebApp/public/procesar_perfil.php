<?php
session_start();
// Asegúrate de que la ruta sea la correcta (subiendo un nivel si estás en /public)
include_once("../resources/db/PerfilDB.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_usuario = $_POST['id_usuario'];
    $datos = [
        'nombre'     => $_POST['nombre'],
        'edad'       => $_POST['edad'],
        'genero'     => $_POST['genero'],
        'condicion'  => $_POST['condicion'],
        'apoyo'      => $_POST['apoyo'],
        'dificultad' => $_POST['dificultad']
    ];

    $resultado = PerfilDB::guardarOActualizarPerfil($id_usuario, $datos);

    if ($resultado) {
        // CAMBIO AQUÍ: Redirigir directamente a perfil.php y añadir exit()
        header("Location: perfil.php?id=$id_usuario&msj=ok");
        exit(); 
    } else {
        echo "Error al guardar los datos en la base de datos.";
    }
} else {
    // Si intentan entrar por URL sin POST, mandarlos de vuelta
    header("Location: perfil.php");
    exit();
}