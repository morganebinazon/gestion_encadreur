<?php
/**
 * Vérifie si l'utilisateur est authentifié
 * 
 * @return bool True si l'utilisateur est authentifié, sinon False
 */
function isAuthenticated() {
    return isset($_SESSION['user']);
}

/**
 * Vérifie si l'utilisateur possède le rôle requis
 * Redirige vers la page de connexion si ce n'est pas le cas
 * 
 * @param string $role Le rôle requis ('admin', 'etudiant', 'encadreur')
 * @return void
 */
function requireRole($role) {
    if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== $role) {
        // Enregistrer un message d'erreur si nécessaire
        $_SESSION['flash_message'] = "Vous n'avez pas les droits nécessaires pour accéder à cette page.";
        
        // Rediriger vers la page de connexion
        header("Location: index.php?controller=auth&action=login");
        exit();
    }
}
?>