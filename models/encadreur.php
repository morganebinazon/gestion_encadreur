<?php
require_once 'config/Database.php';

class Encadreur {
    
    // Récupère tous les encadreurs
    public static function getAll() {
        $db = Database::getConnection();
        $stmt = $db->query("SELECT * FROM encadreurs");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Crée un nouvel encadreur
    public static function create($nom, $prenom, $competences) {
        $db = Database::getConnection();
        $stmt = $db->prepare("INSERT INTO encadreurs (nom, prenom, competences) VALUES (?, ?, ?)");
        $stmt->execute([$nom, $prenom, $competences]);
    }

    // Récupère un encadreur par ID
    public static function getById($id) {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM encadreurs WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Met à jour un encadreur
    public static function update($id, $nom, $prenom, $competences) {
        $db = Database::getConnection();
        $stmt = $db->prepare("UPDATE encadreurs SET nom = ?, prenom = ?, competences = ? WHERE id = ?");
        $stmt->execute([$nom, $prenom, $competences, $id]);
    }

    // Supprime un encadreur
    public static function delete($id) {
        $db = Database::getConnection();
        $stmt = $db->prepare("DELETE FROM encadreurs WHERE id = ?");
        $stmt->execute([$id]);
    }
}
?>
