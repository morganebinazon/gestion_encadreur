<?php
require_once 'config/database.php';

class Etudiants {
    /**
     * Récupère les étudiants sans encadreur
     * 
     * @return array Liste des étudiants sans encadreur
     */
    public static function getWithoutEncadreur() {
        $db = Database::getConnection();
        $stmt = $db->query("
            SELECT e.*, u.username 
            FROM etudiants e
            JOIN users u ON e.id = u.id
            WHERE e.id NOT IN (SELECT etudiant_id FROM affectations)
            ORDER BY e.created_at DESC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Assigne un encadreur à un étudiant
     * 
     * @param int $etudiant_id ID de l'étudiant
     * @param int $encadreur_id ID de l'encadreur
     * @return bool Succès de l'opération
     */
    public static function assignEncadreur($etudiant_id, $encadreur_id) {
        $db = Database::getConnection();
        
        // Vérifier si l'étudiant existe
        $stmt = $db->prepare("SELECT * FROM etudiants WHERE id = ?");
        $stmt->execute([$etudiant_id]);
        if (!$stmt->fetch()) {
            throw new Exception("Étudiant non trouvé.");
        }
        
        // Vérifier si l'encadreur existe
        $stmt = $db->prepare("SELECT * FROM encadreurs WHERE id = ?");
        $stmt->execute([$encadreur_id]);
        if (!$stmt->fetch()) {
            throw new Exception("Encadreur non trouvé.");
        }
        
        // Vérifier si l'étudiant est déjà assigné
        $stmt = $db->prepare("SELECT * FROM affectations WHERE etudiant_id = ?");
        $stmt->execute([$etudiant_id]);
        if ($stmt->fetch()) {
            throw new Exception("Cet étudiant est déjà assigné à un encadreur.");
        }
        
        // Créer l'affectation
        $stmt = $db->prepare("INSERT INTO affectations (etudiant_id, encadreur_id, date_affectation) VALUES (?, ?, NOW())");
        return $stmt->execute([$etudiant_id, $encadreur_id]);
    }

    /**
     * Récupère un étudiant par son ID
     * 
     * @param int $id ID de l'étudiant
     * @return array Données de l'étudiant
     */
    public static function getById($id) {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            SELECT e.*, u.username, u.email AS user_email
            FROM etudiants e
            JOIN users u ON e.id = u.id
            WHERE e.id = ?
        ");
        $stmt->execute([$id]);
        $etudiant = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$etudiant) {
            throw new Exception("Étudiant ID $id introuvable.");
        }
        
        return $etudiant;
    }

    /**
     * Récupère tous les étudiants
     * 
     * @return array Liste de tous les étudiants
     */
    public static function getAll() {
        $db = Database::getConnection();
        $stmt = $db->query("
            SELECT e.*, u.username 
            FROM etudiants e
            JOIN users u ON e.id = u.id
            ORDER BY e.nom, e.prenom
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère les étudiants sans encadreur (alias de getWithoutEncadreur)
     * 
     * @return array Liste des étudiants sans encadreur
     */
    public static function getEtudiantsSansEncadreur() {
        return self::getWithoutEncadreur();
    }

    /**
     * Soumet un thème et un fichier pour l'étudiant
     * 
     * @param int $id ID de l'étudiant
     * @param string $theme Thème du projet
     * @param int $binome 1 si l'étudiant a un binôme, 0 sinon
     * @param string $fichier_pdf Nom du fichier PDF
     * @return bool Succès de l'opération
     */
    public static function submitTheme($id, $theme, $binome, $fichier_pdf) {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            UPDATE etudiants 
            SET theme = ?, binome = ?, fichier_pdf = ?, date_soumission = NOW(), form_submitted = 1 
            WHERE id = ?
        ");
        return $stmt->execute([$theme, $binome, $fichier_pdf, $id]);
    }

    /**
     * Mise à jour de la soumission du thème
     * 
     * @param int $id ID de l'étudiant
     * @param string $theme Nouveau thème
     * @param int $binome Nouveau statut de binôme
     * @param string $fichier_pdf Nouveau fichier PDF
     * @return bool Succès de l'opération
     */
    public static function updateSubmission($id, $theme, $binome, $fichier_pdf) {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            UPDATE etudiants 
            SET theme = ?, binome = ?, fichier_pdf = ?, date_modification = NOW() 
            WHERE id = ?
        ");
        return $stmt->execute([$theme, $binome, $fichier_pdf, $id]);
    }

    /**
     * Mise à jour du profil de l'étudiant
     * 
     * @param int $id ID de l'étudiant
     * @param string $nom Nouveau nom
     * @param string $prenom Nouveau prénom
     * @param string $email Nouvel email
     * @return bool Succès de l'opération
     */
    public static function updateProfile($id, $nom, $prenom, $email) {
        $db = Database::getConnection();
        
        try {
            $db->beginTransaction();
            
            // Mettre à jour la table etudiants
            $stmt = $db->prepare("UPDATE etudiants SET nom = ?, prenom = ?, email = ? WHERE id = ?");
            $stmt->execute([$nom, $prenom, $email, $id]);
            
            // Mettre à jour la table users
            $stmt = $db->prepare("UPDATE users SET email = ? WHERE id = ?");
            $stmt->execute([$email, $id]);
            
            $db->commit();
            return true;
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }
}
?>