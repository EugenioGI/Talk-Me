<?php

require_once 'middleware/admin_only.php';
include_once("../resources/db/PerfilDB.php");

include "../resources/templates/head.html"; 
include '../resources/templates/headerAdmin.html';

// 2. Carga de datos
$usuarios = PerfilDB::listarTodoParaAdmin();
?>

<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">
        <i class="fas fa-clipboard-list text-primary mr-2"></i>Monitoreo de Perfiles Clínicos
    </h1>

    <div class="row mb-4">
        <div class="col-md-8 mx-auto">
            <div class="input-group input-group-lg shadow-sm">
                <div class="input-group-prepend">
                    <span class="input-group-text bg-white border-0 pl-4">
                        <i class="fas fa-search text-muted"></i>
                    </span>
                </div>
                <input type="text" id="inputBuscar" class="form-control border-0 py-4" 
                       placeholder="Escribe para buscar usuario, nombre o condición..." 
                       style="border-radius: 0 30px 30px 0; font-size: 1.1rem; outline: none;">
            </div>
            <div id="contadorResultados" class="text-center mt-2 small text-muted"></div>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-white">
            <h6 class="m-0 font-weight-bold text-primary">Base de Datos de Usuarios</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-bordered" id="tablaUsuarios" width="100%" cellspacing="0">
                    <thead class="thead-dark text-center">
                        <tr>
                            <th>Usuario</th>
                            <th>Nombre Completo</th>
                            <th>Edad</th>
                            <th>Género</th>
                            <th>Dificultad</th>
                            <th>Condición</th>
                            <th>Nivel Apoyo</th>
                        </tr>
                    </thead>
                    <tbody id="cuerpoTabla">
                        <?php foreach ($usuarios as $u): ?>
                        <tr class="fila-usuario">
                            <td class="align-middle"><strong><?= htmlspecialchars($u['usuario']) ?></strong></td>
                            <td class="align-middle nombre-usuario"><?= htmlspecialchars($u['nombre_completo'] ?? 'No registrado') ?></td>
                            <td class="text-center align-middle"><?= $u['edad'] ?? '-' ?></td>
                            <td class="text-center align-middle"><?= $u['genero'] ?? '-' ?></td>
                            <td class="text-center align-middle">
                                <?php if(!empty($u['dificultad_mayor'])): ?>
                                    <span class="badge badge-danger px-3"><?= htmlspecialchars($u['dificultad_mayor']) ?></span>
                                <?php else: ?>
                                    <span class="text-muted small">Sin asignar</span>
                                <?php endif; ?>
                            </td>
                            <td class="small align-middle condicion-usuario"><?= htmlspecialchars($u['condicion_enfermedad'] ?? '-') ?></td>
                            <td class="font-weight-bold text-center align-middle">
                                <?php 
                                    $clase = 'secondary';
                                    $apoyo = $u['nivel_apoyo'] ?? 'N/A';
                                    if($apoyo == 'Alto') $clase = 'danger';
                                    if($apoyo == 'Moderado') $clase = 'warning';
                                    if($apoyo == 'Bajo') $clase = 'success';
                                ?>
                                <i class="fas fa-circle text-<?= $clase ?> mr-1 small"></i> <?= $apoyo ?>
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

<?php include "../resources/templates/footer.html"; ?>

<script>
// --- LÓGICA DEL BUSCADOR INSTANTÁNEO ---
document.getElementById('inputBuscar').addEventListener('keyup', function() {
    let filtro = this.value.toLowerCase();
    let filas = document.querySelectorAll('#cuerpoTabla tr');
    let encontrados = 0;

    filas.forEach(fila => {
        // Obtenemos todo el texto de la fila para una búsqueda global (nombre, usuario o condición)
        let contenidoFila = fila.textContent.toLowerCase();
        
        if (contenidoFila.includes(filtro)) {
            fila.style.display = ""; // Muestra la fila
            encontrados++;
        } else {
            fila.style.display = "none"; // Oculta la fila
        }
    });

    // Actualizar contador opcional
    const contador = document.getElementById('contadorResultados');
    if(filtro !== "") {
        contador.innerHTML = `Resultados encontrados: <b>${encontrados}</b>`;
    } else {
        contador.innerHTML = "";
    }
});

// Al cargar la página, verificar si viene un nombre desde el Ranking (URL)
window.addEventListener('load', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const searchParam = urlParams.get('search');
    
    if (searchParam) {
        const input = document.getElementById('inputBuscar');
        input.value = searchParam;
        // Disparamos el evento manualmente para que filtre de inmediato
        input.dispatchEvent(new Event('keyup'));
    }
});
</script>

<?php include "../resources/templates/fin.html"; ?>