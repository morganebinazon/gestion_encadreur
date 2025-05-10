<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'models/Encadreur.php';
require_once 'models/Etudiants.php';
require_once 'config/database.php';

class AdminController {
    /**
     * Affiche le formulaire d'ajout d'encadreur et traite la soumission
     */
    public function addEncadreur() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nom'], $_POST['prenom'], $_POST['competences'])) {
            $nom = filter_input(INPUT_POST, 'nom', FILTER_SANITIZE_SPECIAL_CHARS);
            $prenom = filter_input(INPUT_POST, 'prenom', FILTER_SANITIZE_SPECIAL_CHARS);
            $competences = isset($_POST['competences']) ? implode(',', $_POST['competences']) : '';
            
            try {
                Encadreur::create($nom, $prenom, $competences);
                $_SESSION['flash_message'] = "Encadreur ajouté avec succès.";
                header('Location: index.php?controller=admin&action=encadreurs');
                exit;
            } catch (Exception $e) {
                $error = "Erreur lors de l'ajout de l'encadreur: " . $e->getMessage();
                require 'views/admin/encadreur_form.php';
            }
        } else {
            require 'views/admin/encadreur_form.php';
        }
    }

    /**
     * Affiche la liste des encadreurs
     */
    public function encadreurs() {
        $encadreurs = Encadreur::getAll();
        require 'views/admin/encadreurs_list.php';
    }

    /**
     * Affiche le formulaire de modification d'un encadreur
     */
    public function editEncadreur() {
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        
        if (!$id) {
            $_SESSION['flash_message'] = "ID d'encadreur invalide.";
            header('Location: index.php?controller=admin&action=encadreurs');
            exit;
        }
        
        try {
            $encadreur = Encadreur::getById($id);
            if (!$encadreur) {
                throw new Exception("Encadreur non trouvé.");
            }
            require 'views/admin/encadreur_form.php';
        } catch (Exception $e) {
            $_SESSION['flash_message'] = "Erreur: " . $e->getMessage();
            header('Location: index.php?controller=admin&action=encadreurs');
            exit;
        }
    }

    /**
     * Met à jour les informations d'un encadreur
     */
    public function updateEncadreur() {
        if (isset($_POST['id'], $_POST['nom'], $_POST['prenom'])) {
            $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
            $nom = filter_input(INPUT_POST, 'nom', FILTER_SANITIZE_SPECIAL_CHARS);
            $prenom = filter_input(INPUT_POST, 'prenom', FILTER_SANITIZE_SPECIAL_CHARS);
            $competences = isset($_POST['competences']) ? implode(',', $_POST['competences']) : '';
            
            try {
                Encadreur::update($id, $nom, $prenom, $competences);
                $_SESSION['flash_message'] = "Encadreur mis à jour avec succès.";
            } catch (Exception $e) {
                $_SESSION['flash_message'] = "Erreur lors de la mise à jour: " . $e->getMessage();
            }
        } else {
            $_SESSION['flash_message'] = "Données manquantes pour la mise à jour.";
        }
        
        header('Location: index.php?controller=admin&action=encadreurs');
        exit;
    }

    /**
     * Supprime un encadreur
     */
    public function deleteEncadreur() {
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        
        if (!$id) {
            $_SESSION['flash_message'] = "ID d'encadreur invalide.";
        } else {
            try {
                // Vérifier si l'encadreur a des étudiants assignés
                $db = Database::getConnection();
                $stmt = $db->prepare("SELECT COUNT(*) FROM affectations WHERE encadreur_id = ?");
                $stmt->execute([$id]);
                $count = $stmt->fetchColumn();
                
                if ($count > 0) {
                    $_SESSION['flash_message'] = "Impossible de supprimer cet encadreur car il a des étudiants assignés.";
                } else {
                    Encadreur::delete($id);
                    $_SESSION['flash_message'] = "Encadreur supprimé avec succès.";
                }
            } catch (Exception $e) {
                $_SESSION['flash_message'] = "Erreur lors de la suppression: " . $e->getMessage();
            }
        }
        
        header('Location: index.php?controller=admin&action=encadreurs');
        exit;
    }

    /**
     * Affiche le formulaire d'assignation d'un étudiant à un encadreur
     */
    public function assignForm() {
        $encadreur_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        
        if (!$encadreur_id) {
            $_SESSION['flash_message'] = "ID d'encadreur invalide.";
            header('Location: index.php?controller=admin&action=encadreurs');
            exit;
        }
        
        try {
            $encadreur = Encadreur::getById($encadreur_id);
            $etudiants = Etudiants::getEtudiantsSansEncadreur();
            
            require 'views/admin/assign_encadreur.php';
        } catch (Exception $e) {
            $_SESSION['flash_message'] = "Erreur: " . $e->getMessage();
            header('Location: index.php?controller=admin&action=encadreurs');
            exit;
        }
    }

    /**
     * Traite l'assignation d'un étudiant à un encadreur
     */
    public function saveAffectation() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $encadreur_id = filter_input(INPUT_POST, 'encadreur_id', FILTER_VALIDATE_INT);
            $etudiant_id = filter_input(INPUT_POST, 'etudiant_id', FILTER_VALIDATE_INT);
            
            if (!$encadreur_id || !$etudiant_id) {
                $_SESSION['flash_message'] = "Identifiants invalides.";
                header("Location: index.php?controller=admin&action=encadreurs");
                exit;
            }
    
            $db = Database::getConnection();
    
            // Vérifier si l'affectation existe déjà
            $stmt = $db->prepare("SELECT * FROM affectations WHERE etudiant_id = ?");
            $stmt->execute([$etudiant_id]);
            $existing = $stmt->fetch();
    
            if (!$existing) {
                try {
                    $db->beginTransaction();
                    
                    $stmt = $db->prepare("INSERT INTO affectations (etudiant_id, encadreur_id, date_affectation) VALUES (?, ?, NOW())");
                    $stmt->execute([$etudiant_id, $encadreur_id]);
                    
                    // Récupérer les informations de l'étudiant pour l'email
                    $stmt = $db->prepare("SELECT * FROM etudiants WHERE id = ?");
                    $stmt->execute([$etudiant_id]);
                    $etudiant = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    // Récupérer les informations de l'encadreur
                    $stmt = $db->prepare("SELECT * FROM encadreurs WHERE id = ?");
                    $stmt->execute([$encadreur_id]);
                    $encadreur = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    $db->commit();
                    
                    // Envoyer un email à l'étudiant si son email est défini
                    if (isset($etudiant['email']) && !empty($etudiant['email'])) {
                        $to = $etudiant['email'];
                        $subject = "Affectation d'un encadreur";
                        $message = "Bonjour " . $etudiant['prenom'] . " " . $etudiant['nom'] . ",\n\n";
                        $message .= "Nous avons le plaisir de vous informer qu'un encadreur vous a été affecté pour votre projet.\n";
                        $message .= "Votre encadreur est : " . $encadreur['prenom'] . " " . $encadreur['nom'] . "\n\n";
                        $message .= "Vous pouvez dès à présent le contacter pour discuter de votre projet.\n\n";
                        $message .= "Cordialement,\nL'administration";
                        
                        $headers = "From: noreply@gestion-encadreurs.com";
                        
                        // Envoyer l'email (désactivé pour le moment - à activer en production)
                        // mail($to, $subject, $message, $headers);
                    }
                    
                    $_SESSION['flash_message'] = "Étudiant affecté à l'encadreur avec succès.";
                } catch (Exception $e) {
                    $db->rollBack();
                    $_SESSION['flash_message'] = "Erreur lors de l'affectation: " . $e->getMessage();
                }
            } else {
                $_SESSION['flash_message'] = "Cet étudiant est déjà affecté à un encadreur.";
            }
    
            header("Location: index.php?controller=admin&action=assignedStudents");
            exit;
        }
    }

    /**
     * Affiche la liste des étudiants sans encadreur
     */
    public function etudiantsWithoutEncadreur() {
        $etudiants = Etudiants::getEtudiantsSansEncadreur();
        require 'views/admin/etudiants_without_encadreur.php';
    }

    /**
     * Affiche la liste des étudiants assignés à un encadreur
     */
    public function assignedStudents() {
        $db = Database::getConnection();
        $stmt = $db->query("
            SELECT e.*, en.nom AS encadreur_nom, en.prenom AS encadreur_prenom, a.date_affectation
            FROM etudiants e
            JOIN affectations a ON e.id = a.etudiant_id
            JOIN encadreurs en ON a.encadreur_id = en.id
            ORDER BY a.date_affectation DESC
        ");
        $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
        require 'views/admin/assigned_students.php';
    }
    
    /**
     * Affiche la liste des étudiants sans encadreur
     */
    public function unassignedStudents() {
        $db = Database::getConnection();
        $stmt = $db->query("
            SELECT * FROM etudiants
            WHERE id NOT IN (SELECT etudiant_id FROM affectations)
            ORDER BY created_at DESC
        ");
        $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
        require 'views/admin/unassigned_students.php';
    }
}
?>