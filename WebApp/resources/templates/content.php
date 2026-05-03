<?php
// 1. Aseguramos que las rutas de base de datos sean correctas
// Nota: Si estas rutas fallan, las variables de abajo serán NULL
include_once("/var/www/html/resources/db/CategoriasDB.php");
include_once("/var/www/html/resources/db/PictogramasDB.php");

// 2. Captura de variables de sesión con valores por defecto (Evita Warnings)
$usuarioSesion = $_SESSION['usuario'] ?? "Invitado";
$id_usuario = $_SESSION['id_usuario'] ?? null;
$categoriaFiltro = $_GET['categoria'] ?? null;

// 3. Obtención de datos con validación (Si falla la DB, devolvemos array vacío [])
$categorias = ($id_usuario) ? CategoriasDB::obtenerPorUsuario($id_usuario) : [];
$listaPictogramas = ($id_usuario) ? PictogramasDB::obtenerPorUsuarioYCategoria($id_usuario, $categoriaFiltro) : [];
$todosLosPictosParaGrafica = ($id_usuario) ? PictogramasDB::obtenerPorUsuario($id_usuario) : [];

// Si por alguna razón la DB devolvió algo que no es un array, lo forzamos a ser uno
if (!is_array($categorias)) $categorias = [];
if (!is_array($listaPictogramas)) $listaPictogramas = [];
if (!is_array($todosLosPictosParaGrafica)) $todosLosPictosParaGrafica = [];

// 4. Lógica para las Gráficas
$nombresGrafica = [];
$conteoGrafica = [];

foreach ($categorias as $cat) {
    $nombresGrafica[] = $cat['nombre'];
    $cantidad = 0;
    foreach ($todosLosPictosParaGrafica as $p) {
        if (isset($p['nombre_categoria']) && $p['nombre_categoria'] === $cat['nombre']) {
            $cantidad++;
        }
    }
    $conteoGrafica[] = $cantidad;
}
?>

