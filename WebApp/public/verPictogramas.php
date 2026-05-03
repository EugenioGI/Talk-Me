<?php

require_once 'middleware/user_only.php';
$id_usuario = $_SESSION['id_usuario'] ?? null;
if (!$id_usuario) {
    echo "<script>window.location.href='login.php';</script>";
    exit();
}

$usuarioSesion = $_SESSION['usuario'] ?? "Invitado";
include_once("/var/www/html/resources/db/PictogramasDB.php");
include_once("/var/www/html/resources/db/CategoriasDB.php");

$categorias = CategoriasDB::obtenerPorUsuario($id_usuario);
$misPictogramas = PictogramasDB::obtenerPorUsuario($id_usuario);

include_once("/var/www/html/resources/templates/head.html");
include_once("/var/www/html/resources/templates/header.html");
?>

<div id="content-wrapper" class="d-flex flex-column">
    <div id="content">
        <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
            <a href="index.php" class="btn btn-outline-primary btn-sm"><i class="fas fa-home"></i> Home</a>
            
            <div class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search">
                <div class="input-group">
                    <input type="text" id="inputBuscar" class="form-control bg-light border-0 small" placeholder="Buscar pictograma..." aria-label="Search">
                    <div class="input-group-append">
                        <button class="btn btn-primary" type="button">
                            <i class="fas fa-search fa-sm"></i>
                        </button>
                    </div>
                </div>
            </div>

            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <span class="nav-link text-gray-600 small"><b><?= htmlspecialchars($usuarioSesion) ?></b></span>
                </li>
            </ul>
        </nav>

        <div class="container-fluid">
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <h1 class="h3 mb-0 text-gray-800">Gestión de Pictogramas</h1>
                <a href="crearPictogramas.php" class="btn btn-primary btn-icon-split">
                    <span class="icon text-white-50"><i class="fas fa-plus"></i></span>
                    <span class="text">Nuevo Pictograma</span>
                </a>
            </div>

            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-primary">
                    <h6 class="m-0 font-weight-bold text-white">Mis Recursos</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered" id="tablaPictogramas" width="100%" cellspacing="0">
                            <thead class="thead-light">
                                <tr>
                                    <th>Imagen</th>
                                    <th>Nombre</th>
                                    <th>Categoría</th>
                                    <th>Audio</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="cuerpoTabla">
                                <?php foreach ($misPictogramas as $p): ?>
                                <tr id="fila-<?= $p['id_pictograma'] ?>">
                                    <td class="text-center">
                                        <img src="uploads/img/<?= $p['ruta_imagen'] ?>" class="rounded shadow-sm" style="width: 50px; height: 50px; object-fit: cover;">
                                    </td>
                                    <td class="align-middle font-weight-bold nombre-picto"><?= htmlspecialchars($p['nombre']) ?></td>
                                    <td class="align-middle">
                                        <span class="badge badge-info"><?= htmlspecialchars($p['nombre_categoria'] ?? 'Sin categoría') ?></span>
                                    </td>
                                    <td class="align-middle text-center">
                                        <button class="btn btn-circle btn-sm btn-secondary" onclick="reproducir('uploads/audio/<?= $p['ruta_audio'] ?>', <?= $p['id_pictograma'] ?>)">
                                            <i class="fas fa-volume-up"></i>
                                        </button>
                                    </td>
                                    <td class="align-middle text-center">
                                        <div class="btn-group shadow-sm">
                                            <a href="gestionarFrases.php?id_picto=<?= $p['id_pictograma'] ?>" class="btn btn-outline-info btn-sm" title="Escribir frase con este picto">
                                                <i class="fas fa-pen"></i>
                                            </a>

                                            <button class="btn btn-outline-danger btn-sm" onclick="agregarFavorito(<?= $p['id_pictograma'] ?>)" title="Añadir a favoritos">
                                                <i class="fas fa-heart"></i>
                                            </button>

                                            <a href="editarPictograma.php?id=<?= $p['id_pictograma'] ?>" class="btn btn-warning btn-sm" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>

                                            <button class="btn btn-danger btn-sm" onclick="confirmarEliminar(<?= $p['id_pictograma'] ?>)" title="Eliminar">
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

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// --- LÓGICA DEL BUSCADOR ---
document.getElementById('inputBuscar').addEventListener('keyup', function() {
    let filtro = this.value.toLowerCase();
    let filas = document.querySelectorAll('#cuerpoTabla tr');

    filas.forEach(fila => {
        // Buscamos el texto dentro de la celda del nombre
        let nombre = fila.querySelector('.nombre-picto').textContent.toLowerCase();
        
        if (nombre.includes(filtro)) {
            fila.style.display = ""; // Muestra la fila
        } else {
            fila.style.display = "none"; // Oculta la fila
        }
    });
});

function reproducir(ruta) {
    const audio = new Audio(ruta);
    audio.play();
}

function confirmarEliminar(id) {
    Swal.fire({
        title: '¿Estás seguro?',
        text: "El pictograma se eliminará con éxito.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#e74a3b',
        cancelButtonColor: '#858796',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`procesar_eliminar.php?id=${id}`)
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        Swal.fire('¡Eliminado!', 'Eliminado con éxito.', 'success');
                        document.getElementById(`fila-${id}`).remove();
                    } else {
                        Swal.fire('Error', 'No se pudo eliminar.', 'error');
                    }
                })
                .catch(error => Swal.fire('Error', 'Fallo en el servidor.', 'error'));
        }
    });
}


function agregarFavorito(idPicto) {
    // Usamos fetch para enviar el ID al servidor
    fetch(`procesar_favorito.php?id=${idPicto}`)
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                Swal.fire({
                    icon: 'success',
                    title: '¡Añadido!',
                    text: 'Se agregó a tus favoritos',
                    timer: 1500,
                    showConfirmButton: false
                });
            } else if (data.status === 'exists') {
                Swal.fire({
                    icon: 'info',
                    title: 'Ya es favorito',
                    text: 'Este pictograma ya está en tu lista.',
                    timer: 1500,
                    showConfirmButton: false
                });
            } else {
                Swal.fire('Error', 'No se pudo agregar.', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire('Error', 'Hubo un problema en el servidor.', 'error');
        });
}
</script>




<?php if (isset($_GET['mensaje']) && $_GET['mensaje'] == 'ok'): ?>
    <script>Swal.fire('¡Éxito!', 'Pictograma guardado correctamente', 'success');</script>
<?php endif; ?>