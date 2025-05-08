<?php
require_once('controllers/AdminController.php');

$controller = $_GET['controller'] ?? 'admin';  // Contrôleur par défaut
$action = $_GET['action'] ?? 'encadreurs';     // Action par défaut

// Vérification si le contrôleur est admin
if ($controller === 'admin') {
    $admin = new AdminController();

    // Vérification si la méthode existe dans le contrôleur
    if (method_exists($admin, $action)) {
        $admin->$action();  // Appel de la méthode
    } else {
        echo "Action non trouvée";  // Si la méthode n'existe pas
    }
}
