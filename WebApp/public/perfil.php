<?php
require_once 'middleware/user_only.php';
include_once("../resources/db/ConexionDB.php");
include_once("../resources/db/PerfilDB.php"); 

if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}

// 1. CARGAMOS EL HEAD Y EL HEADER (Esto trae el CSS)
include "../resources/templates/head.html"; 

// Elegimos el header según el rol
if (isset($_SESSION['rol']) && $_SESSION['rol'] == 'administrador') {
    include '../resources/templates/headerAdmin.html';
} else {
    include '../resources/templates/header.html';
}

$id_usuario_perfil = $_GET['id'] ?? $_SESSION['id_usuario'];
$id_sesion = $_SESSION['id_usuario'];
$rol_sesion = $_SESSION['rol'] ?? 'usuario';

if ($id_usuario_perfil != $id_sesion && $rol_sesion !== 'administrador') {
    $id_usuario_perfil = $id_sesion; 
}

$perfil = PerfilDB::obtenerPerfilPorUsuario($id_usuario_perfil);
$mensaje = $_GET['msj'] ?? null;
?>

<div class="container-fluid">

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-id-card text-primary mr-2"></i>Ficha de Usuario
        </h1>
        <?php if($mensaje == 'ok'): ?>
            <div class="alert alert-success alert-dismissible fade show mb-0" role="alert">
                <strong>¡Guardado!</strong> Los datos se actualizaron correctamente.
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
        <?php endif; ?>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4 border-bottom-primary">
                <div class="card-header py-3 bg-white">
                    <h6 class="m-0 font-weight-bold text-primary">Información Personal y Médica</h6>
                </div>
                <div class="card-body">
                    <form action="procesar_perfil.php" method="POST">
                        <input type="hidden" name="id_usuario" value="<?= $id_usuario_perfil ?>">

                        <div class="row mb-4">
                            <div class="col-md-8">
                                <label class="small font-weight-bold">Nombre Completo</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                                    </div>
                                    <input type="text" name="nombre" class="form-control" 
                                           value="<?= htmlspecialchars($perfil['nombre_completo'] ?? '') ?>" 
                                           placeholder="Ej: Juan Pérez López" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="small font-weight-bold">Edad</label>
                                <input type="number" name="edad" class="form-control" 
                                       value="<?= $perfil['edad'] ?? '' ?>" placeholder="0">
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="small font-weight-bold text-danger">Dificultad Mayor</label>
                                <select name="dificultad" class="form-control border-left-danger" required>
                                    <option value="" disabled <?= !isset($perfil['dificultad_mayor']) ? 'selected' : '' ?>>Seleccionar...</option>
                                    <option value="Motriz" <?= ($perfil['dificultad_mayor'] ?? '') == 'Motriz' ? 'selected' : '' ?>>Motriz</option>
                                    <option value="Del Habla" <?= ($perfil['dificultad_mayor'] ?? '') == 'Del Habla' ? 'selected' : '' ?>>Del Habla</option>
                                    <option value="Visual" <?= ($perfil['dificultad_mayor'] ?? '') == 'Visual' ? 'selected' : '' ?>>Visual</option>
                                    <option value="Cognitiva" <?= ($perfil['dificultad_mayor'] ?? '') == 'Cognitiva' ? 'selected' : '' ?>>Cognitiva</option>
                                    <option value="Auditiva" <?= ($perfil['dificultad_mayor'] ?? '') == 'Auditiva' ? 'selected' : '' ?>>Auditiva</option>
                                    <option value="Otra" <?= ($perfil['dificultad_mayor'] ?? '') == 'Otra' ? 'selected' : '' ?>>Otra / Varias</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="small font-weight-bold text-info">Nivel de Apoyo</label>
                                <select name="apoyo" class="form-control border-left-info">
                                    <option value="Bajo" <?= ($perfil['nivel_apoyo'] ?? '') == 'Bajo' ? 'selected' : '' ?>>Bajo (Independiente)</option>
                                    <option value="Moderado" <?= ($perfil['nivel_apoyo'] ?? '') == 'Moderado' ? 'selected' : '' ?>>Moderado (Asistido)</option>
                                    <option value="Alto" <?= ($perfil['nivel_apoyo'] ?? '') == 'Alto' ? 'selected' : '' ?>>Alto (Dependencia)</option>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="small font-weight-bold">Género</label>
                                <select name="genero" class="form-control">
                                    <option value="Masculino" <?= ($perfil['genero'] ?? '') == 'Masculino' ? 'selected' : '' ?>>Masculino</option>
                                    <option value="Femenino" <?= ($perfil['genero'] ?? '') == 'Femenino' ? 'selected' : '' ?>>Femenino</option>
                                    <option value="Otro" <?= ($perfil['genero'] ?? '') == 'Otro' ? 'selected' : '' ?>>Otro</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="small font-weight-bold">Condición / Diagnóstico</label>
                                <input type="text" name="condicion" class="form-control" 
                                       value="<?= htmlspecialchars($perfil['condicion_enfermedad'] ?? '') ?>" 
                                       placeholder="Ej: TEA, Afasia, TDL...">
                            </div>
                        </div>

                        <hr>
                        <button type="submit" class="btn btn-primary btn-icon-split shadow-sm float-right">
                            <span class="icon text-white-50"><i class="fas fa-check"></i></span>
                            <span class="text">Guardar Cambios</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow mb-4 text-center py-5">
                <div class="mb-4">
                    <img class="img-profile rounded-circle shadow border" 
                         src="../img/undraw_profile.svg" style="width: 150px; background: #f8f9fc;">
                </div>
                <h5 class="font-weight-bold text-gray-800"><?= htmlspecialchars($perfil['nombre_completo'] ?? 'Nombre no asignado') ?></h5>
                <p class="badge badge-primary px-3 py-2">Usuario Activo</p>
                <hr class="my-4">
                <p class="small text-muted text-justify px-3">
                    <i class="fas fa-info-circle mr-1"></i> 
                    Esta información ayuda a adaptar los pictogramas a las necesidades específicas.
                </p>
            </div>
        </div>
    </div>
</div>
</div>

<?php 
// 2. CARGAMOS EL FOOTER (Esto trae el JS y cierra las etiquetas)
include "../resources/templates/footer.html"; 
include "../resources/templates/fin.html"; 
?>