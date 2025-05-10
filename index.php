<?php
session_start(); // Toujours au début

// Récupération des paramètres depuis l'URL avec des valeurs par défaut
$controller = $_GET['controller'] ?? 'auth';   // Par défaut : auth
$action = $_GET['action'] ?? 'register';       // Par défaut : register

// Inclure le middleware pour vérifier les rôles
require_once 'middlewares/AuthMiddleware.php';  // Inclus une seule fois

// Gestion des routes en fonction du contrôleur
switch ($controller) {
    case 'auth':
        require_once 'controllers/authController.php';
        $auth = new authController();
        if (method_exists($auth, $action)) {
            $auth->$action();
        } else {
            echo "Action '$action' non trouvée dans AuthController.";
        }
        break;

    case 'admin':
        // Middleware pour vérifier que l'utilisateur est un administrateur
        requireRole('admin');  // Assurer que l'utilisateur est administrateur

        require_once 'controllers/AdminController.php';
        $admin = new AdminController();
        if (method_exists($admin, $action)) {
            $admin->$action();
        } else {
            echo "Action '$action' non trouvée dans AdminController.";
        }
        break;

    case 'etudiant':
        // Middleware pour vérifier que l'utilisateur est un étudiant
        requireRole('etudiant');  // Assurer que l'utilisateur est un étudiant

        require_once 'controllers/EtudiantController.php';
        $etudiant = new EtudiantController();

        // Gestion des actions POST envoyées par l'étudiant
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (method_exists($etudiant, $action)) {
                $etudiant->$action();
            } elseif (isset($_POST['updateProfile']) && method_exists($etudiant, 'updateProfile')) {
                $etudiant->updateProfile();
            } elseif (isset($_POST['relancer']) && method_exists($etudiant, 'relancer')) {
                $etudiant->relancer();
            } else {
                echo "Action POST inconnue dans EtudiantController.";
            }
        } else {
            // Gestion des actions GET envoyées par l'étudiant
            if (method_exists($etudiant, $action)) {
                $etudiant->$action();
            } else {
                echo "Action '$action' non trouvée dans EtudiantController.";
            }
        }
        break;

    default:
        echo "Contrôleur '$controller' non reconnu.";
        break;
}
?>
