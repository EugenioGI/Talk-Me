<?php

require_once 'middleware/admin_only.php';
include_once("../resources/db/FavoritosDB.php");
include "../resources/templates/head.html"; 
include '../resources/templates/headerAdmin.html';


$ranking = FavoritosDB::obtenerRankingFavoritos();
?>

<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-chart-pie text-primary mr-2"></i>Análisis de Pictogramas Favoritos
        </h1>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between bg-white">
            <h6 class="m-0 font-weight-bold text-primary">Relación de Uso: Pictogramas vs Usuarios</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead class="thead-dark text-center">
                        <tr>
                            <th>Picto</th>
                            <th>Nombre</th>
                            <th>Categoría</th>
                            <th>Total Uso</th>
                            <th>Usuarios que lo tienen como Favorito</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($ranking as $r): ?>
                        <tr>
                            <td class="text-center">
                                <img src="uploads/img/<?= $r['ruta_imagen'] ?>" class="rounded shadow-sm" style="width: 55px; height: 55px; object-fit: cover; border: 1px solid #ddd;">
                            </td>
                            
                            <td class="align-middle font-weight-bold">
                                <?= htmlspecialchars($r['nombre']) ?>
                            </td>
                            
                            <td class="align-middle text-center">
                                <span class="badge badge-light p-2 border">
                                    <?= htmlspecialchars($r['categoria'] ?? 'Sin Categoría') ?>
                                </span>
                            </td>
                            
                            <td class="align-middle text-center">
                                <div class="h5 mb-0 font-weight-bold text-primary"><?= $r['total_favoritos'] ?></div>
                                <small class="text-muted text-uppercase">votos</small>
                            </td>

                            <td class="align-middle">
                                <?php 
                                    if (!empty($r['nombres_usuarios'])):
                                        // Separamos los nombres que vienen concatenados desde el SQL por comas
                                        $nombres = explode(', ', $r['nombres_usuarios']);
                                        foreach ($nombres as $nombre): 
                                ?>
                                    <a href="admin_usuarios.php?search=<?= urlencode($nombre) ?>" 
                                       class="btn btn-sm btn-outline-info mb-1 mr-1 shadow-sm" 
                                       title="Ver en lista de usuarios">
                                        <i class="fas fa-search fa-xs mr-1"></i> <?= htmlspecialchars($nombre) ?>
                                    </a>
                                <?php 
                                        endforeach; 
                                    else:
                                ?>
                                    <span class="text-muted italic small">Sin usuarios asignados</span>
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

<?php 
include "../resources/templates/footer.html"; 
?>

<script>
    $(document).ready(function() {
        $('#dataTable').DataTable({
            "order": [[ 3, "desc" ]], // Ordenar por la columna 'Total Uso' por defecto
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json"
            }
        });
    });
</script>

<?php 
include "../resources/templates/fin.html"; 
?>