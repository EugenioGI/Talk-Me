<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';
include '../resources/db/UsuarioDB.php';
include '../resources/lib/sanitizacion.php';

$mensajeRespuesta = "";
$tipoAlerta = ""; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = sanitizacion($_POST["email"]);


    include '../resources/db/PersonaDB.php';
    
    if (PersonaDB::existeCorreo($email)) {
        $mail = new PHPMailer(true);

        try {
         
            $mail->isSMTP();
            $mail->Host = 'sandbox.smtp.mailtrap.io';
            $mail->SMTPAuth = true;
            $mail->Port = 2525;
            $mail->Username = 'f26f752c2810dd'; 
            $mail->Password = '143539fac0bb35';

            $mail->setFrom('soporte@talkme.com', 'Talk-Me Soporte');
            $mail->addAddress($email);

            $link = "http://" . $_SERVER['HTTP_HOST'] . "/public/restablecer_password.php?email=" . urlencode($email);

            $mail->isHTML(true);
            $mail->Subject = 'Recuperar Contrasena - Talk-Me';
            $mail->Body    = "<h1>Recuperación de contraseña</h1>
                              <p>Haz clic en el siguiente enlace para cambiar tu contraseña:</p>
                              <a href='$link'>Restablecer Contraseña</a>";

            $mail->send();
            $mensajeRespuesta = "¡Correo enviado! Revisa tu bandeja de entrada.";
            $tipoAlerta = "success";
        } catch (Exception $e) {
            $mensajeRespuesta = "Error al enviar el correo.";
            $tipoAlerta = "error";
        }
    } else {
        $mensajeRespuesta = "El correo no está registrado en nuestro sistema.";
        $tipoAlerta = "error";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Recuperar Contraseña - Talk-Me</title>

    <link href="../../vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link href="../../css/sb-admin-2.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        .bg-password-image {
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
                            <div class="col-lg-6 d-none d-lg-block bg-password-image"></div>
                            
                            <div class="col-lg-6">
                                <div class="p-5">
                                    <div class="text-center">
                                        <h1 class="h4 text-gray-900 mb-2">¿Olvidaste tu contraseña?</h1>
                                        <p class="mb-4">Ingresa tu correo y te enviaremos un enlace para restablecerla.</p>
                                    </div>

                                    <form class="user" method="POST" action="">
                                        <div class="form-group">
                                            <input type="email" name="email" class="form-control form-control-user"
                                                placeholder="Ingresa tu correo electrónico..." required>
                                        </div>
                                        <button type="submit" class="btn btn-primary btn-user btn-block">
                                            Enviar correo
                                        </button>
                                    </form>
                                    <hr>
                                    <div class="text-center">
                                        <a class="small" href="register.php">¡Crea una cuenta!</a>
                                    </div>
                                    <div class="text-center">
                                        <a class="small" href="login.php">¿Ya tienes cuenta? Login</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php if ($mensajeRespuesta != ""): ?>
    <script>
        Swal.fire({
            icon: '<?= $tipoAlerta ?>',
            title: '<?= ($tipoAlerta == "success") ? "Éxito" : "Atención" ?>',
            text: '<?= $mensajeRespuesta ?>'
        });
    </script>
    <?php endif; ?>

    <script src="../../vendor/jquery/jquery.min.js"></script>
    <script src="../../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>