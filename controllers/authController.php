<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'config/database.php';

class authController {
    public function form() {
        require_once 'views/auth/login.php';
    }
    public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'];
            $email = $_POST['email'];
            $password = $_POST['password'];
            $confirm = $_POST['confirm_password'];
    
            $pdo = Database::getConnection();
    
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? OR username = ?");
            $stmt->execute([$email, $username]);
            if ($stmt->fetch()) {
                $error = "Email ou nom d'utilisateur déjà utilisé.";
                include 'views/auth/register.php';
                return;
            }
    
            if ($password !== $confirm) {
                $error = "Les mots de passe ne correspondent pas.";
                include 'views/auth/register.php';
                return;
            }
    
            $hash = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, 'etudiant')");
            $stmt->execute([$username, $email, $hash]);
            

        $userId = $pdo->lastInsertId();

        $stmt2 = $pdo->prepare("INSERT INTO etudiants (id, nom, prenom, email, role, created_at) VALUES (?, ?, ?, ?, 'etudiant', NOW())");
        $stmt2->execute([$userId, 'Nom', 'Prénom', $email]);
            header("Location: index.php?controller=auth&action=form");
        } else {
            include 'views/auth/register.php';
        }
        
    }
    

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'];
            $password = $_POST['password'];
    
            $pdo = Database::getConnection();
            
            // Vérifier d'abord dans la table users
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();
    
            if ($user && password_verify($password, $user['password'])) {
                // Stockage des informations de session
                $_SESSION['user'] = [
                    'id' => $user['id'],
                    'role' => $user['role'],
                    'username' => $user['username']
                ];
                
                // Redirection selon le rôle
                if ($user['role'] === 'etudiant') {
                    $_SESSION['etudiant_id'] = $user['id']; 
                    header("Location: index.php?controller=etudiant&action=dashboard");
                    exit();
                } elseif ($user['role'] === 'admin') {
                    header("Location: index.php?controller=admin&action=encadreurs");
                    exit();
                }
            } else {
                // Si pas trouvé dans users, vérifier dans encadreurs
                $stmt = $pdo->prepare("SELECT * FROM encadreurs WHERE email = ?");
                $stmt->execute([$email]);
                $encadreur = $stmt->fetch();
                
                if ($encadreur && password_verify($password, $encadreur['mot_de_passe'])) {
                    // Stockage des informations de session pour encadreur
                    $_SESSION['user'] = [
                        'id' => $encadreur['id'],
                        'role' => 'admin', // On considère les encadreurs comme des admins
                        'username' => $encadreur['nom'] . ' ' . $encadreur['prenom']
                    ];
                    
                    header("Location: index.php?controller=admin&action=encadreurs");
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
    
    

    public function logout() {
        session_unset();  // Efface toutes les variables de session
        session_destroy();
        header("Location: index.php?controller=auth&action=login");
        exit();
    }
}
