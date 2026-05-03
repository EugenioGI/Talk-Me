<?php
session_start();
require_once 'middleware/auth.php';
require_once("/var/www/html/resources/db/FrasesDB.php");

// Verificación de sesión
$id_usuario = $_SESSION['id_usuario'] ?? null;
$usuarioSesion = $_SESSION['usuario'] ?? "Usuario"; 
$id_picto_seleccionado = $_GET['id_picto'] ?? null;

if (!$id_usuario) {
    header("Location: login.php");
    exit();
}

// Carga de cabeceras
include_once("/var/www/html/resources/templates/head.html");
include_once("/var/www/html/resources/templates/header.html");

$misFrases = FrasesDB::obtenerPorUsuario($id_usuario);
?>

<div class="container-fluid">
    <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow rounded">
        <a href="index.php" class="btn btn-outline-primary btn-sm"><i class="fas fa-home"></i> Home</a>
        
        <div class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search">
            <div class="input-group">
                <input type="text" id="inputBuscar" class="form-control bg-light border-0 small" placeholder="Buscar frase...">
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

    <h1 class="h3 mb-4 text-gray-800">Mis Frases Favoritas</h1>

    <?php if ($id_picto_seleccionado): ?>
    <div class="card shadow mb-4 border-left-success animated--grow-in">
        <div class="card-body">
            <h6 class="font-weight-bold text-success mb-2">Nueva Frase Detectada</h6>
            <form action="procesarFrase.php" method="POST" class="form-inline">
                <input type="hidden" name="id_picto" value="<?= htmlspecialchars($id_picto_seleccionado) ?>">
                <input type="text" name="nombre_frase" class="form-control mr-2 w-50" placeholder="Dale un nombre a tu frase (ej: Pedir agua)" required autofocus>
                <button type="submit" class="btn btn-success shadow-sm">
                    <i class="fas fa-save"></i> Guardar Frase
                </button>
                <a href="gestionarFrases.php" class="btn btn-link text-muted btn-sm">Cancelar</a>
            </form>
        </div>
    </div>
    <?php endif; ?>

    <div class="row">
        <?php if (empty($misFrases)): ?>
            <div class="col-12 text-center text-muted py-5">
                <div class="alert alert-warning shadow">
                    <i class="fas fa-exclamation-triangle fa-2x mb-3"></i><br>
                    Aún no tienes frases favoritas. <br>
                    Para crear una, pulsa la <b>estrella</b> en el Dashboard.
                </div>
            </div>
        <?php else: ?>
            <?php foreach ($misFrases as $frase): ?>
                <div class="col-xl-4 col-md-6 mb-4">
                    <div class="card border-left-primary shadow h-100 py-2 hover-card">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        <?= htmlspecialchars($frase['nombre_frase']) ?>
                                    </div>
                                    <small class="text-xs text-muted">Creada el: <?= $frase['fecha'] ?></small>
                                </div>
                                <div class="col-auto">
                                    <div class="btn-group">
                                        <a href="verDetalleFrase.php?id=<?= $frase['id_frase'] ?>" class="btn btn-info btn-circle btn-sm shadow-sm" title="Ver detalles">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        
                                        <button onclick="editarNombre(<?= $frase['id_frase'] ?>, '<?= addslashes($frase['nombre_frase']) ?>')" class="btn btn-warning btn-circle btn-sm shadow-sm mx-1" title="Editar nombre">
                                            <i class="fas fa-edit"></i>
                                        </button>

                                        <button onclick="eliminarFrase(<?= $frase['id_frase'] ?>)" class="btn btn-danger btn-circle btn-sm shadow-sm" title="Eliminar frase">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // 1. Manejo de mensajes por URL
    const urlParams = new URLSearchParams(window.location.search);
    const msj = urlParams.get('mensaje');

    if (msj === 'ok') {
        Swal.fire({ icon: 'success', title: '¡Guardada!', text: 'La frase se guardó correctamente.', timer: 2000, showConfirmButton: false });
    } else if (msj === 'editado') {
        Swal.fire({ icon: 'success', title: '¡Actualizado!', text: 'Nombre modificado con éxito.', timer: 2000, showConfirmButton: false });
    } else if (msj === 'eliminado') {
        Swal.fire({ icon: 'success', title: '¡Eliminado!', text: 'La frase ha sido borrada.', timer: 2000, showConfirmButton: false });
    } else if (msj === 'error_db') {
        Swal.fire({ icon: 'error', title: 'Error', text: 'No se pudo procesar la solicitud en la base de datos.' });
    }

    // 2. Función para eliminar con confirmación
    function eliminarFrase(id) {
        Swal.fire({
            title: '¿Eliminar frase?',
            text: "Esta acción no se puede deshacer.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e74a3b',
            cancelButtonColor: '#858796',
            confirmButtonText: 'Sí, borrar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = `eliminarFrase.php?id=${id}`;
            }
        });
    }

    // 3. Función para editar nombre (Input modal)
    async function editarNombre(id, nombreActual) {
        const { value: nuevoNombre } = await Swal.fire({
            title: 'Editar nombre',
            input: 'text',
            inputLabel: 'Nuevo nombre para la frase:',
            inputValue: nombreActual,
            showCancelButton: true,
            inputValidator: (value) => {
                if (!value) return '¡El nombre no puede estar vacío!'
            }
        });

        if (nuevoNombre && nuevoNombre !== nombreActual) {
            window.location.href = `editarFraseNombre.php?id=${id}&nuevo_nombre=${encodeURIComponent(nuevoNombre)}`;
        }
    }

    // 4. Buscador simple en tiempo real
    document.getElementById('inputBuscar').addEventListener('keyup', function() {
        let filtro = this.value.toLowerCase();
        let tarjetas = document.querySelectorAll('.col-xl-4');
        
        tarjetas.forEach(tarjeta => {
            let texto = tarjeta.querySelector('.h5').innerText.toLowerCase();
            tarjeta.style.display = texto.includes(filtro) ? "" : "none";
        });
    });
</script>

<style>
    .hover-card:hover {
        transform: translateY(-5px);
        transition: all 0.3s ease;
        border-left-width: 8px !important;
    }
</style>