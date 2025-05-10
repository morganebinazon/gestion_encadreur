<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'config/database.php';

class AuthController {
    /**
     * Affiche le formulaire de connexion
     */
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
            $password = $_POST['password'];
            
            if (!$email || !$password) {
                $error = "Veuillez remplir tous les champs.";
                include 'views/auth/login.php';
                return;
            }
    
            $db = Database::getConnection();
            
            // Vérifier d'abord dans la table users
            $stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
            if ($user && password_verify($password, $user['password'])) {
                // Stockage des informations de session
                $_SESSION['user'] = [
                    'id' => $user['id'],
                    'role' => $user['role'],
                    'username' => $user['username'],
                    'email' => $user['email']
                ];
                
                // Redirection selon le rôle
                if ($user['role'] === 'etudiant') {
                    $_SESSION['etudiant_id'] = $user['id']; 
                    header("Location: index.php?controller=etudiant&action=dashboard");
                    exit();
                } elseif ($user['role'] === 'admin') {
                    header("Location: index.php?controller=admin&action=encadreurs");
                    exit();
                } elseif ($user['role'] === 'encadreur') {
                    header("Location: index.php?controller=encadreur&action=dashboard");
                    exit();
                }
            } else {
                // Si pas trouvé dans users, vérifier dans encadreurs
                $stmt = $db->prepare("SELECT * FROM encadreurs WHERE email = ?");
                $stmt->execute([$email]);
                $encadreur = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($encadreur && password_verify($password, $encadreur['mot_de_passe'])) {
                    // Stockage des informations de session pour encadreur
                    $_SESSION['user'] = [
                        'id' => $encadreur['id'],
                        'role' => 'encadreur',
                        'username' => $encadreur['nom'] . ' ' . $encadreur['prenom'],
                        'email' => $encadreur['email']
                    ];
                    
                    header("Location: index.php?controller=encadreur&action=dashboard");
                    exit();
                } else {
                    $error = "Identifiants incorrects.";
                    include 'views/auth/login.php';
                }
            }
        } else {
            include 'views/auth/login.php';
        }
    }
    
    /**
     * Affiche le formulaire d'inscription et traite la soumission
     */
    public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_SPECIAL_CHARS);
            $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
            $password = $_POST['password'];
            $confirm = $_POST['confirm_password'];
            
            if (!$username || !$email || !$password || !$confirm) {
                $error = "Veuillez remplir tous les champs.";
                include 'views/auth/register.php';
                return;
            }
    
            $db = Database::getConnection();
    
            // Vérifier si l'email ou le nom d'utilisateur existe déjà
            $stmt = $db->prepare("SELECT * FROM users WHERE email = ? OR username = ?");
            $stmt->execute([$email, $username]);
            if ($stmt->fetch()) {
                $error = "Email ou nom d'utilisateur déjà utilisé.";
                include 'views/auth/register.php';
                return;
            }
    
            // Vérifier si les mots de passe correspondent
            if ($password !== $confirm) {
                $error = "Les mots de passe ne correspondent pas.";
                include 'views/auth/register.php';
                return;
            }
    
            try {
                $db->beginTransaction();
                
                // Créer l'utilisateur
                $hash = password_hash($password, PASSWORD_BCRYPT);
                $stmt = $db->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, 'etudiant')");
                $stmt->execute([$username, $email, $hash]);
                $userId = $db->lastInsertId();
                
                // Créer le profil étudiant
                $stmt2 = $db->prepare("INSERT INTO etudiants (id, nom, prenom, email, role, created_at) VALUES (?, ?, ?, ?, 'etudiant', NOW())");
                $stmt2->execute([$userId, 'À compléter', 'À compléter', $email]);
                
                $db->commit();
                
                $_SESSION['flash_message'] = "Inscription réussie. Vous pouvez maintenant vous connecter.";
                header("Location: index.php?controller=auth&action=login");
                exit();
            } catch (PDOException $e) {
                $db->rollBack();
                $error = "Erreur lors de l'inscription : " . $e->getMessage();
                include 'views/auth/register.php';
                return;
            }
        } else {
            include 'views/auth/register.php';
        }
    }

    /**
     * Déconnexion de l'utilisateur
     */
    public function logout() {
        session_unset();  // Efface toutes les variables de session
        session_destroy(); // Détruit la session
        
        // Rediriger vers la page de connexion
        header("Location: index.php?controller=auth&action=login");
        exit();
    }
}
?>