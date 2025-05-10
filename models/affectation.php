<?php
require_once 'config/database.php';

class Affectation {
    /**
     * Récupère toutes les affectations
     * 
     * @return array Liste de toutes les affectations
     */
    public static function getAll() {
        $db = Database::getConnection();
        $stmt = $db->query("
            SELECT a.*, e.nom AS etudiant_nom, e.prenom AS etudiant_prenom,
                   en.nom AS encadreur_nom, en.prenom AS encadreur_prenom
            FROM affectations a
            JOIN etudiants e ON a.etudiant_id = e.id
            JOIN encadreurs en ON a.encadreur_id = en.id
            ORDER BY a.date_affectation DESC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Crée une nouvelle affectation
     * 
     * @param int $etudiant_id ID de l'étudiant
     * @param int $encadreur_id ID de l'encadreur
     * @return bool Succès de l'opération
     */
    public static function create($etudiant_id, $encadreur_id) {
        $db = Database::getConnection();
        
        // Vérifier si l'étudiant a déjà un encadreur
        $stmt = $db->prepare("SELECT * FROM affectations WHERE etudiant_id = ?");
        $stmt->execute([$etudiant_id]);
        
        if ($stmt->fetch()) {
            throw new Exception("Cet étudiant a déjà un encadreur assigné.");
        }
        
        // Créer l'affectation
        $stmt = $db->prepare("INSERT INTO affectations (etudiant_id, encadreur_id, date_affectation) VALUES (?, ?, NOW())");
        return $stmt->execute([$etudiant_id, $encadreur_id]);
    }
    
    /**
     * Supprime une affectation
     * 
     * @param int $etudiant_id ID de l'étudiant
     * @return bool Succès de l'opération
     */
    public static function delete($etudiant_id) {
        $db = Database::getConnection();
        $stmt = $db->prepare("DELETE FROM affectations WHERE etudiant_id = ?");
        return $stmt->execute([$etudiant_id]);
    }
    
    /**
     * Change l'encadreur d'un étudiant
     * 
     * @param int $etudiant_id ID de l'étudiant
     * @param int $nouveau_encadreur_id ID du nouvel encadreur
     * @return bool Succès de l'opération
     */
    public static function changeEncadreur($etudiant_id, $nouveau_encadreur_id) {
        $db = Database::getConnection();
        
        try {
            $db->beginTransaction();
            
            // Supprimer l'ancienne affectation
            $stmt = $db->prepare("DELETE FROM affectations WHERE etudiant_id = ?");
            $stmt->execute([$etudiant_id]);
            
            // Créer la nouvelle affectation
            $stmt = $db->prepare("INSERT INTO affectations (etudiant_id, encadreur_id, date_affectation) VALUES (?, ?, NOW())");
            $stmt->execute([$etudiant_id, $nouveau_encadreur_id]);
            
            $db->commit();
            return true;
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }
    
    /**
     * Vérifie si un étudiant a un encadreur
     * 
     * @param int $etudiant_id ID de l'étudiant
     * @return bool True si l'étudiant a un encadreur, sinon False
     */
    public static function hasEncadreur($etudiant_id) {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT COUNT(*) FROM affectations WHERE etudiant_id = ?");
        $stmt->execute([$etudiant_id]);
        return $stmt->fetchColumn() > 0;
    }
    
    /**
     * Récupère l'encadreur d'un étudiant
     * 
     * @param int $etudiant_id ID de l'étudiant
     * @return array|bool Données de l'encadreur ou False si aucun encadreur n'est assigné
     */
    public static function getEncadreurByEtudiant($etudiant_id) {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            SELECT e.*, a.date_affectation
            FROM encadreurs e
            JOIN affectations a ON e.id = a.encadreur_id
            WHERE a.etudiant_id = ?
        ");
        $stmt->execute([$etudiant_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}