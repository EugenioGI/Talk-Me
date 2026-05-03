<?php
require_once 'middleware/user_only.php';
include_once("/var/www/html/resources/templates/head.html");
include_once("/var/www/html/resources/templates/header.html");
include("../resources/db/CategoriasDB.php");

$usuarioSesion = $_SESSION['usuario'] ?? "Invitado";
$id_usuario = $_SESSION['id_usuario'] ?? null;

// Verificar sesión
if (!$id_usuario) {
    header("Location: login.php");
    exit();
}

// Obtener el ID de la categoría por URL
$id_categoria = $_GET['id'] ?? null;

if (!$id_categoria) {
    header("Location: verCategorias.php");
    exit();
}

$categorias = CategoriasDB::obtenerPorUsuario($id_usuario);
$categoriaActual = null;

foreach ($categorias as $cat) {
    if ($cat['id_categoria'] == $id_categoria) {
        $categoriaActual = $cat;
        break;
    }
}

if (!$categoriaActual) {
    echo "<script>alert('Categoría no encontrada o acceso denegado'); window.location='verCategorias.php';</script>";
    exit();
}
?>

<div id="content-wrapper" class="d-flex flex-column">
    <div id="content">
        <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
            <a href="verCategorias.php" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Volver a Categorías
            </a>
        </nav>

        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-lg-6">
                    <div class="card shadow mb-4 border-left-warning">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-warning">
                                <i class="fas fa-edit mr-2"></i>Editar Categoría
                            </h6>
                        </div>
                        <div class="card-body">
                            <form action="procesar_categoria.php" method="POST">
                                <input type="hidden" name="accion" value="actualizar">
                                <input type="hidden" name="id_categoria" value="<?= $categoriaActual['id_categoria'] ?>">

                                <div class="form-group">
                                    <label class="font-weight-bold text-gray-800">Nombre de la Categoría</label>
                                    <input type="text" 
                                           name="nombre" 
                                           class="form-control form-control-user" 
                                           value="<?= htmlspecialchars($categoriaActual['nombre']) ?>" 
                                           placeholder="Ej. Alimentos, Saludos..." 
                                           required 
                                           autofocus>
                                    <small class="form-text text-muted">
                                        Este nombre se mostrará en los filtros de tus pictogramas.
                                    </small>
                                </div>

                                <hr>

                                <div class="d-flex justify-content-between">
                                    <a href="verCategorias.php" class="btn btn-light btn-icon-split border shadow-sm">
                                        <span class="icon text-gray-600"><i class="fas fa-times"></i></span>
                                        <span class="text">Cancelar</span>
                                    </a>
                                    <button type="submit" class="btn btn-warning btn-icon-split shadow-sm">
                                        <span class="icon text-white-50"><i class="fas fa-save"></i></span>
                                        <span class="text">Guardar Cambios</span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<?php
if (isset($_GET['error'])) {
    echo "<script>Swal.fire('Error', 'No se pudo actualizar la categoría.', 'error');</script>";
}
?>