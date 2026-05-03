<?php 
session_start();

$usuarioSesion = $_SESSION['usuario'] ?? "Invitado";
$rol = $_SESSION['rol'] ?? 'usuario';

include "../resources/templates/head.html"; 

// Header según rol
if ($rol == 'administrador') {
    include '../resources/templates/headerAdmin.html';
} else {
    include '../resources/templates/header.html';
}
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-lg-6">

            <div class="card shadow-lg border-0 rounded-lg text-center">
                <div class="card-body p-5">

                    <i class="fas fa-times-circle fa-4x text-danger mb-4"></i>

                    <h2 class="text-dark">Acceso Denegado</h2>

                    <div class="d-grid gap-2 mt-4">
                        <a href="index.php" class="btn btn-secondary">
                            Ir al inicio
                        </a>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>
</div>

<?php
include "../resources/templates/footer.html"; 
include "../resources/templates/fin.html"; 
?>