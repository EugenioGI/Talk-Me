<?php
include_once("/var/www/html/resources/db/ConexionDB.php");

class PersonaDB {

    public static function insertaPersona($persona) {
        $dbh = Conexion::getInstancia()->getDbh();

        $stmt = $dbh->prepare(
            'INSERT INTO Persona (nombre, apellidos, correo) VALUES (?, ?, ?)'
        );

        $stmt->execute([
            $persona['nombre'],
            $persona['apellidos'],
            $persona['email']
        ]);
    }

    public static function getUltimoIdInsertado() {
        return Conexion::getInstancia()->getDbh()->lastInsertId();
    }

    public static function existeCorreo($correo): bool {
        $dbh = Conexion::getInstancia()->getDbh();

        $stmt = $dbh->prepare('SELECT correo FROM Persona WHERE correo = ?');
        $stmt->execute([$correo]);

        return ($stmt->fetch() !== false);
    }
}