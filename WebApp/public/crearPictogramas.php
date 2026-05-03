<?php

require_once 'middleware/user_only.php';
include_once("/var/www/html/resources/templates/head.html");
include_once("/var/www/html/resources/templates/header.html");
include_once("/var/www/html/resources/db/PictogramasDB.php");
include("../resources/db/CategoriasDB.php");

$usuarioSesion = $_SESSION['usuario'] ?? "Invitado";

$mensaje = "";
$error = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nombre = trim($_POST["nombre"]);
    $id_categoria = $_POST["id_categoria"];

    $id_usuario = $_SESSION['id_usuario'] ?? null;

    if (!$id_usuario) {
        header("Location: login.php");
        exit();
    }

    $imagen = $_FILES["imagen"];
    $audio = $_FILES["audio"];

    if (empty($nombre) || empty($id_categoria) || $imagen["error"] != 0 || $audio["error"] != 0) {
        $error = "Todos los campos son obligatorios";
    } else {

        $extImg = strtolower(pathinfo($imagen["name"], PATHINFO_EXTENSION));
        $extAud = strtolower(pathinfo($audio["name"], PATHINFO_EXTENSION));

        if (!in_array($extImg, ["jpg","png","jpeg"])) {
            $error = "Imagen inválida";
        } elseif (!in_array($extAud, ["mp3","wav"])) {
            $error = "Audio inválido";
        } else {

            $nombreImg = time() . "_" . $imagen["name"];
            $nombreAud = time() . "_" . $audio["name"];

            move_uploaded_file($imagen["tmp_name"], "uploads/img/" . $nombreImg);
            move_uploaded_file($audio["tmp_name"], "uploads/audio/" . $nombreAud);

            $res = PictogramasDB::insertar(
                $nombre,
                $nombreImg,
                $nombreAud,
                $id_categoria,
                $id_usuario 
            );

            if ($res) {
                $mensaje = "ok";
            } else {
                $error = "Error al guardar";
            }
        }
    }
}
?>
<div id="content-wrapper" class="d-flex flex-colun">
<div id="content">

<nav class="navbar navbar-expand navbar-light bg-white topbar mb-2 shadow">

<a href="index.php" class="btn btn-outline-primary btn-sm"><i class="fas fa-home"></i> Home</a>

<ul class="navbar-nav ml-auto">
    <span class="mr-3 text-gray-600 small">
        <?= htmlspecialchars($usuarioSesion) ?>
    </span>
    <img class="img-profile rounded-circle" src="../../img/undraw_profile.svg">
</ul>

</nav>

<div class="container-fluid">

<h1 class="h3 mb-4 text-gray-800">Crear Pictograma</h1>

<div class="card shadow mb-4">
<div class="card-body">

<form method="POST" enctype="multipart/form-data">

<div class="form-group">
    <label>Nombre</label>
    <input type="text" name="nombre" class="form-control" required>
</div>

<div class="form-group">
    <label>Imagen</label>
    <input type="file" name="imagen" class="form-control" accept="image/*" required>
</div>

<div class="form-group">
    <label>Audio</label>
    <input type="file" name="audio" class="form-control" accept="audio/*" required>
</div>

<div class="form-group">
    <label>Categoría</label>
    <select name="id_categoria" class="form-control" required>
        <option value="">Selecciona</option>

        <?php
        $categorias = CategoriasDB::obtenerTodas();

        foreach ($categorias as $cat) {
            echo "<option value='{$cat['id_categoria']}'>{$cat['nombre']}</option>";
        }
        ?>
    </select>
</div>

<button class="btn btn-primary">Guardar</button>

</form>

</div>
</div>

</div>
</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<?php if ($mensaje == "ok"): ?>
<script>
Swal.fire({
    icon: 'success',
    title: 'Pictograma creado',
    text: 'Se guardó correctamente'
}).then(() => {
    window.location = "index.php";
});
</script>
<?php endif; ?>

<?php if (!empty($error)): ?>
<script>
Swal.fire({
    icon: 'error',
    title: 'Error',
    text: '<?= $error ?>'
});
</script>
<?php endif; ?>