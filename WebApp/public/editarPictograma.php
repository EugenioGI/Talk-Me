<?php
require_once 'middleware/user_only.php';
include_once("/var/www/html/resources/templates/head.html");
include_once("/var/www/html/resources/templates/header.html");
include_once("/var/www/html/resources/db/PictogramasDB.php");
include("../resources/db/CategoriasDB.php");

$id_usuario = $_SESSION['id_usuario'] ?? null;
$id_picto = $_GET['id'] ?? null;

if (!$id_usuario || !$id_picto) { header("Location: verPictogramas.php"); exit(); }

$p = PictogramasDB::obtenerPorId($id_picto);
$categorias = CategoriasDB::obtenerPorUsuario($id_usuario);
?>

<div class="container-fluid mt-4">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-warning text-white">
                    <h6 class="m-0 font-weight-bold">Editar Pictograma: <?= htmlspecialchars($p['nombre']) ?></h6>
                </div>
                <div class="card-body text-center">
                    <img src="uploads/img/<?= $p['ruta_imagen'] ?>" class="mb-4 rounded shadow" style="width:100px;">
                    
                    <form action="procesar_editar.php" method="POST" enctype="multipart/form-data" class="text-left">
                        <input type="hidden" name="id_pictograma" value="<?= $p['id_pictograma'] ?>">
                        
                        <div class="form-group">
                            <label>Nombre</label>
                            <input type="text" name="nombre" class="form-control" value="<?= htmlspecialchars($p['nombre']) ?>" required>
                        </div>

                        <div class="form-group">
                            <label>Categoría</label>
                            <select name="id_categoria" class="form-control">
                                <?php foreach ($categorias as $cat): ?>
                                    <option value="<?= $cat['id_categoria'] ?>" <?= ($cat['id_categoria'] == $p['id_categoria']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($cat['nombre']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label>Cambiar Imagen (opcional)</label>
                            <input type="file" name="imagen" class="form-control-file" accept="image/*">
                        </div>

                        <div class="form-group">
                            <label>Cambiar Audio (opcional)</label>
                            <input type="file" name="audio" class="form-control-file" accept="audio/*">
                        </div>

                        <hr>
                        <div class="d-flex justify-content-between">
                            <a href="verPictogramas.php" class="btn btn-secondary">Volver</a>
                            <button type="submit" class="btn btn-warning">Guardar Cambios</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>