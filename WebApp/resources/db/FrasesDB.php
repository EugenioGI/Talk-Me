<?php
require_once __DIR__ . "/ConexionDB.php";

class FrasesDB {
    
    /**
     * READ: Obtiene todas las frases de un usuario
     */
    public static function obtenerPorUsuario($id_usuario) {
        $db = Conexion::getInstancia()->getDbh();
        $stmt = $db->prepare("SELECT * FROM Frases WHERE id_usuario = ? ORDER BY fecha DESC");
        $stmt->execute([$id_usuario]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * CREATE: Crea una frase y le asigna el primer pictograma
     */
    public static function crearFrase($id_usuario, $nombre, $id_pictograma) {
        $db = Conexion::getInstancia()->getDbh();
        try {
            $db->beginTransaction();

            // 1. Insertar en tabla Frases
            $stmt = $db->prepare("INSERT INTO Frases (id_usuario, nombre_frase, fecha) VALUES (?, ?, NOW())");
            $stmt->execute([$id_usuario, $nombre]);
            $id_nueva_frase = $db->lastInsertId();

            // 2. Insertar el primer pictograma en FraseDetalle
            $stmtDetalle = $db->prepare("INSERT INTO FraseDetalle (id_frase, id_pictograma, orden) VALUES (?, ?, 1)");
            $stmtDetalle->execute([$id_nueva_frase, $id_pictograma]);

            $db->commit();
            return true;
        } catch (Exception $e) {
            if ($db->inTransaction()) $db->rollBack();
            error_log("Error en crearFrase: " . $e->getMessage());
            return false;
        }
    }

    /**
     * READ DETALLE: Obtiene los pictogramas de una frase específica (JOIN con Pictogramas)
     */
    public static function obtenerDetallesDeFrase($id_frase) {
        $db = Conexion::getInstancia()->getDbh();
        $sql = "SELECT p.nombre, p.ruta_imagen, p.ruta_audio, fd.orden 
                FROM FraseDetalle fd
                JOIN Pictogramas p ON fd.id_pictograma = p.id_pictograma
                WHERE fd.id_frase = ?
                ORDER BY fd.orden ASC";
        $stmt = $db->prepare($sql);
        $stmt->execute([$id_frase]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * UPDATE: Cambia el nombre de una frase existente
     */
    public static function actualizarNombre($id_frase, $id_usuario, $nuevo_nombre) {
        $db = Conexion::getInstancia()->getDbh();
        try {
            $stmt = $db->prepare("UPDATE Frases SET nombre_frase = ? WHERE id_frase = ? AND id_usuario = ?");
            return $stmt->execute([$nuevo_nombre, $id_frase, $id_usuario]);
        } catch (Exception $e) {
            error_log("Error en actualizarNombre: " . $e->getMessage());
            return false;
        }
    }

    /**
     * DELETE: Borra la frase y sus detalles (por integridad)
     */
    public static function eliminarFrase($id_frase, $id_usuario) {
        $db = Conexion::getInstancia()->getDbh();
        try {
            $db->beginTransaction();

            // 1. Borrar detalles primero para evitar errores de FK
            $stmt1 = $db->prepare("DELETE FROM FraseDetalle WHERE id_frase = ?");
            $stmt1->execute([$id_frase]);

            // 2. Borrar la frase (validando que sea del usuario)
            $stmt2 = $db->prepare("DELETE FROM Frases WHERE id_frase = ? AND id_usuario = ?");
            $stmt2->execute([$id_frase, $id_usuario]);

            $db->commit();
            return true;
        } catch (Exception $e) {
            if ($db->inTransaction()) $db->rollBack();
            error_log("Error en eliminarFrase: " . $e->getMessage());
            return false;
        }
    }
}