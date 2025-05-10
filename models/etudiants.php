<?php
require_once 'config/database.php';

class Etudiants {
    // Récupère les étudiants sans encadreur
    public static function getWithoutEncadreur() {
        $db = (new Database())->getConnection();
        $stmt = $db->query("SELECT * FROM etudiants WHERE encadreur_id IS NULL");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Assigner un encadreur à un étudiant
    public static function assignEncadreur($etudiant_id, $encadreur_id) {
        $db = (new Database())->getConnection();
        $stmt = $db->prepare("UPDATE etudiants SET encadreur_id = ? WHERE id = ?");
        return $stmt->execute([$encadreur_id, $etudiant_id]);
    }

    // Récupérer un étudiant par son ID
    public static function getById($id) {
        $db = (new Database())->getConnection();
        $stmt = $db->prepare("SELECT * FROM users WHERE id = ? AND role = 'etudiant'");
        $stmt->execute([$id]);
        $etudiant = $stmt->fetch(PDO::FETCH_ASSOC);
      if (!$etudiant) {
       throw new Exception("Étudiant ID $id introuvable dans la table users.");
        }
       return $etudiant;
    }

    // Récupérer tous les étudiants
    public static function getAll() {
        $db = (new Database())->getConnection();
        $stmt = $db->query("SELECT * FROM etudiants");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Récupérer les étudiants sans encadreur (fonction similaire à `getWithoutEncadreur`)
    public static function getEtudiantsSansEncadreur() {
        return self::getWithoutEncadreur();  // Cette fonction peut être redondante, on peut réutiliser la première
    }

    // Soumettre un thème et un fichier pour l'étudiant
    public static function submitTheme($id, $theme, $binome, $fichier_pdf) {
        $db = (new Database())->getConnection();
        $stmt = $db->prepare("UPDATE etudiants SET theme = ?, binome = ?, fichier_pdf = ? WHERE id = ?");
        $stmt->execute([$theme, $binome, $fichier_pdf, $id]);
    }

    // Mise à jour de la soumission du thème
    public static function updateSubmission($id, $theme, $binome, $fichier_pdf) {
        $db = (new Database())->getConnection();
    
        $sql = "UPDATE etudiants SET theme = ?, binome = ?, fichier_pdf = ? WHERE id = ?";
        $stmt = $db->prepare($sql);
    
        return $stmt->execute([$theme, $binome, $fichier_pdf, $id]);
    }
    

    // Mise à jour du profil de l'étudiant
    public static function updateProfichier_pdf($id, $nom, $prenom, $email) {
        $db = (new Database())->getConnection();
        $sql = "UPDATE etudiants SET nom = ?, prenom = ?, email = ? WHERE id = ?";
        $stmt = $db->prepare($sql);
        return $stmt->execute([$nom, $prenom, $email, $id]);
    }
}
?>
