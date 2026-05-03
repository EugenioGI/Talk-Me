<?php
include_once("/var/www/html/resources/db/ConexionDB.php");

class UsuarioDB {

    public static function insertaUsuario($idPersona, $usuario) {
        $dbh = Conexion::getInstancia()->getDbh();

        $stmt = $dbh->prepare(
            'INSERT INTO Usuarios (usuario, password, rol, fecha_registro, fk_persona, activo)
             VALUES (?, ?, ?, NOW(), ?, 0)'
        );

        return $stmt->execute([
            $usuario['usuario'],
            $usuario['contrasenia'],
            'usuario',
            $idPersona
        ]);
    }

    public static function existeUsuario($usuario) {
        $dbh = Conexion::getInstancia()->getDbh();

        $stmt = $dbh->prepare('SELECT id_usuario FROM Usuarios WHERE usuario = ?');
        $stmt->execute([$usuario]);

        return ($stmt->fetch() !== false);
    }

    public static function getIdUltimoInsertado(): int {
        return (int) Conexion::getInstancia()->getDbh()->lastInsertId();
    }


    public static function getPasswordHashByUser($usuario) {
        $dbh = Conexion::getInstancia()->getDbh();

        $stmt = $dbh->prepare('SELECT password FROM Usuarios WHERE usuario = ?');
        $stmt->execute([$usuario]);

        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

        return $resultado ? $resultado['password'] : null;
    }

    public static function esActivo($usuario) {
        $dbh = Conexion::getInstancia()->getDbh();

        $stmt = $dbh->prepare('SELECT activo FROM Usuarios WHERE usuario = ?');
        $stmt->execute([$usuario]);

        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

        return ($resultado && $resultado['activo'] == 1);
    }


    public static function getUserByUsername($usuario) {
    $db = Conexion::getInstancia()->getDbh();

    $stmt = $db->prepare("SELECT id_usuario, usuario, password, activo, rol FROM Usuarios WHERE usuario = ?");
    $stmt->execute([$usuario]);

    return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function obtenerTodosLosUsuarios() {
        $db = Conexion::getInstancia()->getDbh();
        $stmt = $db->prepare("SELECT id_usuario, usuario, rol, activo, fecha_registro FROM Usuarios ORDER BY fecha_registro DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function contarTotalUsuarios() {
        $db = Conexion::getInstancia()->getDbh();
        $stmt = $db->query("SELECT COUNT(*) as total FROM Usuarios");
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        return $resultado['total'];
    }

 

    public static function actualizarPasswordPorEmail($email, $passwordHash) {
        try {
            $db = Conexion::getInstancia()->getDbh();
            
            $query = "UPDATE Usuarios u 
                    JOIN Persona p ON u.fk_persona = p.id_persona 
                    SET u.password = ? 
                    WHERE p.correo = ?";
                    
            $stmt = $db->prepare($query);
            return $stmt->execute([$passwordHash, $email]);
            
        } catch (PDOException $e) {
            return false;
        }
    }


public static function activarUsuario($id_usuario) {
    try {
        $db = Conexion::getInstancia()->getDbh();
        
        $stmt = $db->prepare("UPDATE Usuarios SET activo = 1 WHERE id_usuario = ?");
        $stmt->execute([$id_usuario]);
        
        return $stmt->rowCount() > 0;
        
    } catch (PDOException $e) {
        return false;
    }
}





} 