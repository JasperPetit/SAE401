<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$action_actuelle = $_GET['action'] ?? 'accueil';

if (!isset($_SESSION['utilisateur_id'])) {
    if ($action_actuelle !== 'login' && $action_actuelle !== 'connexion') {
        header('Location: index.php?action=login');
        exit();
    }
}
?>