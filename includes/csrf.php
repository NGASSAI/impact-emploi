<?php
/**
 * PROTECTION CSRF - Cross-Site Request Forgery
 * 
 * Ce fichier génère et valide les tokens CSRF pour protéger les formulaires
 * contre les attaques CSRF (requêtes forgées au nom de l'utilisateur).
 * 
 * Utilisation :
 *   - Dans les formulaires : echo generateCSRFToken();
 *   - Dans les traitements POST : verifyCSRFToken($_POST['csrf_token']);
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Génère un token CSRF unique pour la session
 * Stocké en $_SESSION['csrf_token']
 * À insérer dans chaque formulaire POST
 */
function generateCSRFToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Valide un token CSRF reçu
 * Utilise hash_equals() pour éviter les timing attacks
 * 
 * @param string $token Token à valider (généralement $_POST['csrf_token'])
 * @return bool true si valide, false sinon
 */
function verifyCSRFToken($token) {
    if (empty($_SESSION['csrf_token']) || empty($token)) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Helper pour afficher le champ HTML du token
 * À utiliser directement dans les formulaires avec : <?php csrfField(); ?>
 */
function csrfField() {
    echo '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(generateCSRFToken()) . '">';
}

?>
