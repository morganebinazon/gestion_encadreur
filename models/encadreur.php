<?php
require_once 'config/database.php';

class Encadreur {
    /**
     * Récupère tous les encadreurs
     * 
     * @return array Liste de tous les encadreurs
     */
    public static function getAll() {
        $db = Database::getConnection();
        $stmt = $db->query("SELECT * FROM encadreurs ORDER BY nom, prenom");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Crée un nouvel encadreur
     * 
     * @param string $nom Nom de l'encadreur
     * @param string $prenom Prénom de l'encadreur
     * @param string $competences Compétences de l'encadreur
     * @param string $email Email de l'encadreur (optionnel)
     * @return int ID de l'encadreur créé
     */
    public static function create($nom, $prenom, $competences, $email = null) {
        $db = Database::getConnection();
        
        try {
            $db->beginTransaction();
            
            // Générer un mot de passe aléatoire si l'email est fourni
            $mot_de_passe = null;
            if ($email) {
                $mot_de_passe = password_hash(bin2hex(random_bytes(8)), PASSWORD_BCRYPT);
            }
            
            $stmt = $db->prepare("INSERT INTO encadreurs (nom, prenom, competences, email, mot_de_passe, date_creation) VALUES (?, ?, ?, ?, ?, NOW())");
            $stmt->execute([$nom, $prenom, $competences, $email, $mot_de_passe]);
            
            $encadreur_id = $db->lastInsertId();
            
            // Si un email a été fourni, envoyer les identifiants par email (désactivé pour le moment)
            if ($email && $mot_de_passe) {
                /*
                $to = $email;
                $subject = "Vos identifiants de connexion";
                $message = "Bonjour $prenom $nom,\n\n";
                $message .= "Votre compte encadreur a été créé sur la plateforme de gestion d'encadreurs.\n";
                $message .= "Voici vos identifiants de connexion :\n";
                $message .= "Email : $email\n";
                $message .= "Mot de passe : " . bin2hex(random_bytes(8)) . "\n\n";
                $message .= "Nous vous invitons à vous connecter et à changer votre mot de passe dès que possible.\n\n";
                $message .= "Cordialement,\nL'administration";
                
                $headers = "From: noreply@gestion-encadreurs.com";
                
                mail($to, $subject, $message, $headers);
                */
            }
            
            $db->commit();
            return $encadreur_id;
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    /**
     * Récupère un encadreur par son ID
     * 
     * @param int $id ID de l'encadreur
     * @return array Données de l'encadreur
     */
    public static function getById($id) {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM encadreurs WHERE id = ?");
        $stmt->execute([$id]);
        $encadreur = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$encadreur) {
            throw new Exception("Encadreur ID $id introuvable.");
        }
        
        return $encadreur;
    }

    /**
     * Met à jour un encadreur
     * 
     * @param int $id ID de l'encadreur
     * @param string $nom Nouveau nom
     * @param string $prenom Nouveau prénom
     * @param string $competences Nouvelles compétences
     * @param string $email Nouvel email (optionnel)
     * @return bool Succès de l'opération
     */
    public static function update($id, $nom, $prenom, $competences, $email = null) {
        $db = Database::getConnection();
        
        // Si l'email est fourni, inclure dans la mise à jour
        if ($email) {
            $stmt = $db->prepare("UPDATE encadreurs SET nom = ?, prenom = ?, competences = ?, email = ? WHERE id = ?");
            return $stmt->execute([$nom, $prenom, $competences, $email, $id]);
        } else {
            $stmt = $db->prepare("UPDATE encadreurs SET nom = ?, prenom = ?, competences = ? WHERE id = ?");
            return $stmt->execute([$nom, $prenom, $competences, $id]);
        }
    }

    /**
     * Supprime un encadreur
     * 
     * @param int $id ID de l'encadreur
     * @return bool Succès de l'opération
     */
    public static function delete($id) {
        $db = Database::getConnection();
        
        // Vérifier si l'encadreur a des étudiants assignés
        $stmt = $db->prepare("SELECT COUNT(*) FROM affectations WHERE encadreur_id = ?");
        $stmt->execute([$id]);
        $count = $stmt->fetchColumn();
        
        if ($count > 0) {
            throw new Exception("Impossible de supprimer cet encadreur car il a des étudiants assignés.");
        }
        
        $stmt = $db->prepare("DELETE FROM encadreurs WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    /**
     * Récupère la liste des étudiants assignés à un encadreur
     * 
     * @param int $encadreur_id ID de l'encadreur
     * @return array Liste des étudiants assignés
     */
    public static function getAssignedStudents($encadreur_id) {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            SELECT e.*, a.date_affectation, u.username
            FROM etudiants e
            JOIN affectations a ON e.id = a.etudiant_id
            JOIN users u ON e.id = u.id
            WHERE a.encadreur_id = ?
            ORDER BY a.date_affectation DESC
        ");
        $stmt->execute([$encadreur_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Modifie le mot de passe d'un encadreur
     * 
     * @param int $id ID de l'encadreur
     * @param string $password Nouveau mot de passe (non hashé)
     * @return bool Succès de l'opération
     */
    public static function updatePassword($id, $password) {
        $db = Database::getConnection();
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);
        
        $stmt = $db->prepare("UPDATE encadreurs SET mot_de_passe = ? WHERE id = ?");
        return $stmt->execute([$hashed_password, $id]);
    }
}