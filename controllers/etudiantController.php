<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'config/database.php';
require_once 'models/etudiants.php';

class EtudiantController {
    /**
     * Affiche le tableau de bord de l'étudiant
     */
    public function dashboard() {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'etudiant') {
            header('Location: index.php?controller=auth&action=login');
            exit();
        }

        $userId = $_SESSION['user']['id'];
        $db = Database::getConnection();

        // Requête SQL pour récupérer les informations de l'étudiant et de son encadreur
        $stmt = $db->prepare("
            SELECT e.*, u.username, 
                   enc.nom AS encadreur_nom, enc.prenom AS encadreur_prenom 
            FROM etudiants e
            JOIN users u ON e.id = u.id
            LEFT JOIN affectations a ON e.id = a.etudiant_id
            LEFT JOIN encadreurs enc ON a.encadreur_id = enc.id
            WHERE e.id = :etudiant_id
        ");
        $stmt->execute(['etudiant_id' => $userId]);
        $etudiant = $stmt->fetch(PDO::FETCH_ASSOC);

        // Si le profil étudiant n'existe pas encore, le créer
        if (!$etudiant) {
            $stmt = $db->prepare("
                INSERT INTO etudiants (id, nom, prenom, email, role, created_at) 
                VALUES (:id, :nom, :prenom, :email, 'etudiant', NOW())
            ");
            $stmt->execute([
                'id' => $userId,
                'nom' => 'À compléter',
                'prenom' => 'À compléter',
                'email' => $_SESSION['user']['email'] ?? ''
            ]);

            // Récupérer à nouveau les informations de l'étudiant
            $stmt = $db->prepare("
                SELECT e.*, u.username 
                FROM etudiants e
                JOIN users u ON e.id = u.id
                WHERE e.id = :etudiant_id
            ");
            $stmt->execute(['etudiant_id' => $userId]);
            $etudiant = $stmt->fetch(PDO::FETCH_ASSOC);
        }

        include 'views/etudiant/dashboard.php';
    }

    /**
     * Traite la soumission du formulaire (thème, binôme, fichier PDF)
     */
    public function submitForm() {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'etudiant') {
            header('Location: index.php?controller=auth&action=login');
            exit();
        }
        
        $etudiantId = $_SESSION['user']['id'];
        $theme = $_POST['theme'];
        $binome = $_POST['binome'];
    
        if (!isset($_FILES['fichier']) || $_FILES['fichier']['error'] !== 0) {
            $_SESSION['flash_message'] = "Erreur : aucun fichier ou problème lors de l'envoi.";
            header('Location: index.php?controller=etudiant&action=dashboard');
            exit();
        }
    
        $fichier_pdf = $_FILES['fichier'];
    
        // Vérifier le type MIME
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($fichier_pdf['tmp_name']);
        
        if ($mime !== 'application/pdf') {
            $_SESSION['flash_message'] = "Seuls les fichiers PDF sont autorisés.";
            header('Location: index.php?controller=etudiant&action=dashboard');
            exit();
        }
    
        // Nom de fichier unique
        $nomFichier = uniqid() . '_' . basename($fichier_pdf['name']);
        $destination = 'public/uploads/' . $nomFichier;
    
        // Créer le dossier s'il n'existe pas
        if (!is_dir('public/uploads')) {
            mkdir('public/uploads', 0777, true);
        }
    
        // Enregistrement du fichier
        if (move_uploaded_file($fichier_pdf['tmp_name'], $destination)) {
            // Sauvegarde en base de données
            $db = Database::getConnection();
            $stmt = $db->prepare("
                UPDATE etudiants 
                SET theme = ?, binome = ?, fichier_pdf = ?, date_soumission = NOW(), form_submitted = 1 
                WHERE id = ?
            ");
            $stmt->execute([$theme, $binome, $nomFichier, $etudiantId]);
            
            $_SESSION['flash_message'] = "Votre cahier des charges a été soumis avec succès.";
            header('Location: index.php?controller=etudiant&action=dashboard');
            exit();
        } else {
            $_SESSION['flash_message'] = "Erreur lors de l'envoi du fichier.";
            header('Location: index.php?controller=etudiant&action=dashboard');
            exit();
        }
    }
    
    /**
     * Envoie une relance à l'administrateur pour l'attribution d'un encadreur
     */
    public function relancer() {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'etudiant') {
            header('Location: index.php?controller=auth&action=login');
            exit();
        }
        
        // Pour l'exercice, on simule simplement l'envoi d'une relance
        $_SESSION['flash_message'] = "Votre demande de relance a été envoyée à l'administration.";
        header('Location: index.php?controller=etudiant&action=dashboard');
        exit();
    }

    /**
     * Met à jour le profil de l'étudiant
     */
    public function updateProfile() {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'etudiant') {
            header('Location: index.php?controller=auth&action=login');
            exit();
        }
        
        $nom = $_POST['nom'];
        $prenom = $_POST['prenom'];
        $email = $_POST['email'];
        $etudiantId = $_SESSION['user']['id'];
        
        $db = Database::getConnection();
        
        // Mise à jour des informations dans la table etudiants
        $stmt = $db->prepare("UPDATE etudiants SET nom = ?, prenom = ?, email = ? WHERE id = ?");
        $stmt->execute([$nom, $prenom, $email, $etudiantId]);
        
        // Mise à jour de l'email dans la table users
        $stmt = $db->prepare("UPDATE users SET email = ? WHERE id = ?");
        $stmt->execute([$email, $etudiantId]);
        
        $_SESSION['flash_message'] = "Votre profil a été mis à jour avec succès.";
        header('Location: index.php?controller=etudiant&action=profile');
        exit();
    }

    /**
     * Affiche le profil de l'étudiant
     */
    public function profile() {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'etudiant') {
            header('Location: index.php?controller=auth&action=login');
            exit();
        }
        
        $etudiantId = $_SESSION['user']['id'];
        
        $db = Database::getConnection();
        $stmt = $db->prepare("
            SELECT e.*, u.username, 
                   enc.nom AS encadreur_nom, enc.prenom AS encadreur_prenom
            FROM etudiants e
            JOIN users u ON e.id = u.id
            LEFT JOIN affectations a ON e.id = a.etudiant_id
            LEFT JOIN encadreurs enc ON a.encadreur_id = enc.id
            WHERE e.id = ?
        ");
        $stmt->execute([$etudiantId]);
        $etudiant = $stmt->fetch(PDO::FETCH_ASSOC);
        
        include 'views/etudiant/profile.php';
    }
}
?>