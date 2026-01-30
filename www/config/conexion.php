<?php
@include_once "config.php";

class Database {
    private static $instance = null;
    private $pdo;

    private function __construct()
    {
        $host = trim(getenv('DB_HOST'));
        $db   = trim(getenv('DB_NAME'));
        $user = trim(getenv('DB_USER'));
        $pass = trim(getenv('DB_PASSWORD'));

        $dsn = "mysql:host={$host};dbname={$db};charset=utf8mb4";

        try {
            $this->pdo = new PDO($dsn, $user, $pass, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_EMULATE_PREPARES   => false,
                PDO::ATTR_PERSISTENT         => true
            ]);
        } catch (PDOException $e) {
            error_log("❌ Error DB: " . $e->getMessage());
            throw new Exception("Error de conexión a la base de datos");
        }
    }

    public static function getConnection()
    {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance->pdo;
    }
}
