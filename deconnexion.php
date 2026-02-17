<?php
// Détruit la session de l'utilisateur puis redirige vers la page de connexion
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Supprimer toutes les variables de session
$_SESSION = [];

// Supprimer le cookie de session si présent
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params['path'], $params['domain'], $params['secure'], $params['httponly']
    );
}

// Détruire la session côté serveur
session_destroy();

// Redirection vers la page de connexion
header('Location: connexion.php');
exit;

?>
