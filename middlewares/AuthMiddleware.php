<?php
function isAuthenticated() {
    return isset($_SESSION['user']);
}

function requireRole($role) {
    if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== $role) {
        header("Location: index.php?controller=auth&action=login");
        exit();
    }
}
