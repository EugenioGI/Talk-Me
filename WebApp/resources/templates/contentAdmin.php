<?php

include_once("/var/www/html/resources/db/CategoriasDB.php");
include_once("/var/www/html/resources/db/PictogramasDB.php");
include_once("/var/www/html/resources/db/UsuarioDB.php"); 

$usuarioSesion = $_SESSION['usuario'] ?? "Admin";
$id_usuario = $_SESSION['id_usuario'] ?? null;

$totalPictosGlobal = count(PictogramasDB::obtenerTodos()); 
$categoriasGlobales = CategoriasDB::obtenerTodas(); 

$usuariosSistema = UsuarioDB::obtenerTodosLosUsuarios(); 
$totalUsuarios = count($usuariosSistema);
$usuariosActivos = 0;
foreach($usuariosSistema as $u) if($u['activo']) $usuariosActivos++;


$conteoRoles = ['administrador' => 0, 'usuario' => 0];
foreach($usuariosSistema as $u) {
    if(isset($u['rol'])) $conteoRoles[$u['rol']]++;
}
?>

<div id="content-wrapper" class="d-flex flex-column">
    <div id="content">
        <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
            <h2 class="h5 mb-0 text-gray-800 ml-3">Panel de Control Administrativo</h2>
            <ul class="navbar-nav ml-auto">
                <li class="nav-item dropdown no-arrow">
                    <a class="nav-link dropdown-toggle" href="#" id="userDropdown" data-toggle="dropdown">
                        <span class="mr-2 d-none d-lg-inline text-primary font-weight-bold"><?= htmlspecialchars($usuarioSesion) ?> (Admin)</span>
                        <img class="img-profile rounded-circle" src="../img/undraw_profile.svg" width="30">
                    </a>
                    <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in">
                        <a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i> Cerrar Sesión</a>
                    </div>
                </li>
            </ul>
        </nav>

        <div class="container-fluid">
            <div class="row">
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-info shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Usuarios Totales</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $totalUsuarios ?></div>
                                </div>
                                <div class="col-auto"><i class="fas fa-users fa-2x text-gray-300"></i></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-success shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Cuentas Activas</div>
                                    <div class="row no-gutters align-items-center">
                                        <div class="col-auto">
                                            <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800">
                                                <?= ($totalUsuarios > 0) ? round(($usuariosActivos/$totalUsuarios)*100) : 0 ?>%
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div class="progress progress-sm mr-2">
                                                <div class="progress-bar bg-success" style="width: <?= ($totalUsuarios > 0) ? ($usuariosActivos/$totalUsuarios)*100 : 0 ?>%"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-auto"><i class="fas fa-check-circle fa-2x text-gray-300"></i></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-primary shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Recursos (Pictos)</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $totalPictosGlobal ?></div>
                                </div>
                                <div class="col-auto"><i class="fas fa-database fa-2x text-gray-300"></i></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-4 col-lg-5">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3 bg-gray-800">
                            <h6 class="m-0 font-weight-bold text-white">Distribución de Roles</h6>
                        </div>
                        <div class="card-body">
                            <div class="chart-pie pt-4 pb-2">
                                <canvas id="chartRoles"></canvas>
                            </div>
                            <div class="mt-4 text-center small">
                                <span class="mr-2"><i class="fas fa-circle text-primary"></i> Admins</span>
                                <span class="mr-2"><i class="fas fa-circle text-success"></i> Usuarios</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-8 col-lg-7">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3 d-flex justify-content-between align-items-center">
                            <h6 class="m-0 font-weight-bold text-primary">Control de Usuarios Registrados</h6>
                            <a href="generarReporteAdminDashboard.php" target="_blank" class="btn btn-sm btn-primary shadow-sm">
                                <i class="fas fa-file-pdf fa-sm"></i> Exportar Reporte PDF
                            </a>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>Usuario</th>
                                            <th>Rol</th>
                                            <th>Estado</th>
                                            <th>Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach (array_slice($usuariosSistema, 0, 5) as $user): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($user['usuario']) ?></td>
                                            <td><span class="badge badge-secondary"><?= $user['rol'] ?></span></td>
                                            <td>
                                                <?= $user['activo'] ? 
                                                '<span class="text-success"><i class="fas fa-check"></i> Activo</span>' : 
                                                '<span class="text-danger"><i class="fas fa-times"></i> Pendiente</span>' ?>
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-circle btn-warning"><i class="fas fa-edit"></i></button>
                                                <button class="btn btn-sm btn-circle btn-danger"><i class="fas fa-trash"></i></button>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <div class="text-center">
                                <a class="small" href="gestionUsuarios.php">Ver todos los usuarios →</a>
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
    // Gráfica de Pastel para Roles
    new Chart(document.getElementById("chartRoles"), {
        type: 'doughnut',
        data: {
            labels: ["Administradores", "Usuarios"],
            datasets: [{
                data: [<?= $conteoRoles['administrador'] ?>, <?= $conteoRoles['usuario'] ?>],
                backgroundColor: ['#4e73df', '#1cc88a'],
                hoverBackgroundColor: ['#2e59d9', '#17a673'],
                hoverBorderColor: "rgba(234, 236, 244, 1)",
            }],
        },
        options: {
            maintainAspectRatio: false,
            tooltips: { backgroundColor: "rgb(255,255,255)", bodyFontColor: "#858796", borderColor: '#dddfeb', borderWidth: 1, xPadding: 15, yPadding: 15, displayColors: false, caretPadding: 10 },
            legend: { display: false },
            cutoutPercentage: 80,
        },
    });
</script>