<div id="content-wrapper" class="d-flex flex-column">
    <div id="content">
        <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
            <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                <i class="fa fa-bars"></i>
            </button>
            <ul class="navbar-nav ml-auto">
                <div class="topbar-divider d-none d-sm-block"></div>
                <li class="nav-item dropdown no-arrow">
                    <a class="nav-link dropdown-toggle" href="#" id="userDropdown" data-toggle="dropdown">
                        <span class="mr-2 d-none d-lg-inline text-gray-600 small"><?= htmlspecialchars($usuarioSesion) ?></span>
                        <img class="img-profile rounded-circle" src="../../img/undraw_profile.svg" width="30">
                    </a>
                    <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in">
                        <a class="dropdown-item" href="perfil.php"><i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i> Perfil</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i> Salir</a>
                    </div>
                </li>
            </ul>
        </nav>

        <div class="container-fluid">
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <h1 class="h3 mb-0 text-gray-800">Dashboard de Pictogramas</h1>
                <a href="/public/reporteGenerar.php" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
                    <i class="fas fa-download fa-sm text-white-50"></i> Generar Reporte
                </a>
            </div>

            <div class="row">
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-primary shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Pictogramas</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?= count($todosLosPictosParaGrafica) ?></div>
                                </div>
                                <div class="col-auto"><i class="fas fa-image fa-2x text-gray-300"></i></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-warning shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Categorías</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?= count($categorias) ?></div>
                                </div>
                                <div class="col-auto"><i class="fas fa-folder fa-2x text-gray-300"></i></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-8 col-lg-7">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">Estadísticas por Categoría</h6></div>
                        <div class="card-body">
                            <div class="chart-bar" style="height: 300px;"><canvas id="canvasBarras"></canvas></div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4 col-lg-5">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">Distribución</h6></div>
                        <div class="card-body">
                            <div class="chart-pie" style="height: 300px;"><canvas id="canvasPastel"></canvas></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-8 mb-4">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3 d-flex justify-content-between align-items-center bg-primary text-white">
                            <h6 class="m-0 font-weight-bold">Mis Pictogramas</h6>
                            <a href="crearPictogramas.php" class="btn btn-sm btn-light"><i class="fas fa-plus"></i> Nuevo</a>
                        </div>
                        <div class="card-body">
                            <form method="GET" class="form-inline mb-4">
                                <label class="mr-2">Filtrar por:</label>
                                <select name="categoria" class="form-control form-control-sm" onchange="this.form.submit()">
                                    <option value="">Todas las categorías</option>
                                    <?php foreach ($categorias as $cat): ?>
                                        <option value="<?= $cat['id_categoria'] ?>" <?= ($categoriaFiltro == $cat['id_categoria']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($cat['nombre']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </form>
                            
                            <div class="row">
                                <?php if (!empty($listaPictogramas)): ?>
                                    <?php foreach ($listaPictogramas as $picto): ?>
                                        <div class="col-xl-3 col-md-4 col-6 mb-4 text-center">
                                            <div class="border p-2 rounded shadow-sm h-100 d-flex flex-column align-items-center bg-white">
                                                <img src="uploads/img/<?= htmlspecialchars($picto['ruta_imagen']) ?>" 
                                                     class="img-fluid rounded mb-2" 
                                                     style="height:80px; width:80px; object-fit:cover;"
                                                     onerror="this.src='img/no-image.png'">
                                                <h6 class="small font-weight-bold text-dark"><?= htmlspecialchars($picto['nombre']) ?></h6>
                                                
                                                <div class="btn-group mt-auto w-100">
                                                    <button class="btn btn-sm btn-outline-primary" 
                                                            onclick="reproducir('uploads/audio/<?= $picto['ruta_audio'] ?>', <?= $picto['id_pictograma'] ?>)">
                                                        <i class="fas fa-volume-up"></i>
                                                    </button>
                                                    <a href="gestionarFrases.php?id_picto=<?= $picto['id_pictograma'] ?>" class="btn btn-sm btn-outline-warning"><i class="fas fa-star"></i></a>
                                                    <button class="btn btn-sm btn-outline-danger" onclick="confirmarEliminar(<?= $picto['id_pictograma'] ?>)"><i class="fas fa-trash"></i></button>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="col-12 text-center text-muted py-4">No hay pictogramas para mostrar.</div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 mb-4">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3 bg-success text-white d-flex justify-content-between align-items-center">
                            <h6 class="m-0 font-weight-bold">Categorías</h6>
                            <a href="crearCategorias.php" class="btn btn-sm btn-light"><i class="fas fa-plus"></i></a>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-sm table-hover">
                                    <thead><tr><th>Nombre</th><th class="text-right">Acciones</th></tr></thead>
                                    <tbody>
                                        <?php foreach ($categorias as $cat): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($cat['nombre']) ?></td>
                                                <td class="text-right">
                                                    <a href="editarCategoria.php?id=<?= $cat['id_categoria'] ?>" class="text-warning mr-2"><i class="fas fa-edit"></i></a>
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
    </div>
</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const etiquetas = <?php echo json_encode($nombresGrafica); ?>;
    const datos = <?php echo json_encode($conteoGrafica); ?>;

    new Chart(document.getElementById("canvasBarras"), {
        type: 'bar',
        data: {
            labels: etiquetas,
            datasets: [{
                label: "Cantidad de Pictogramas",
                backgroundColor: "#4e73df",
                data: datos,
            }],
        },
        options: { maintainAspectRatio: false }
    });

    new Chart(document.getElementById("canvasPastel"), {
        type: 'doughnut',
        data: {
            labels: etiquetas,
            datasets: [{
                data: datos,
                backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b'],
            }],
        },
        options: { maintainAspectRatio: false, cutout: '70%' }
    });

    function confirmarEliminar(id) {
        if(confirm('¿Estás seguro de eliminar este pictograma?')) {
            window.location.href = "eliminar_pictograma.php?id=" + id;
        }
    }
</script>