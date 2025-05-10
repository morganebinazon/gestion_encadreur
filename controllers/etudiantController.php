<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require 'config/database.php';
require 'models/etudiants.php';

class etudiantController {
    private $etudiants;

    public function __construct() {
        $this->etudiants = new Etudiants();
    }

    // Soumission du formulaire (thème, binôme, fichier PDF)
    public function submitForm() {
        $etudiantId = $_SESSION['etudiant_id'];
        $theme = $_POST['theme'];
        $binome = $_POST['binome'];
    
        if (!isset($_FILES['fichier']) || $_FILES['fichier']['error'] !== 0) {
            echo "Erreur : aucun fichier ou problème lors de l'envoi.";
            return;
        }
    
        $fichier_pdf = $_FILES['fichier'];
    
        // Vérifier le type MIME (plus sûr que juste l'extension)
        if (mime_content_type($fichier_pdf['tmp_name']) !== 'application/pdf') {
            die("Seuls les fichiers PDF sont autorisés.");
        }
    
        // Nom de fichier unique
        $nomFichier = uniqid() . '_' . basename($fichier_pdf['name']);
        $destination = 'public/uploads/' . $nomFichier;
    
        // Créer le dossier s’il n'existe pas
        if (!is_dir('public/uploads')) {
            mkdir('public/uploads', 0777, true);
        }
    
        // Enregistrement du fichier
        if (move_uploaded_file($fichier_pdf['tmp_name'], $destination)) {
            // Sauvegarde en base de données
            Etudiants::submitTheme($etudiantId, $theme, $binome, $nomFichier);
    
            // ✅ Redirection vers la page d’accueil
            header("Location: views/etudiant/dashboard.php");
            exit();
        } else {
            echo "Erreur lors de l’envoi du fichier.";
        }
    }
    
    public function relancer(): never {
        mail(
            'admin@votreapp.com',
            'Relance pour attribution d\'encadreur',
            'L\'étudiant ' . $_SESSION['etudiant_nom'] . ' demande à être attribué à un encadreur.',
            'From: noreply@votreapp.com'
        );
        header("Location: index.php?controller=etudiant&action=dashboard");
        exit();
    }

    public function updateProfile() {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'etudiant') {
            header('Location: index.php?controller=auth&action=login');
            exit();
        }

        $nom = $_POST['nom'];
        $prenom = $_POST['prenom'];
        $email = $_POST['email'];
        $etudiantId = $_SESSION['user']['id'];

        $db = (new Database())->getConnection();
        $stmt = $db->prepare("UPDATE etudiants SET nom = ?, prenom = ?, email = ? WHERE id = ?");
        $stmt->execute([$nom, $prenom, $email, $etudiantId]);

        header('Location: index.php?controller=etudiant&action=dashboard');
        exit();
    }

    public function profile() {
        $etudiantId = $_SESSION['etudiant_id'];
        $etudiant = Etudiants::getById($etudiantId);
        include 'views/etudiant/profile.php';
    }

    public function dashboard() {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'etudiant') {
            header('Location: index.php?controller=auth&action=login');
            exit();
        }

        $userId = $_SESSION['user']['id'];
        $username = $_SESSION['user']['username'];
        $email = $_SESSION['user']['email'] ?? '';

        $db = (new Database())->getConnection();

        $stmt = $db->prepare("SELECT e.*, u.username, enc.nom AS encadreur_nom, enc.prenom AS encadreur_prenom 
                              FROM etudiants e
                              JOIN users u ON e.id = u.id
                              LEFT JOIN affectations a ON e.id = a.etudiant_id
                              LEFT JOIN encadreurs enc ON a.encadreur_id = enc.id
                              WHERE e.id = :etudiant_id");
        $stmt->execute(['etudiant_id' => $userId]);
        $etudiant = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$etudiant) {
            $stmt = $db->prepare("INSERT INTO etudiants (user_id, nom, prenom, email, created_at) 
                                  VALUES (:user_id, :nom, :prenom, :email, NOW())");
            $stmt->execute([
                'user_id' => $userId,
                'nom' => 'À remplir',
                'prenom' => 'À remplir',
                'email' => $email
            ]);

            $stmt = $db->prepare("SELECT e.*, u.username, enc.nom AS encadreur_nom, enc.prenom AS encadreur_prenom 
                                  FROM etudiants e
                                  JOIN users u ON e.user_id = u.id
                                  LEFT JOIN affectations a ON e.id = a.etudiant_id
                                  LEFT JOIN encadreurs enc ON a.encadreur_id = enc.id
                                  WHERE e.user_id = :user_id");
            $stmt->execute(['user_id' => $userId]);
            $etudiant = $stmt->fetch(PDO::FETCH_ASSOC);
        }

        include 'views/etudiant/dashboard.php';
    }
}
