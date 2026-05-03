<?php
include '../resources/db/UsuarioDB.php';

$id_usuario = $_GET['id'] ?? null;
$mensaje = "";
$tipo = "";
$titulo = "";

if ($id_usuario) {
    // 1. Llamamos al método para activar
    $activado = UsuarioDB::activarUsuario($id_usuario);

    if ($activado) {
        $titulo = "¡Cuenta Activada!";
        $mensaje = "Tu cuenta ha sido confirmada con éxito. Ya puedes iniciar sesión.";
        $tipo = "success";
    } else {
        $titulo = "Error de activación";
        $mensaje = "No se pudo activar la cuenta o el enlace es inválido.";
        $tipo = "error";
    }
} else {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Activación - Talk-Me</title>
    <link href="../css/sb-admin-2.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .bg-activation-image {
            background: url("../img/logo.png");
            background-position: center; 
            background-size: contain;
            background-repeat: no-repeat; 
            background-color: #f8f9fc;
        }
    </style>
</head>

<body class="bg-gradient-primary">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-10 col-lg-12 col-md-9">
                <div class="card o-hidden border-0 shadow-lg my-5">
                    <div class="card-body p-0">
                        <div class="row">
                            <div class="col-lg-6 d-none d-lg-block bg-activation-image"></div>
                            <div class="col-lg-6">
                                <div class="p-5 text-center">
                                    <h1 class="h4 text-gray-900 mb-4"><?= $titulo ?></h1>
                                    <p class="mb-4"><?= $mensaje ?></p>
                                    <a href="login.php" class="btn btn-primary btn-user btn-block">
                                        Ir al Login
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Mostramos el SweetAlert automáticamente al cargar
        Swal.fire({
            icon: '<?= $tipo ?>',
            title: '<?= $titulo ?>',
            text: '<?= $mensaje ?>',
            confirmButtonText: 'Entendido'
        }).then((result) => {
            if ('<?= $tipo ?>' === 'success') {
                window.location = "login.php";
            }
        });
    </script>
</body>
</html>