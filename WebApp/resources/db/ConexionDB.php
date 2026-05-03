<?php

require __DIR__ . '/../../vendor/autoload.php';

// Cargar .env solo si existe localmente
if (file_exists(__DIR__ . '/../../.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../');
    $dotenv->load();
}

class Conexion {
    private $dbh;
    private static $instancia;
    
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
        // getenv() es más seguro para leer las variables de Render
        $this->host = getenv('DB_HOST') ?: 'localhost';
        $this->usuario = getenv('DB_USER') ?: 'root';
        $this->password = getenv('DB_PASSWORD') ?: '';
        $this->nombreBaseDatos = getenv('DB_NAME') ?: 'aumentativePage';
        $this->port = getenv('DB_PORT') ?: '3306';

        try {
            // El DSN correcto para forzar conexión por red (TCP)
            $dsn = "mysql:host={$this->host};port={$this->port};dbname={$this->nombreBaseDatos};charset=utf8mb4";
            
            $options = [
                PDO::MYSQL_ATTR_SSL_CA => NULL, // Necesario para Aiven
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ];

            $this->dbh = new PDO($dsn, $this->usuario, $this->password, $options);
            
        } catch (PDOException $e) {
            // Esto nos dirá si el error ahora es de "Access Denied" o de Red
            die("Error de conexión: " . $e->getMessage());
        }
    }

    public function getDbh() {
        return $this->dbh;
    }
}