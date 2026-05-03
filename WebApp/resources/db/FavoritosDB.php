<?php
include_once("ConexionDB.php");

class FavoritosDB {

    /**
     * Agrega un pictograma a la tabla de favoritos de un usuario
     */
    public static function agregar($id_usuario, $id_pictograma) {
        try {
            $db = Conexion::getInstancia()->getDbh();
            
            // Primero verificamos si ya existe para no duplicar
            if (self::esFavorito($id_usuario, $id_pictograma)) {
                return 'exists';
            }

            $sql = "INSERT INTO Favoritos (id_usuario, id_pictograma) VALUES (?, ?)";
            $stmt = $db->prepare($sql);
            return $stmt->execute([$id_usuario, $id_pictograma]) ? 'success' : 'error';
        } catch (Exception $e) {
            error_log("Error en FavoritosDB::agregar -> " . $e->getMessage());
            return 'error';
        }
    }

    /**
     * Verifica si un pictograma ya está marcado como favorito
     */
    public static function esFavorito($id_usuario, $id_pictograma) {
        $db = Conexion::getInstancia()->getDbh();
        $sql = "SELECT id_favorito FROM Favoritos WHERE id_usuario = ? AND id_pictograma = ?";
        $stmt = $db->prepare($sql);
        $stmt->execute([$id_usuario, $id_pictograma]);
        return $stmt->fetch() !== false;
    }

    /**
     * Lista todos los favoritos de un usuario (para la vista de admin o del usuario)
     */
    public static function obtenerPorUsuario($id_usuario) {
        try {
            $db = Conexion::getInstancia()->getDbh();
            $sql = "SELECT p.*, f.id_favorito 
                    FROM Favoritos f
                    JOIN Pictogramas p ON f.id_pictograma = p.id_pictograma
                    WHERE f.id_usuario = ?";
            $stmt = $db->prepare($sql);
            $stmt->execute([$id_usuario]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }

    public static function listarFavoritosDetallados($id_usuario) {
    try {
        $db = Conexion::getInstancia()->getDbh();
        // Hacemos un JOIN para traer los datos del pictograma desde la tabla de favoritos
        $sql = "SELECT p.id_pictograma, p.nombre, p.ruta_imagen, p.ruta_audio 
                FROM Favoritos f
                JOIN Pictogramas p ON f.id_pictograma = p.id_pictograma
                WHERE f.id_usuario = ?
                ORDER BY f.id_favorito DESC";
        $stmt = $db->prepare($sql);
        $stmt->execute([$id_usuario]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        return [];
    }
}

public static function obtenerRankingFavoritos() {
    try {
        $db = Conexion::getInstancia()->getDbh();
        // GROUP_CONCAT es la clave aquí para listar los usuarios en una sola celda
        $sql = "SELECT p.nombre, p.ruta_imagen, c.nombre AS categoria, 
                       COUNT(f.id_favorito) AS total_favoritos,
                       GROUP_CONCAT(u.usuario SEPARATOR ', ') as nombres_usuarios
                FROM Favoritos f
                JOIN Pictogramas p ON f.id_pictograma = p.id_pictograma
                JOIN Usuarios u ON f.id_usuario = u.id_usuario
                LEFT JOIN Categorias c ON p.id_categoria = c.id_categoria
                GROUP BY p.id_pictograma
                ORDER BY total_favoritos DESC";
        
        return $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        error_log("Error en Ranking: " . $e->getMessage());
        return [];
    }
}
}