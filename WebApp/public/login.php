<?php
session_start();

// Si ya está logueado, mandarlo al index directamente
if (isset($_SESSION['id_usuario'])) {
    header("Location: index.php");
    exit();
}

require '../vendor/autoload.php';
include '../resources/db/UsuarioDB.php';
include '../resources/lib/sanitizacion.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = sanitizacion($_POST["usuario"]);
    $password = $_POST["password"];

    $user = UsuarioDB::getUserByUsername($usuario);

    if ($user && password_verify($password, $user['password'])) {
        if ($user['activo']) {
            
            $_SESSION['usuario'] = $user['usuario'];
            $_SESSION['id_usuario'] = $user['id_usuario']; 

            $_SESSION['rol'] = $user['rol']; 

            header("Location: index.php");
            exit();
        } else {
            $error = "Cuenta no activa.";
        }
    } else {
        $error = "Credenciales incorrectas.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Login - Talk-Me</title>

    <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link href="../css/sb-admin-2.min.css" rel="stylesheet">


    <style>
        .bg-login-image {
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
                            <div class="col-lg-6 d-none d-lg-block bg-login-image"></div>
                            <div class="col-lg-6">
                                <div class="p-5">
                                    <div class="text-center">
                                        <h1 class="h4 text-gray-900 mb-4">¡Bienvenido de nuevo!</h1>
                                    </div>

                                    <?php if (!empty($error)): ?>
                                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                            <?= $error ?>
                                        </div>
                                    <?php endif; ?>

                                    <form class="user" method="POST" action="login.php">
                                        <div class="form-group">
                                            <input type="text" name="usuario" class="form-control form-control-user" placeholder="Ingresa tu usuario..." required autofocus>
                                        </div>
                                        <div class="form-group">
                                            <input type="password" name="password" class="form-control form-control-user" placeholder="Contraseña" required>
                                        </div>
                                        <button type="submit" class="btn btn-primary btn-user btn-block">
                                            Entrar
                                        </button>
                                    </form>
                                    <hr>
                                    <div class="text-center">
                                        <a class="small" href="register.php">¿No tienes cuenta? ¡Regístrate!</a>
                                    </div>
                                    <div class="text-center">
                                        <a class="small" href="forgot-password.php">¿Olvidaste tu contraseña? ¡Recuperala!</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="../vendor/jquery/jquery.min.js"></script>
    <script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="../js/sb-admin-2.min.js"></script>
</body>
</html>