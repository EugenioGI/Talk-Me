<?php
require_once 'middleware/admin_only.php';

$usuarioSesion = $_SESSION['usuario'] ?? "Admin";
include_once("/var/www/html/resources/db/PictogramasDB.php");

// Usamos el método detallado que suma los usos del historial
$pictogramasMasUsados = PictogramasDB::obtenerMasUsadosDetallado();

// 4. DISEÑO
include_once("/var/www/html/resources/templates/head.html");
include_once("/var/www/html/resources/templates/header.html");
?>

<div id="content-wrapper" class="d-flex flex-column">
    <div id="content">
        <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
            <a href="index.php" class="btn btn-outline-primary btn-sm"><i class="fas fa-arrow-left"></i> Volver al Panel</a>
            
            <div class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search">
                <div class="input-group">
                    <input type="text" id="inputBuscar" class="form-control bg-light border-0 small" placeholder="Filtrar por nombre o categoría..." aria-label="Search">
                </div>
            </div>

            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <span class="nav-link text-gray-600 small">Panel de Control: <b><?= htmlspecialchars($usuarioSesion) ?></b></span>
                </li>
            </ul>
        </nav>

        <div class="container-fluid">
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <h1 class="h3 mb-0 text-gray-800">Ranking de Uso Global</h1>
                <a href="generarPictosMasUsadosPDF.php" class="btn btn-sm btn-danger shadow-sm">
                    <i class="fas fa-file-pdf fa-sm text-white-50"></i> Descargar PDF
                </a>
            </div>

            <div class="card shadow mb-4 border-left-success">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-success">Estadísticas de Pictogramas</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="tablaMasUsados" width="100%" cellspacing="0">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th width="10%">Ranking</th>
                                    <th>Imagen</th>
                                    <th>Nombre del Pictograma</th>
                                    <th>Categoría</th>
                                    <th class="text-center">Total de Usos</th>
                                    <th width="15%">Popularidad</th>
                                </tr>
                            </thead>
                            <tbody id="cuerpoTabla">
                                <?php 
                                $contador = 1;
                                foreach ($pictogramasMasUsados as $p): 
                                    // Cálculo simple para la barra de progreso (asumiendo máximo basado en el top 1)
                                    $maxUsos = $pictogramasMasUsados[0]['total_usos'] > 0 ? $pictogramasMasUsados[0]['total_usos'] : 1;
                                    $porcentaje = ($p['total_usos'] / $maxUsos) * 100;
                                ?>
                                <tr>
                                    <td class="text-center font-weight-bold">#<?= $contador++ ?></td>
                                    <td class="text-center">
                                        <img src="uploads/img/<?= $p['ruta_imagen'] ?>" class="rounded-circle border" style="width: 45px; height: 45px; object-fit: cover;">
                                    </td>
                                    <td class="align-middle font-weight-bold text-primary nombre-picto">
                                        <?= htmlspecialchars($p['nombre']) ?>
                                    </td>
                                    <td class="align-middle">
                                        <span class="badge badge-light border"><?= htmlspecialchars($p['categoria'] ?? 'Sin categoría') ?></span>
                                    </td>
                                    <td class="align-middle text-center">
                                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $p['total_usos'] ?></div>
                                    </td>
                                    <td class="align-middle">
                                        <div class="progress progress-sm mr-2">
                                            <div class="progress-bar bg-success" role="progressbar" style="width: <?= $porcentaje ?>%"></div>
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

<script>
// Filtro dinámico
document.getElementById('inputBuscar').addEventListener('keyup', function() {
    let filtro = this.value.toLowerCase();
    let filas = document.querySelectorAll('#cuerpoTabla tr');

    filas.forEach(fila => {
        let textoFila = fila.innerText.toLowerCase();
        fila.style.display = textoFila.includes(filtro) ? "" : "none";
    });
});
</script>

