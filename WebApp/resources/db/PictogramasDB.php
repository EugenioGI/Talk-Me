<?php

include_once("/var/www/html/resources/db/ConexionDB.php");

class PictogramasDB {

    // Obtener todos los pictogramas de un usuario (con nombre de categoría)
    public static function obtenerPorUsuario($id_usuario) {
        $db = Conexion::getInstancia()->getDbh();
        $sql = "SELECT p.id_pictograma, p.nombre, p.ruta_imagen, p.ruta_audio, c.nombre as nombre_categoria 
                FROM Pictogramas p
                LEFT JOIN Categorias c ON p.id_categoria = c.id_categoria
                WHERE p.id_usuario = ?
                ORDER BY p.fecha_creacion DESC";
        
        $stmt = $db->prepare($sql);
        $stmt->execute([$id_usuario]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener un solo pictograma por ID (para el formulario de edición)
    public static function obtenerPorId($id_pictograma) {
        $db = Conexion::getInstancia()->getDbh();
        $sql = "SELECT * FROM Pictogramas WHERE id_pictograma = ?";
        $stmt = $db->prepare($sql);
        $stmt->execute([$id_pictograma]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function insertar($nombre, $imagen, $audio, $categoria, $usuario) {
        try {
            $db = Conexion::getInstancia()->getDbh();
            $sql = "INSERT INTO Pictogramas 
                    (nombre, ruta_imagen, ruta_audio, id_categoria, id_usuario, fecha_creacion)
                    VALUES (?, ?, ?, ?, ?, NOW())";

            $stmt = $db->prepare($sql);
            return $stmt->execute([$nombre, $imagen, $audio, $categoria, $usuario]);
        } catch (PDOException $e) {
            error_log("Error en insertar: " . $e->getMessage());
            return false;
        }
    }

    // NUEVO MÉTODO: Actualizar pictograma
    public static function actualizar($id, $nombre, $imagen, $audio, $categoria) {
        try {
            $db = Conexion::getInstancia()->getDbh();
            // Esta consulta asume que si imagen o audio vienen vacíos, mantienes los anteriores
            $sql = "UPDATE Pictogramas SET 
                    nombre = ?, 
                    ruta_imagen = ?, 
                    ruta_audio = ?, 
                    id_categoria = ? 
                    WHERE id_pictograma = ?";

            $stmt = $db->prepare($sql);
            return $stmt->execute([$nombre, $imagen, $audio, $categoria, $id]);
        } catch (PDOException $e) {
            error_log("Error en actualizar: " . $e->getMessage());
            return false;
        }
    }

    // NUEVO MÉTODO: Eliminar pictograma
    public static function eliminar($id_pictograma) {
        try {
            $db = Conexion::getInstancia()->getDbh();
            $sql = "DELETE FROM Pictogramas WHERE id_pictograma = ?";
            $stmt = $db->prepare($sql);
            return $stmt->execute([$id_pictograma]);
        } catch (PDOException $e) {
            error_log("Error en eliminar: " . $e->getMessage());
            return false;
        }
    }

    // --- Métodos de filtrado existentes ---

    public static function obtenerPorUsuarioYCategoria($id_usuario, $id_categoria = null) {
        $db = Conexion::getInstancia()->getDbh();
        
        $params = [$id_usuario];
        $sql = "SELECT id_pictograma, nombre, ruta_imagen, ruta_audio FROM Pictogramas WHERE id_usuario = ?";
        
        if ($id_categoria) {
            $sql .= " AND id_categoria = ?";
            $params[] = $id_categoria;
        }

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function obtenerCategoriasPorUsuario($id_usuario) {
        $db = Conexion::getInstancia()->getDbh();
        $sql = "SELECT DISTINCT c.id_categoria, c.nombre 
                FROM Pictogramas p
                INNER JOIN Categorias c ON p.id_categoria = c.id_categoria
                WHERE p.id_usuario = ?
                ORDER BY c.nombre ASC";

        $stmt = $db->prepare($sql);
        $stmt->execute([$id_usuario]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public static function obtenerTodos() {
    $db = Conexion::getInstancia()->getDbh();
    // Según tu imagen: p.id_categoria es la llave foránea
    $stmt = $db->prepare("SELECT p.*, c.nombre as nombre_categoria 
                          FROM Pictogramas p 
                          JOIN Categorias c ON p.id_categoria = c.id_categoria");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}



public static function obtenerMasUsadosDetallado() {
    try {
        $db = Conexion::getInstancia()->getDbh();
    
        $sql = "SELECT 
                    p.id_pictograma, 
                    p.nombre, 
                    p.ruta_imagen, 
                    c.nombre as categoria, 
                    COUNT(h.id_historial) as total_usos
                FROM Pictogramas p
                LEFT JOIN Categorias c ON p.id_categoria = c.id_categoria
                LEFT JOIN HistorialUso h ON p.id_pictograma = h.id_pictograma
                GROUP BY p.id_pictograma, p.nombre, p.ruta_imagen, c.nombre
                ORDER BY total_usos DESC";
                
        $stmt = $db->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return [];
    }
}



public static function registrarUso($id_usuario, $id_pictograma) {
    try {
        $db = Conexion::getInstancia()->getDbh();
        $sql = "INSERT INTO HistorialUso (id_usuario, id_pictograma, fecha) VALUES (?, ?, NOW())";
        $stmt = $db->prepare($sql);
        return $stmt->execute([$id_usuario, $id_pictograma]);
    } catch (PDOException $e) {
        return false;
    }
}

public static function insertarHistorial($id_usuario, $id_pictograma) {
    try {
        $db = Conexion::getInstancia()->getDbh();
        $sql = "INSERT INTO HistorialUso (id_usuario, id_pictograma, fecha) VALUES (?, ?, NOW())";
        $stmt = $db->prepare($sql);
        return $stmt->execute([$id_usuario, $id_pictograma]);
    } catch (PDOException $e) {
        return false;
    }
}


// Para guardar la frase nueva
public static function guardarFraseCompleta($id_usuario, $nombre, $id_picto) {
    $db = Conexion::getInstancia()->getDbh();
    
    // Insertamos la cabecera
    $stmt = $db->prepare("INSERT INTO Frases (id_usuario, nombre_frase) VALUES (?, ?)");
    $stmt->execute([$id_usuario, $nombre]);
    $id_frase = $db->lastInsertId();
    
    // Insertamos el primer picto (orden 1)
    $stmtDetalle = $db->prepare("INSERT INTO FraseDetalle (id_frase, id_pictograma, orden) VALUES (?, ?, 1)");
    return $stmtDetalle->execute([$id_frase, $id_picto]);
}

// Para listar las frases en la pantalla de favoritos
public static function obtenerFrases($id_usuario) {
    $db = Conexion::getInstancia()->getDbh();
    $stmt = $db->prepare("SELECT * FROM Frases WHERE id_usuario = ? ORDER BY fecha_creacion DESC");
    $stmt->execute([$id_usuario]);
    return $stmt->fetchAll();
}


}