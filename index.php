<?php
session_start(); // Démarrage de la session en début de script

// Récupération des paramètres depuis l'URL avec des valeurs par défaut
$controller = $_GET['controller'] ?? 'auth';   // Par défaut : auth
$action = $_GET['action'] ?? 'login';          // Par défaut : login

// Inclure le middleware pour vérifier les rôles
require_once 'middlewares/AuthMiddleware.php';

// Inclure la connexion à la base de données
require_once 'config/database.php';

// Gestion des routes en fonction du contrôleur
switch ($controller) {
    case 'auth':
        require_once 'controllers/authController.php';
        $auth = new AuthController();
        if (method_exists($auth, $action)) {
            $auth->$action();
        } else {
            echo "Action '$action' non trouvée dans AuthController.";
        }
        break;

    case 'admin':
        // Middleware pour vérifier que l'utilisateur est un administrateur
        requireRole('admin');

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
        requireRole('etudiant');

        require_once 'controllers/EtudiantController.php';
        $etudiant = new EtudiantController();

        // Gestion des actions POST envoyées par l'étudiant
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['updateProfile']) && method_exists($etudiant, 'updateProfile')) {
                $etudiant->updateProfile();
            } elseif (isset($_POST['relancer']) && method_exists($etudiant, 'relancer')) {
                $etudiant->relancer();
            } elseif (isset($_POST['submitForm']) && method_exists($etudiant, 'submitForm')) {
                $etudiant->submitForm();
            } elseif (method_exists($etudiant, $action)) {
                $etudiant->$action();
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

    case 'encadreur':
        // Middleware pour vérifier que l'utilisateur est un encadreur
        requireRole('encadreur');

        require_once 'controllers/EncadreurController.php';
        $encadreur = new EncadreurController();
        if (method_exists($encadreur, $action)) {
            $encadreur->$action();
        } else {
            echo "Action '$action' non trouvée dans EncadreurController.";
        }
        break;

    default:
        echo "Contrôleur '$controller' non reconnu.";
        break;
}
?>