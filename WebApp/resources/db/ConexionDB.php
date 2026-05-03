<?php

require __DIR__ . '/../../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->load();

class Conexion {
    private $dbh;
    private static $instancia; //The single instance
    private $host = 'mysql'; //nombre del servicio en compose.yml
    private $usuario = 'root';
    //~ private $password = $_ENV['DB_PASSWORD'];; // initialization must be a constant value.
    private $password;
    private $nombreBaseDatos = 'aumentativePage';

    /*
    Get an instance of the Database
    @return Instance
     */
    public static function getInstancia() {
        if (!self::$instancia) { // singleton
            self::$instancia = new self();
        }
        return self::$instancia;
    }

    // Constructor
    private function __construct() {
        $this->password = $_ENV['DB_PASSWORD'];
        try {
            $this->dbh = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->nombreBaseDatos, $this->usuario, $this->password);
            $this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    public function getDbh() {
        return $this->dbh;
    }
}
