<?php
include '../resources/db/UsuarioDB.php';
include '../resources/db/PersonaDB.php';
include '../resources/lib/sanitizacion.php';

$email = $_GET['email'] ?? '';
$mensaje = "";
$tipo = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $emailPost = $_POST['email'];
    $pass1 = $_POST['pass1'];
    $pass2 = $_POST['pass2'];

    if ($pass1 !== $pass2) {
        $mensaje = "Las contraseñas no coinciden.";
        $tipo = "error";
    } else {
        // Encriptar la nueva contraseña
        $passwordHash = password_hash($pass1, PASSWORD_BCRYPT);
        
        // 1. Necesitas este método en UsuarioDB.php
        $actualizado = UsuarioDB::actualizarPasswordPorEmail($emailPost, $passwordHash);

        if ($actualizado) {
            $mensaje = "¡Contraseña actualizada con éxito!";
            $tipo = "success";
        } else {
            $mensaje = "No se pudo actualizar la contraseña. Intenta de nuevo.";
            $tipo = "error";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Restablecer Contraseña - Talk-Me</title>
    <link href="../css/sb-admin-2.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .bg-reset-image {
            background: url("../img/logo.png");
            background-position: center; background-size: contain;
            background-repeat: no-repeat; background-color: #f8f9fc;
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
                            <div class="col-lg-6 d-none d-lg-block bg-reset-image"></div>
                            <div class="col-lg-6">
                                <div class="p-5">
                                    <div class="text-center">
                                        <h1 class="h4 text-gray-900 mb-4">Nueva Contraseña</h1>
                                        <p>Crea una nueva clave para <b><?= htmlspecialchars($email) ?></b></p>
                                    </div>
                                    
                                    <form class="user" method="POST">
                                        <input type="hidden" name="email" value="<?= htmlspecialchars($email) ?>">
                                        
                                        <div class="form-group">
                                            <input type="password" name="pass1" class="form-control form-control-user" 
                                                placeholder="Nueva contraseña" required>
                                        </div>
                                        <div class="form-group">
                                            <input type="password" name="pass2" class="form-control form-control-user" 
                                                placeholder="Confirmar nueva contraseña" required>
                                        </div>
                                        
                                        <button type="submit" class="btn btn-primary btn-user btn-block">
                                            Actualizar Contraseña
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php if ($mensaje != ""): ?>
    <script>
        Swal.fire({
            icon: '<?= $tipo ?>',
            title: '<?= ($tipo == "success") ? "¡Hecho!" : "Error" ?>',
            text: '<?= $mensaje ?>'
        }).then(() => {
            <?php if ($tipo == "success"): ?>
                window.location = "login.php";
            <?php endif; ?>
        });
    </script>
    <?php endif; ?>
</body>
</html>