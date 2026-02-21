<?php
require_once 'config.php';

// Enregistrer l'activité
if(isset($_SESSION['auth_id'])) {
    log_activity($_SESSION['auth_id'], 'logout', 'Déconnexion');
}

// Détruire la session
$_SESSION = array();

if(ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

session_destroy();

// Rediriger vers l'accueil
header('Location: ' . BASE_URL . '/index.php?success=Vous avez été déconnecté');
exit();
