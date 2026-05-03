<?php
require_once 'middleware/admin_only.php';

$usuarioSesion = $_SESSION['usuario'] ?? "Admin";

include_once("../resources/db/CategoriasDB.php");
// Usamos el nuevo método para el administrador
$categorias = CategoriasDB::obtenerCategoriasParaAdmin();

include_once("../resources/templates/head.html");
include_once("../resources/templates/headerAdmin.html"); // Header de admin
?>

<div id="content-wrapper" class="d-flex flex-column">
    <div id="content">
        <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
            <div class="form-inline mr-auto w-100 px-4">
                <div class="input-group w-50 shadow-sm" style="border-radius: 25px; overflow: hidden; border: 1px solid #ddd;">
                    <div class="input-group-prepend">
                        <span class="input-group-text bg-light border-0"><i class="fas fa-search"></i></span>
                    </div>
                    <input type="text" id="inputBuscar" class="form-control bg-light border-0" 
                           placeholder="Buscar categoría en todo el sistema..." style="height: 45px;">
                </div>
            </div>
        </nav>

        <div class="container-fluid">
            <div class="mb-4">
                <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-tags text-primary mr-2"></i>Global de Categorías</h1>
                <p class="text-muted">Monitoreo de uso de categorías por parte de los usuarios.</p>
            </div>

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Estadísticas de Uso</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="tablaCategorias">
                            <thead class="thead-light">
                                <tr>
                                    <th>Nombre de Categoría</th>
                                    <th class="text-center">Usuarios que la usan</th>
                                    <th class="text-center">Pictogramas totales</th>
                                    <th class="text-center">Estado</th>
                                </tr>
                            </thead>
                            <tbody id="cuerpoTabla">
                                <?php foreach ($categorias as $c): ?>
                                <tr>
                                    <td class="align-middle font-weight-bold nombre-cat text-dark">
                                        <i class="fas fa-folder text-warning mr-2"></i>
                                        <?= htmlspecialchars($c['nombre']) ?>
                                    </td>
                                    <td class="text-center align-middle">
                                        <span class="badge badge-info px-3 py-2">
                                            <i class="fas fa-users mr-1"></i> <?= $c['total_usuarios'] ?> usuarios
                                        </span>
                                    </td>
                                    <td class="text-center align-middle">
                                        <span class="text-muted small font-weight-bold">
                                            <?= $c['total_pictogramas'] ?> pictogramas creados
                                        </span>
                                    </td>
                                    <td class="text-center align-middle">
                                        <?php if($c['total_usuarios'] > 0): ?>
                                            <span class="text-success small"><i class="fas fa-check-circle"></i> Activa</span>
                                        <?php else: ?>
                                            <span class="text-danger small"><i class="fas fa-exclamation-circle"></i> Sin uso</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// BUSCADOR EN TIEMPO REAL
document.getElementById('inputBuscar').addEventListener('keyup', function() {
    let filtro = this.value.toLowerCase();
    let filas = document.querySelectorAll('#cuerpoTabla tr');

    filas.forEach(fila => {
        let nombre = fila.querySelector('.nombre-cat').textContent.toLowerCase();
        fila.style.display = nombre.includes(filtro) ? "" : "none";
    });
});
</script>

<?php include_once("../resources/templates/fin.html"); ?>