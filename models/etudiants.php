<?php
require_once 'config/Database.php';

class Etudiants {
    public static function getWithoutEncadreur() {
        $db = (new Database())->getConnection();
        $stmt = $db->query("SELECT * FROM students WHERE encadreur_id IS NULL");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function assignEncadreur($student_id, $encadreur_id) {
        $db = (new Database())->getConnection();
        $stmt = $db->prepare("UPDATE students SET encadreur_id = ? WHERE id = ?");
        $stmt->execute([$encadreur_id, $student_id]);
    }

    public static function getById($id) {
        $db = (new Database())->getConnection();
        $stmt = $db->prepare("SELECT * FROM students WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public static function getEtudiantsSansEncadreur() {
        $db = Database::getConnection();
        $sql = "SELECT * FROM etudiants WHERE encadreur_id IS NULL";
        return $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }
    
}
?>
