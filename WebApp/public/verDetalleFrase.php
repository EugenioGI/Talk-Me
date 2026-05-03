<?php
require_once 'middleware/user_only.php';
require_once("/var/www/html/resources/db/FrasesDB.php");

$id_frase = $_GET['id'] ?? null;
$id_usuario = $_SESSION['id_usuario'] ?? null;

if (!$id_frase || !$id_usuario) {
    header("Location: gestionarFrases.php");
    exit();
}

// Obtenemos los pictogramas de esta frase
$detalles = FrasesDB::obtenerDetallesDeFrase($id_frase);

include_once("/var/www/html/resources/templates/head.html");
include_once("/var/www/html/resources/templates/header.html");
?>

<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Visualizar Frase</h1>
        <a href="gestionarFrases.php" class="btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm"></i> Volver a Mis Frases
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-primary text-white d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold">Secuencia de la Frase</h6>
            <span class="badge badge-light"><?= count($detalles) ?> Pictogramas</span>
        </div>
        <div class="card-body bg-light">
            <div class="d-flex flex-wrap align-items-center justify-content-center">
                <?php if (empty($detalles)): ?>
                    <p class="text-center w-100 py-5">Esta frase no tiene pictogramas guardados.</p>
                <?php else: ?>
                    <?php 
                    $total = count($detalles);
                    foreach ($detalles as $index => $item): 
                    ?>
                        <div class="card m-2 shadow-sm picto-card" 
                             style="width: 140px; cursor: pointer; transition: transform 0.2s;" 
                             onclick="reproducirAudio('<?= $item['ruta_audio'] ?>', this)">
                            <img src="uploads/img/<?= htmlspecialchars($item['ruta_imagen']) ?>" 
                                class="card-img-top p-3" 
                                alt="<?= htmlspecialchars($item['nombre']) ?>">
                            <div class="card-footer bg-white p-1 text-center">
                                <small class="font-weight-bold text-uppercase text-primary" style="font-size: 0.7rem;">
                                    <?= htmlspecialchars($item['nombre']) ?>
                                </small>
                            </div>
                        </div>
                        
                        <?php if ($index < $total - 1): ?>
                            <i class="fas fa-chevron-right text-gray-400 mx-1 d-none d-md-block"></i>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
        <div class="card-footer text-center bg-white">
            <button class="btn btn-success btn-lg px-5 shadow-sm" onclick="reproducirTodo()">
                <i class="fas fa-volume-up mr-2"></i> Escuchar Frase Completa
            </button>
        </div>
    </div>
</div>
</div>

<style>
    .picto-card:hover { transform: scale(1.05); border-color: #4e73df; }
    .playing { border: 3px solid #1cc88a !important; transform: scale(1.1); }
</style>

<script>
    // Reproducir un solo audio con efecto visual
    function reproducirAudio(nombreArchivo, elemento = null) {
        if (!nombreArchivo) return;
        
        // CORRECCIÓN: Añadimos la ruta de la carpeta de audios
        const rutaCompleta = 'uploads/audio/' + nombreArchivo;
        const audio = new Audio(rutaCompleta);
        
        if (elemento) elemento.classList.add('playing');
        
        audio.play().catch(e => console.error("Error audio:", e));
        
        audio.onended = () => {
            if (elemento) elemento.classList.remove('playing');
        };
    }

    // Reproducir secuencia completa
    function reproducirTodo() {
        const cards = document.querySelectorAll('.picto-card');
        const audios = [
            <?php foreach ($detalles as $item): ?>
                "uploads/audio/<?= $item['ruta_audio'] ?>", // CORRECCIÓN: Ruta completa
            <?php endforeach; ?>
        ];
        
        let i = 0;
        function playNext() {
            if (i < audios.length) {
                cards.forEach(c => c.classList.remove('playing'));
                
                if (audios[i]) {
                    const a = new Audio(audios[i]);
                    cards[i].classList.add('playing');
                    
                    a.onended = () => {
                        cards[i].classList.remove('playing');
                        i++;
                        playNext();
                    };
                    a.play().catch(e => {
                        console.log("Error al reproducir:", e);
                        i++; 
                        playNext(); 
                    });
                } else {
                    i++;
                    playNext();
                }
            } else {
                cards.forEach(c => c.classList.remove('playing'));
            }
        }
        playNext();
    }
</script>
<?php include_once("/var/www/html/resources/templates/footer.html"); ?>