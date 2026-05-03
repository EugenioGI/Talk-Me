<?php
include_once("/var/www/html/resources/db/ConexionDB.php");

class CategoriasDB {

    // --- CREATE ---
    public static function insertar($nombre, $id_usuario) {
        try {
            $db = Conexion::getInstancia()->getDbh();
            $sql = "INSERT INTO Categorias (nombre, id_usuario) VALUES (?, ?)";
            $stmt = $db->prepare($sql);
            return $stmt->execute([$nombre, $id_usuario]);
        } catch (PDOException $e) {
            error_log("Error en insertar categoría: " . $e->getMessage());
            return false;
        }
    }

    // --- READ ---
    public static function obtenerTodas() {
        $db = Conexion::getInstancia()->getDbh();
        $sql = "SELECT * FROM Categorias ORDER BY nombre ASC";
        $stmt = $db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function obtenerPorUsuario($id_usuario) {
        $db = Conexion::getInstancia()->getDbh();
        $sql = "SELECT * FROM Categorias WHERE id_usuario = ? ORDER BY nombre ASC";
        $stmt = $db->prepare($sql);
        $stmt->execute([$id_usuario]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // --- UPDATE ---
    public static function actualizar($id_categoria, $nombre) {
        try {
            $db = Conexion::getInstancia()->getDbh();
            $sql = "UPDATE Categorias SET nombre = ? WHERE id_categoria = ?";
            $stmt = $db->prepare($sql);
            return $stmt->execute([$nombre, $id_categoria]);
        } catch (PDOException $e) {
            return false;
        }
    }

    // --- DELETE ---
    public static function eliminar($id_categoria) {
        try {
            $db = Conexion::getInstancia()->getDbh();
            // Nota: Si hay pictogramas asociados, esto fallará por integridad referencial
            // a menos que uses ON DELETE CASCADE en tu BD.
            $sql = "DELETE FROM Categorias WHERE id_categoria = ?";
            $stmt = $db->prepare($sql);
            return $stmt->execute([$id_categoria]);
        } catch (PDOException $e) {
            return false;
        }
    }

    public static function obtenerCategoriasParaAdmin() {
    try {
        $db = Conexion::getInstancia()->getDbh();
        
        // Esta consulta cuenta cuántos USUARIOS únicos usan cada categoría
        $sql = "SELECT 
                    c.id_categoria, 
                    c.nombre, 
                    COUNT(DISTINCT p.id_usuario) as total_usuarios,
                    COUNT(p.id_pictograma) as total_pictogramas
                FROM Categorias c
                LEFT JOIN Pictogramas p ON c.id_categoria = p.id_categoria
                GROUP BY c.id_categoria, c.nombre
                ORDER BY c.nombre ASC";

        $stmt = $db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error en obtenerCategoriasParaAdmin: " . $e->getMessage());
        return [];
    }
}

}