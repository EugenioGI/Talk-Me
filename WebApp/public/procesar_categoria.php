<?php
session_start();
include_once("/var/www/html/resources/db/CategoriasDB.php");

$id_usuario = $_SESSION['id_usuario'] ?? null;

if (!$id_usuario) {
    header("Content-Type: application/json");
    echo json_encode(['status' => 'error', 'message' => 'Sesión no iniciada']);
    exit();
}


if (isset($_GET['id']) && !isset($_POST['accion'])) {
    header("Content-Type: application/json");
    $id_cat = intval($_GET['id']);
    

    $resultado = CategoriasDB::eliminar($id_cat);

    if ($resultado) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode([
            'status' => 'error', 
            'message' => 'No se puede eliminar: existen pictogramas usando esta categoría.'
        ]);
    }
    exit();
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';
    $nombre = trim($_POST['nombre'] ?? '');

    if (empty($nombre)) {
        header("Location: verCategorias.php?error=nombre_vacio");
        exit();
    }

    if ($accion === 'crear') {
        $res = CategoriasDB::insertar($nombre, $id_usuario);
        if ($res) {
            header("Location: verCategorias.php?mensaje=ok");
        } else {
            header("Location: verCategorias.php?error=db");
        }
    } 
    
    elseif ($accion === 'actualizar') {
        $id_cat = intval($_POST['id_categoria'] ?? 0);
        
        if ($id_cat > 0) {
            $res = CategoriasDB::actualizar($id_cat, $nombre);
            if ($res) {
                header("Location: verCategorias.php?mensaje=ok");
            } else {
                header("Location: editarCategoria.php?id=$id_cat&error=1");
            }
        }
    }
    exit();
}

// Si alguien accede directamente al archivo sin datos
header("Location: verCategorias.php");
exit();