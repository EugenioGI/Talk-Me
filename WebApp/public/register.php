<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';
include '../resources/lib/sanitizacion.php';
include '../resources/db/PersonaDB.php';
include '../resources/db/UsuarioDB.php';

$errorMensaje = "";
$resEnviarCorreo = false;

// mantener datos
$nombre = $_POST['nombre'] ?? "";
$paterno = $_POST['paterno'] ?? "";
$materno = $_POST['materno'] ?? "";
$email = $_POST['email'] ?? "";
$usuario = $_POST['usuario'] ?? "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nombre  = sanitizacion($nombre);
    $paterno = sanitizacion($paterno);
    $materno = sanitizacion($materno);
    $usuario = sanitizacion($usuario);
    $email   = sanitizacion($email);

    $_POST['apellidos'] = $paterno . " " . $materno;

    //  VALIDACIÓN BACKEND
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errorMensaje = "Correo inválido";
    } 
    else if (!PersonaDB::existeCorreo($email) && !UsuarioDB::existeUsuario($usuario)) {

        $_POST['contrasenia'] = password_hash($_POST['contrasenia'], PASSWORD_BCRYPT);

        PersonaDB::insertaPersona($_POST);
        $idPersonaInsertada = PersonaDB::getUltimoIdInsertado();

        $resInsertUsuario = UsuarioDB::insertaUsuario($idPersonaInsertada, $_POST);
        $idUsuarioInsertado = UsuarioDB::getIdUltimoInsertado();

        if ($resInsertUsuario) {

            $mail = new PHPMailer(true);

            try {
                $mail->isSMTP();
                $mail->Host = 'sandbox.smtp.mailtrap.io';
                $mail->SMTPAuth = true;
                $mail->Port = 2525;
                $mail->Username = 'f26f752c2810dd';
                $mail->Password = '143539fac0bb35';

                $mail->setFrom('registro@test.com', 'Sistema');
                $mail->addAddress($email, $nombre);

                $link = "http://" . $_SERVER['HTTP_HOST'] . "/public/usuario_activacion.php?id=" . $idUsuarioInsertado;

                $mail->isHTML(true);
                $mail->Subject = 'Activa tu cuenta';
                $mail->Body = "Hola <b>$nombre</b><br><br><a href='$link'>Activar cuenta</a>";

                $resEnviarCorreo = $mail->send();

            } catch (Exception $e) {
                $errorMensaje = "Error al enviar correo";
            }
        }

    } else {
        $errorMensaje = "Correo o usuario ya existen";
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
<meta charset="UTF-8">
<title>Registro</title>

<link href="../../css/sb-admin-2.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
.is-invalid {
    border: 2px solid #e74a3b !important;
}
</style>

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
        <div class="card o-hidden border-0 shadow-lg my-5">
            <div class="card-body p-0">
                <div class="row">
                    
                    <div class="col-lg-5 d-none d-lg-block bg-login-image"></div>

                    <div class="col-lg-7">
                        <div class="p-5">
                            <div class="text-center">
                                <h1 class="h4 text-gray-900 mb-4">Crear cuenta</h1>
                            </div>

                            <form class="user" method="POST" id="formRegistro" novalidate>
                                <div class="form-group row">
                                    <div class="col-sm-6">
                                        <input type="text" name="nombre" id="nombre"
                                            value="<?= htmlspecialchars($nombre) ?>"
                                            class="form-control form-control-user" placeholder="Nombre">
                                    </div>
                                    <div class="col-sm-6">
                                        <input type="text" name="paterno" id="paterno"
                                            value="<?= htmlspecialchars($paterno) ?>"
                                            class="form-control form-control-user" placeholder="Apellido paterno">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <input type="text" name="materno" id="materno"
                                        value="<?= htmlspecialchars($materno) ?>"
                                        class="form-control form-control-user" placeholder="Apellido materno">
                                </div>

                                <div class="form-group">
                                    <input type="email" name="email" id="email"
                                        value="<?= htmlspecialchars($email) ?>"
                                        class="form-control form-control-user" placeholder="Correo">
                                </div>

                                <div class="form-group">
                                    <input type="text" name="usuario" id="usuario"
                                        value="<?= htmlspecialchars($usuario) ?>"
                                        class="form-control form-control-user" placeholder="Usuario">
                                </div>

                                <div class="form-group row">
                                    <div class="col-sm-6">
                                        <input type="password" id="contrasenia" name="contrasenia"
                                            class="form-control form-control-user" placeholder="Contraseña">
                                    </div>
                                    <div class="col-sm-6">
                                        <input type="password" id="contrasenia2"
                                            class="form-control form-control-user" placeholder="Confirmar">
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary btn-user btn-block">
                                    Registrar
                                </button>
                            </form>
                            
                            <hr>
                            <div class="text-center">
                                <a class="small" href="login.php">¿Ya tienes cuenta? ¡Inicia sesión!</a>
                            </div>
                        </div>
                    </div> </div> </div>
        </div>
    </div>

<script>
const form = document.getElementById("formRegistro");

form.addEventListener("submit", function(e) {

    let campos = ["nombre","paterno","materno","email","usuario","contrasenia","contrasenia2"];
    let valido = true;

    campos.forEach(id => {
        let input = document.getElementById(id);
        input.classList.remove("is-invalid");

        if (!input.value.trim()) {
            input.classList.add("is-invalid");
            valido = false;
        }
    });

    let email = document.getElementById("email");
    let regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    if (email.value && !regex.test(email.value)) {
        email.classList.add("is-invalid");
        Swal.fire({
            icon: 'error',
            title: 'Correo incorrecto ❗',
            text: 'Ingresa un correo válido'
        });
        e.preventDefault();
        return;
    }

    let pass = document.getElementById("contrasenia");
    let confirm = document.getElementById("contrasenia2");

    if (pass.value !== confirm.value) {
        pass.classList.add("is-invalid");
        confirm.classList.add("is-invalid");

        Swal.fire({
            icon: 'warning',
            title: 'Contraseñas no coinciden ❗'
        });

        e.preventDefault();
        return;
    }

    if (!valido) {
        Swal.fire({
            icon: 'error',
            title: 'Campos incompletos ❗',
            text: 'Llena todos los campos'
        });
        e.preventDefault();
    }

});
</script>

<!-- ERROR -->
<?php if (!empty($errorMensaje)): ?>
<script>
Swal.fire({
    icon: 'error',
    title: 'Error',
    text: '<?= $errorMensaje ?>'
});
</script>
<?php endif; ?>

<!-- ÉXITO -->
<?php if ($resEnviarCorreo): ?>
<script>
Swal.fire({
    title: "¡Registro exitoso!",
    text: "Revisa tu correo para activar tu cuenta",
    icon: "success"
}).then(() => {
    window.location = "login.php";
});
</script>
<?php endif; ?>

</body>
</html>