<?php
require_once 'middleware/user_only.php';
include_once("/var/www/html/resources/templates/head.html");
include_once("/var/www/html/resources/templates/header.html");
include("../resources/db/CategoriasDB.php");

$usuarioSesion = $_SESSION['usuario'] ?? "Invitado";
$id_usuario = $_SESSION['id_usuario'] ?? null;

if (!$id_usuario) {
    header("Location: login.php");
    exit();
}

// Obtenemos solo las categorías del usuario logueado
$misCategorias = CategoriasDB::obtenerPorUsuario($id_usuario);
?>

<div id="content-wrapper" class="d-flex flex-column">
    <div id="content">
        <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
            <a href="index.php" class="btn btn-outline-primary btn-sm"><i class="fas fa-home"></i> Home</a>
            
            <div class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search">
                <div class="input-group">
                    <input type="text" id="inputBuscar" class="form-control bg-light border-0 small" placeholder="Buscar categoría..." aria-label="Search">
                    <div class="input-group-append">
                        <button class="btn btn-primary" type="button">
                            <i class="fas fa-search fa-sm"></i>
                        </button>
                    </div>
                </div>
            </div>

            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <span class="nav-link text-gray-600 small">Usuario: <b><?= htmlspecialchars($usuarioSesion) ?></b></span>
                </li>
            </ul>
        </nav>

        <div class="container-fluid">
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <h1 class="h3 mb-0 text-gray-800">Gestión de Categorías</h1>
                <a href="crearCategorias.php" class="btn btn-success btn-icon-split shadow-sm" data-toggle="modal" data-target="#modalNuevaCategoria">
                    <span class="icon text-white-50"><i class="fas fa-plus"></i></span>
                    <span class="text">Nueva Categoría</span>
                </a>
            </div>

            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-success">
                    <h6 class="m-0 font-weight-bold text-white"><i class="fas fa-tags mr-2"></i>Mis Categorías</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered" id="tablaCategorias" width="100%" cellspacing="0">
                            <thead class="thead-light">
                                <tr>
                                    <th>Nombre de la Categoría</th>
                                    <th width="20%" class="text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="cuerpoTabla">
                                <?php if (empty($misCategorias)): ?>
                                    <tr><td colspan="2" class="text-center text-muted">No has creado ninguna categoría todavía.</td></tr>
                                <?php endif; ?>

                                <?php foreach ($misCategorias as $cat): ?>
                                <tr id="fila-<?= $cat['id_categoria'] ?>">
                                    <td class="align-middle font-weight-bold nombre-cat">
                                        <?= htmlspecialchars($cat['nombre']) ?>
                                    </td>
                                    <td class="align-middle text-center">
                                        <div class="btn-group shadow-sm">
                                            <a href="editarCategoria.php?id=<?= $cat['id_categoria'] ?>" class="btn btn-warning btn-sm" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button class="btn btn-danger btn-sm" title="Eliminar" onclick="confirmarEliminar(<?= $cat['id_categoria'] ?>)">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
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

<div class="modal fade" id="modalNuevaCategoria" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content border-left-success shadow">
            <form action="procesar_categoria.php" method="POST">
                <input type="hidden" name="accion" value="crear">
                <div class="modal-header">
                    <h5 class="modal-title font-weight-bold text-success">Añadir Categoría</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label class="font-weight-bold">Nombre de la Categoría</label>
                        <input type="text" name="nombre" class="form-control border-left-success" placeholder="Ej. Alimentos, Acciones..." required autofocus>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary btn-sm" type="button" data-dismiss="modal">Cancelar</button>
                    <button class="btn btn-success btn-sm" type="submit">Guardar Categoría</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
// --- BUSCADOR EN TIEMPO REAL ---
document.getElementById('inputBuscar').addEventListener('keyup', function() {
    const texto = this.value.toLowerCase();
    const filas = document.querySelectorAll('#cuerpoTabla tr');

    filas.forEach(fila => {
        const nombreCelda = fila.querySelector('.nombre-cat');
        if (nombreCelda) {
            const nombre = nombreCelda.textContent.toLowerCase();
            fila.style.display = nombre.includes(texto) ? "" : "none";
        }
    });
});

// --- ELIMINACIÓN CON SWEETALERT ---
// --- ELIMINACIÓN CON SWEETALERT ---
function confirmarEliminar(id) {
    Swal.fire({
        title: '¿Eliminar categoría?',
        text: "¡Atención! Si esta categoría tiene pictogramas, no podrá ser eliminada.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#e74a3b',
        cancelButtonColor: '#858796',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            // CAMBIA ESTA LÍNEA: debe apuntar a procesar_categoria.php
            fetch(`procesar_categoria.php?id=${id}`) 
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        Swal.fire('¡Borrado!', 'Categoría eliminada.', 'success');
                        document.getElementById(`fila-${id}`).remove();
                    } else {
                        // Aquí te dirá si hay pictogramas asociados
                        Swal.fire('Error', data.message, 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire('Error', 'No se pudo conectar con el servidor.', 'error');
                });
        }
    });
}
</script>

<?php if (isset($_GET['mensaje']) && $_GET['mensaje'] == 'ok'): ?>
    <script>
        Swal.fire({
            icon: 'success',
            title: '¡Hecho!',
            text: 'Operación realizada correctamente',
            timer: 2000,
            showConfirmButton: false
        });
    </script>
<?php endif; ?>