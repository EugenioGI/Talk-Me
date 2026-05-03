<?php

require_once 'middleware/user_only.php';
include_once("../resources/db/FavoritosDB.php");

$id_usuario = $_SESSION['id_usuario'] ?? null;
if (!$id_usuario) { header("Location: login.php"); exit(); }

include "../resources/templates/head.html"; 
include '../resources/templates/header.html';

$favoritos = FavoritosDB::listarFavoritosDetallados($id_usuario);
?>

<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-heart text-danger mr-2"></i>Mis Pictogramas Favoritos</h1>
    </div>

    <div class="row">
        <?php if (empty($favoritos)): ?>
            <div class="col-12 text-center py-5">
                <i class="fas fa-star fa-4x text-gray-300 mb-3"></i>
                <p class="lead text-gray-500">Aún no tienes pictogramas favoritos. <br> ¡Agrega algunos haciendo clic en el corazón!</p>
            </div>
        <?php else: ?>
            <?php foreach ($favoritos as $fav): ?>
                <div class="col-xl-2 col-lg-3 col-md-4 col-6 mb-4" id="fav-card-<?= $fav['id_pictograma'] ?>">
                    <div class="card border-bottom-danger shadow h-100 py-2 card-picto" style="cursor: pointer;" onclick="reproducir('uploads/audio/<?= $fav['ruta_audio'] ?>')">
                        <div class="card-body text-center">
                            <img src="uploads/img/<?= $fav['ruta_imagen'] ?>" class="img-fluid mb-2 rounded" style="max-height: 100px;">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                <?= htmlspecialchars($fav['nombre']) ?>
                            </div>
                            <button class="btn btn-sm btn-circle btn-light mt-2" onclick="quitarFavorito(event, <?= $fav['id_pictograma'] ?>)" title="Quitar de favoritos">
                                <i class="fas fa-heart-broken text-muted"></i>
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function reproducir(ruta) {
    const audio = new Audio(ruta);
    audio.play();
}

function quitarFavorito(event, idPicto) {
    event.stopPropagation(); // Evita que se dispare el sonido al hacer clic en el botón de borrar
    
    fetch(`procesar_quitar_favorito.php?id=${idPicto}`)
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                document.getElementById(`fav-card-${idPicto}`).remove();
            }
        });
}
</script>

<style>
    .card-picto:hover {
        transform: scale(1.05);
        transition: all 0.2s ease-in-out;
        border: 2px solid #e74a3b;
    }
</style>

<?php 
include "../resources/templates/footer.html"; 
include "../resources/templates/fin.html"; 
?>