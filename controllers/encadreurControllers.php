<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'config/database.php';
require_once 'models/Encadreur.php';
require_once 'models/Etudiants.php';

class EncadreurController {
    /**
     * Affiche le tableau de bord de l'encadreur
     */
    public function dashboard() {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'encadreur') {
            header('Location: index.php?controller=auth&action=login');
            exit();
        }

        $encadreurId = $_SESSION['user']['id'];
        
        // Récupérer les informations de l'encadreur
        $encadreur = Encadreur::getById($encadreurId);
        
        // Récupérer la liste des étudiants assignés à cet encadreur
        $db = Database::getConnection();
        $stmt = $db->prepare("
            SELECT e.*, a.date_affectation
            FROM etudiants e
            JOIN affectations a ON e.id = a.etudiant_id
            WHERE a.encadreur_id = ?
            ORDER BY a.date_affectation DESC
        ");
        $stmt->execute([$encadreurId]);
        $etudiants = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        include 'views/encadreur/dashboard.php';
    }
    
    /**
     * Affiche le profil de l'encadreur
     */
    public function profile() {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'encadreur') {
            header('Location: index.php?controller=auth&action=login');
            exit();
        }
        
        $encadreurId = $_SESSION['user']['id'];
        $encadreur = Encadreur::getById($encadreurId);
        
        include 'views/encadreur/profile.php';
    }
    
    /**
     * Met à jour le profil de l'encadreur
     */
    public function updateProfile() {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'encadreur') {
            header('Location: index.php?controller=auth&action=login');
            exit();
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?controller=encadreur&action=profile');
            exit();
        }
        
        $encadreurId = $_SESSION['user']['id'];
        $nom = filter_input(INPUT_POST, 'nom', FILTER_SANITIZE_SPECIAL_CHARS);
        $prenom = filter_input(INPUT_POST, 'prenom', FILTER_SANITIZE_SPECIAL_CHARS);
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $competences = isset($_POST['competences']) ? implode(',', $_POST['competences']) : '';
        
        if (!$nom || !$prenom || !$email) {
            $_SESSION['flash_message'] = "Tous les champs sont obligatoires.";
            header('Location: index.php?controller=encadreur&action=profile');
            exit();
        }
        
        try {
            Encadreur::update($encadreurId, $nom, $prenom, $competences, $email);
            
            // Mettre à jour les informations de session
            $_SESSION['user']['username'] = $nom . ' ' . $prenom;
            $_SESSION['user']['email'] = $email;
            
            $_SESSION['flash_message'] = "Votre profil a été mis à jour avec succès.";
        } catch (Exception $e) {
            $_SESSION['flash_message'] = "Erreur lors de la mise à jour : " . $e->getMessage();
        }
        
        header('Location: index.php?controller=encadreur&action=profile');
        exit();
    }
    
    /**
     * Affiche les détails d'un étudiant assigné
     */
    public function viewEtudiant() {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'encadreur') {
            header('Location: index.php?controller=auth&action=login');
            exit();
        }
        
        $etudiantId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        $encadreurId = $_SESSION['user']['id'];
        
        if (!$etudiantId) {
            $_SESSION['flash_message'] = "ID d'étudiant invalide.";
            header('Location: index.php?controller=encadreur&action=dashboard');
            exit();
        }
        
        // Vérifier que l'étudiant est bien assigné à cet encadreur
        $db = Database::getConnection();
        $stmt = $db->prepare("
            SELECT e.*, a.date_affectation, u.username
            FROM etudiants e
            JOIN affectations a ON e.id = a.etudiant_id
            JOIN users u ON e.id = u.id
            WHERE e.id = ? AND a.encadreur_id = ?
        ");
        $stmt->execute([$etudiantId, $encadreurId]);
        $etudiant = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$etudiant) {
            $_SESSION['flash_message'] = "Cet étudiant n'est pas assigné à votre compte.";
            header('Location: index.php?controller=encadreur&action=dashboard');
            exit();
        }
        
        include 'views/encadreur/view_etudiant.php';
    }
}
?>