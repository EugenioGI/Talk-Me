<?php
session_start();
include_once("/var/www/html/resources/db/PictogramasDB.php");

// Verificación de sesión
$id_usuario = $_SESSION['id_usuario'] ?? null;
if (!$id_usuario) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_pictograma = $_POST['id_pictograma'];
    $nombre = trim($_POST['nombre']);
    $id_categoria = $_POST['id_categoria'];

    // 1. Obtener los datos actuales del pictograma para no perder las rutas si no se cambian
    $pictoActual = PictogramasDB::obtenerPorId($id_pictograma);
    
    $nombreImg = $pictoActual['ruta_imagen']; // Por defecto la actual
    $nombreAud = $pictoActual['ruta_audio'];  // Por defecto la actual

    // 2. Gestión de Nueva Imagen (Solo si subieron una)
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0) {
        $extImg = strtolower(pathinfo($_FILES["imagen"]["name"], PATHINFO_EXTENSION));
        if (in_array($extImg, ["jpg", "jpeg", "png"])) {
            $nuevoNombreImg = time() . "_edit_" . bin2hex(random_bytes(4)) . "." . $extImg;
            if (move_uploaded_file($_FILES["imagen"]["tmp_name"], "uploads/img/" . $nuevoNombreImg)) {
                // Opcional: Borrar la imagen vieja del servidor para no acumular basura
                // @unlink("uploads/img/" . $pictoActual['ruta_imagen']); 
                $nombreImg = $nuevoNombreImg;
            }
        }
    }

    // 3. Gestión de Nuevo Audio (Solo si subieron uno)
    if (isset($_FILES['audio']) && $_FILES['audio']['error'] == 0) {
        $extAud = strtolower(pathinfo($_FILES["audio"]["name"], PATHINFO_EXTENSION));
        if (in_array($extAud, ["mp3", "wav"])) {
            $nuevoNombreAud = time() . "_edit_" . bin2hex(random_bytes(4)) . "." . $extAud;
            if (move_uploaded_file($_FILES["audio"]["tmp_name"], "uploads/audio/" . $nuevoNombreAud)) {
                // Opcional: Borrar el audio viejo
                // @unlink("uploads/audio/" . $pictoActual['ruta_audio']);
                $nombreAud = $nuevoNombreAud;
            }
        }
    }

    // 4. Actualizar en la Base de Datos
    $res = PictogramasDB::actualizar($id_pictograma, $nombre, $nombreImg, $nombreAud, $id_categoria);

    if ($res) {
        // Redirigimos a la tabla con un parámetro de éxito
        header("Location: verPictogramas.php?edit=success");
    } else {
        header("Location: verPictogramas.php?edit=error");
    }
    exit();
}