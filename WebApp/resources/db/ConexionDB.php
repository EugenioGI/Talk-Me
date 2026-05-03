<?php

require __DIR__ . '/../../vendor/autoload.php';

// Intentar cargar .env solo si existe (en Render no es necesario porque usamos Environment Variables)
if (file_exists(__DIR__ . '/../../.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../');
    $dotenv->load();
}

class Conexion {
    private $dbh;
    private static $instancia;
    
    // Estos valores ahora se llenan desde Render o el .env
    private $host;
    private $usuario;
    private $password;
    private $nombreBaseDatos;
    private $port;

    public static function getInstancia() {
        if (!self::$instancia) {
            self::$instancia = new self();
        }
        return self::$instancia;
    }

    private function __construct() {
        // getenv busca las variables que pusiste en las "cajitas" de Render
        $this->host = getenv('DB_HOST') ?: 'localhost';
        $this->usuario = getenv('DB_USER') ?: 'root';
        $this->password = getenv('DB_PASSWORD') ?: '';
        $this->nombreBaseDatos = getenv('DB_NAME') ?: 'aumentativePage';
        $this->port = getenv('DB_PORT') ?: '3306';

        try {
            // Aiven requiere SSL. Añadimos las opciones al PDO
            $options = [
                PDO::MYSQL_ATTR_SSL_CA => NULL, // Esto activa el SSL básico
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ];

            $dsn = "mysql:host=" . $this->host . ";port=" . $this->port . ";dbname=" . $this->nombreBaseDatos;
            
            $this->dbh = new PDO($dsn, $this->usuario, $this->password, $options);
            
        } catch (PDOException $e) {
            // En producción es mejor no mostrar el mensaje directo, pero para debug está bien
            echo "Error de conexión: " . $e->getMessage();
        }
    }

    public function getDbh() {
        return $this->dbh;
    }
}