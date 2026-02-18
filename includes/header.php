<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// === HEADERS DE SÉCURITÉ ===
// Empêche les attaques clickjacking (UI Redressing)
header('X-Frame-Options: SAMEORIGIN', true);

// Force HTTPS lorsqu'en production (à décommenter une fois certificat SSL installé)
// header('Strict-Transport-Security: max-age=31536000; includeSubDomains', true);

// Empêche le sniffing de contenu (force à respecter le Content-Type correct)
header('X-Content-Type-Options: nosniff', true);

// Complément à htmlspecialchars() contre les attaques XSS
header('X-XSS-Protection: 1; mode=block', true);

// CSP améliorée (Content Security Policy) - restrict où les ressources peuvent venir
// Permet les styles depuis 'self' et Google Fonts, interdit les styles inline
header("Content-Security-Policy: default-src 'self'; script-src 'self'; style-src 'self' https://fonts.googleapis.com; font-src https://fonts.gstatic.com; img-src 'self' data: https:; object-src 'none'; base-uri 'self'; form-action 'self';", true);

// Charge la configuration
require_once __DIR__ . '/config.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Impact Emploi - Trouvez votre avenir</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<nav>
    <a href="index.php">Impact Emploi</a>
    <div>
        <?php
        if (isset($_SESSION['user_id'])) {
            try {
                // récupérer le prénom et nom de l'utilisateur
                $stmt = $db->prepare('SELECT prenom, nom FROM users WHERE id = ?');
                $stmt->execute([$_SESSION['user_id']]);
                $row = $stmt->fetch();
                if ($row) {
                    $initiales = strtoupper($row['prenom'][0] ?? '') . strtoupper($row['nom'][0] ?? '');
                    $nom_complet = htmlspecialchars($row['prenom'] . ' ' . $row['nom']);
                    echo "<span class='user-badge' title='$nom_complet'>$initiales</span>";
                }
            } catch (Exception $e) { /* silent */ }
        }
        ?>

        <?php if(isset($_SESSION['user_id'])): ?>
            <?php if($_SESSION['user_id'] == 1): ?>
                <a href="admin_dashboard.php">📊 Tableau de Bord</a>
            <?php endif; ?>
            <a href="mon_espace.php">Mon Espace</a>
            <a href="profil.php">Mon Profil</a>
            <a href="suggestions.php">Suggestions</a>
            <a href="deconnexion.php">Déconnexion</a>
        <?php else: ?>
            <div class="nav-auth">
                <a href="connexion.php">Connexion</a>
                <a href="inscription.php">S'inscrire</a>
            </div>
        <?php endif; ?>
    </div>
 </nav>
 
 <div class="container">