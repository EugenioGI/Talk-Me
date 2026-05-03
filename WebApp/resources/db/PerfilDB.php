<?php
include_once("ConexionDB.php"); 

class PerfilDB {

    // Obtener la ficha de un usuario específico
    public static function obtenerPorUsuario($id_usuario) {
        try {
            $db = Conexion::getInstancia()->getDbh();
            $sql = "SELECT * FROM DatosPersonales WHERE id_usuario = ?";
            $stmt = $db->prepare($sql);
            $stmt->execute([$id_usuario]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return false;
        }
    }

    // Guardar o Actualizar los datos del perfil
    public static function guardar($id_usuario, $data) {
        try {
            $db = Conexion::getInstancia()->getDbh();
            $existente = self::obtenerPorUsuario($id_usuario);

            if ($existente) {
                $sql = "UPDATE DatosPersonales SET 
                        nombre_completo = ?, edad = ?, genero = ?, 
                        condicion_enfermedad = ?, nivel_apoyo = ?, dificultad_mayor = ? 
                        WHERE id_usuario = ?";
                $params = [$data['nombre'], $data['edad'], $data['genero'], 
                           $data['condicion'], $data['apoyo'], $data['dificultad'], $id_usuario];
            } else {
                $sql = "INSERT INTO DatosPersonales (id_usuario, nombre_completo, edad, genero, 
                        condicion_enfermedad, nivel_apoyo, dificultad_mayor) 
                        VALUES (?, ?, ?, ?, ?, ?, ?)";
                $params = [$id_usuario, $data['nombre'], $data['edad'], $data['genero'], 
                           $data['condicion'], $data['apoyo'], $data['dificultad']];
            }

            $stmt = $db->prepare($sql);
            return $stmt->execute($params);
        } catch (Exception $e) {
            return false;
        }
    }


    public static function listarTodoParaAdmin() {
    try {
        $db = Conexion::getInstancia()->getDbh();
        $sql = "SELECT 
                    u.id_usuario, 
                    u.usuario, 
                    dp.nombre_completo, 
                    dp.edad, 
                    dp.genero, 
                    dp.dificultad_mayor, 
                    dp.condicion_enfermedad, 
                    dp.nivel_apoyo
                FROM Usuarios u
                LEFT JOIN DatosPersonales dp ON u.id_usuario = dp.id_usuario
                WHERE u.rol = 'usuario'";
        
        $stmt = $db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        error_log("Error en listarTodoParaAdmin: " . $e->getMessage());
        return [];
    }
}

    public static function guardarOActualizarPerfil($id_usuario, $data) {
    $db = Conexion::getInstancia()->getDbh();
    $perfilExistente = self::obtenerPerfilPorUsuario($id_usuario);

    if ($perfilExistente) {
        $sql = "UPDATE DatosPersonales SET 
                nombre_completo = ?, edad = ?, genero = ?, 
                condicion_enfermedad = ?, nivel_apoyo = ?, dificultad_mayor = ? 
                WHERE id_usuario = ?";
        $params = [$data['nombre'], $data['edad'], $data['genero'], 
                   $data['condicion'], $data['apoyo'], $data['dificultad'], $id_usuario];
    } else {
        $sql = "INSERT INTO DatosPersonales (id_usuario, nombre_completo, edad, genero, 
                condicion_enfermedad, nivel_apoyo, dificultad_mayor) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $params = [$id_usuario, $data['nombre'], $data['edad'], $data['genero'], 
                   $data['condicion'], $data['apoyo'], $data['dificultad']];
    }

    $stmt = $db->prepare($sql);
    return $stmt->execute($params);
}

public static function obtenerPerfilPorUsuario($id_usuario) {
        try {
            $db = Conexion::getInstancia()->getDbh();
            $sql = "SELECT * FROM DatosPersonales WHERE id_usuario = ?";
            $stmt = $db->prepare($sql);
            $stmt->execute([$id_usuario]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return false;
        }
    }

}