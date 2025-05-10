<?php
class Database {
    private static $host = 'localhost';
    private static $db_name = 'gestion_encadreurs';
    private static $username = 'root';
    private static $password = '';
    private static $conn = null;

    /**
     * Établit une connexion à la base de données si elle n'existe pas déjà
     * 
     * @return PDO Instance de connexion à la base de données
     */
    public static function getConnection() {
        if (self::$conn === null) {
            try {
                self::$conn = new PDO(
                    "mysql:host=" . self::$host . ";dbname=" . self::$db_name, 
                    self::$username, 
                    self::$password,
                    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
                );
                self::$conn->exec("SET NAMES utf8");
            } catch (PDOException $e) {
                die("Erreur de connexion à la base de données : " . $e->getMessage());
            }
        }
        return self::$conn;
    }
}
?>