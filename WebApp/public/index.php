<?php 
session_start();


if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}


$id_usuario = $_SESSION['id_usuario'];
$usuarioSesion = $_SESSION['usuario'] ?? "Usuario";
$rol = $_SESSION['rol'] ?? 'usuario';

include "../resources/templates/head.html"; 

if ($rol == 'administrador') {
    include '../resources/templates/headerAdmin.html';
} else {
    include '../resources/templates/header.html';
}


if ($rol == 'administrador') {
    include '../resources/templates/contentAdmin.php';
} else {
    include '../resources/templates/content.php';
}


include "../resources/templates/footer.html"; 
include "../resources/templates/fin.html"; 
?>