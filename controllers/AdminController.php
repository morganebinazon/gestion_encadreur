<?php
require_once 'models/Encadreur.php';
require_once 'models/Etudiants.php';

class AdminController {

    public function addEncadreur() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nom'], $_POST['prenom'], $_POST['competences'])) {
            $nom = $_POST['nom'];
            $prenom = $_POST['prenom'];
            $competences = implode(',', $_POST['competences']);
            Encadreur::create($nom, $prenom, $competences);
            header('Location: /gestion_encadreur/index.php?controller=admin&action=encadreurs');
            exit;
        } else {
            require 'views/admin/encadreur_form.php'; // Affiche le formulaire si ce n'est pas une soumission POST
        }
    }

    public function encadreurs() {
        $encadreurs = Encadreur::getAll();  // Assure-toi que cette méthode existe dans ton modèle Encadreur
        require 'views/admin/encadreurs_list.php';  // Crée cette vue pour afficher la liste
    }

    public function editEncadreur() {
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            $encadreur = Encadreur::getById($id);
            require 'views/admin/encadreur_form.php';
        }
    }

    public function updateEncadreur() {
        if (isset($_POST['id'], $_POST['nom'], $_POST['prenom'], $_POST['competences'])) {
            $id = $_POST['id'];
            $nom = $_POST['nom'];
            $prenom = $_POST['prenom'];
            $competences = implode(',', $_POST['competences']);
            Encadreur::update($id, $nom, $prenom, $competences);
        }
        header('Location: /gestion_encadreur/index.php?controller=admin&action=encadreurs');
        exit;
    }

    public function deleteEncadreur() {
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            Encadreur::delete($id);
        }
        header('Location: /gestion_encadreur/index.php?controller=admin&action=encadreurs');
        exit;
    }

    public function assignForm() {
        if (isset($_GET['id'])) {
            $encadreur_id = $_GET['id'];
            $encadreur = Encadreur::getById($encadreur_id); // Crée cette méthode si elle n'existe pas
            $etudiants = Etudiants::getAll(); // Liste des étudiants
    
            require 'views/admin/assign_encadreur.php';
        } else {
            echo "Encadreur introuvable.";
        }
    }

    public function assign() {
        if (isset($_POST['etudiants_id'], $_POST['encadreur_id'])) {
            $etudiant_id = $_POST['etudiants_id'];
            $encadreur_id = $_POST['encadreur_id'];

            Etudiants::assignEncadreur($etudiant_id, $encadreur_id);
            $etudiant = Etudiants::getById($etudiant_id);

            // Envoi de mail simple
            $to = $etudiant['email'];
            $subject = "Encadreur attribué";
            $message = "Bonjour " . $etudiant['nom'] . ", un encadreur vous a été affecté.";
            $headers = "From: noreply@votreapp.com";

            mail($to, $subject, $message, $headers);
        }

        header('Location: /gestion_encadreur/index.php?controller=admin&action=etudiants-without-encadreur');
        exit;
    }
    public function etudiantsWithoutEncadreur() {
        $etudiants = Etudiants::getEtudiantsSansEncadreur();
        require 'views/admin/etudiants_without_encadreur.php';
    }
    public function assignedStudents() {
        $db = (new Database())->getConnection();
        $stmt = $db->query("
            SELECT e.*, en.nom AS encadreur_nom, en.prenom AS encadreur_prenom
            FROM etudiants e
            JOIN affectations a ON e.id = a.etudiant_id
            JOIN encadreurs en ON a.encadreur_id = en.id
        ");
        $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
        require 'views/admin/assigned_students.php';
    }
    
    public function unassignedStudents() {
        $db = (new Database())->getConnection();
        $stmt = $db->query("
            SELECT * FROM etudiants
            WHERE id NOT IN (SELECT etudiant_id FROM affectations)
        ");
        $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
        require 'views/admin/unassigned_students.php';
    }
    public function saveAffectation() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $encadreur_id = $_POST['encadreur_id'];
            $etudiant_id = $_POST['etudiant_id'];
    
            $db = (new Database())->getConnection();
    
            // Vérifie si l'affectation existe déjà
            $stmt = $db->prepare("SELECT * FROM affectations WHERE etudiant_id = ?");
            $stmt->execute([$etudiant_id]);
            $existing = $stmt->fetch();
    
            if (!$existing) {
                $stmt = $db->prepare("INSERT INTO affectations (etudiant_id, encadreur_id) VALUES (?, ?)");
                $stmt->execute([$etudiant_id, $encadreur_id]);
            }
    
            // Redirige vers la page des étudiants assignés
            header("Location: index.php?controller=admin&action=assignedStudents");
            exit;
        }
    }
}
?>
