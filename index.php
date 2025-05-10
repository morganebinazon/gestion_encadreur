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
        $authController = new AuthController();
        if (method_exists($authController, $action)) {
            $authController->$action();
        } else {
            echo "Action '$action' non trouvée dans AuthController.";
        }
        break;

    case 'admin':
        // Middleware pour vérifier que l'utilisateur est un administrateur
        requireRole('admin');

        require_once 'controllers/AdminController.php';
        $adminController = new AdminController();
        if (method_exists($adminController, $action)) {
            $adminController->$action();
        } else {
            echo "Action '$action' non trouvée dans AdminController.";
        }
        break;

    case 'etudiant':
        // Middleware pour vérifier que l'utilisateur est un étudiant
        requireRole('etudiant');

        require_once 'controllers/etudiantController.php';
        $etudiantController = new EtudiantController();  // Changé de $etudiant à $etudiantController

        // Gestion des actions POST envoyées par l'étudiant
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['updateProfile']) && method_exists($etudiantController, 'updateProfile')) {
                $etudiantController->updateProfile();
            } elseif (isset($_POST['relancer']) && method_exists($etudiantController, 'relancer')) {
                $etudiantController->relancer();
            } elseif (isset($_POST['submitForm']) && method_exists($etudiantController, 'submitForm')) {
                $etudiantController->submitForm();
            } elseif (method_exists($etudiantController, $action)) {
                $etudiantController->$action();
            } else {
                echo "Action POST inconnue dans EtudiantController.";
            }
        } else {
            // Gestion des actions GET envoyées par l'étudiant
            if (method_exists($etudiantController, $action)) {
                $etudiantController->$action();
            } else {
                echo "Action '$action' non trouvée dans EtudiantController.";
            }
        }
        break;

    case 'encadreur':
        // Middleware pour vérifier que l'utilisateur est un encadreur
        requireRole('encadreur');

        require_once 'controllers/encadreurController.php';
        $encadreurController = new EncadreurController();  // Changé de $encadreur à $encadreurController
        if (method_exists($encadreurController, $action)) {
            $encadreurController->$action();
        } else {
            echo "Action '$action' non trouvée dans EncadreurController.";
        }
        break;

    default:
        echo "Contrôleur '$controller' non reconnu.";
        break;
}
?>