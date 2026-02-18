<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// === HEADERS DE SÉCURITÉ ===
header('X-Frame-Options: SAMEORIGIN', true);
header('X-Content-Type-Options: nosniff', true);
header('X-XSS-Protection: 1; mode=block', true);
header("Content-Security-Policy: default-src 'self'; script-src 'self'; style-src 'self' https://fonts.googleapis.com; font-src https://fonts.gstatic.com; img-src 'self' data: https:; object-src 'none'; base-uri 'self'; form-action 'self';", true);

require_once __DIR__ . '/config.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Impact Emploi - Trouvez votre avenir</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="assets/js/ui-components.js" defer></script>
</head>
<body>

<nav>
    <a href="index.php" class="nav-logo">Impact Emploi</a>
    
    <!-- Hamburger menu pour mobile -->
    <button class="hamburger" id="hamburger" aria-label="Menu">
        <span></span>
        <span></span>
        <span></span>
    </button>
    
    <!-- Menu principal (mobile-friendly) -->
    <div class="nav-menu" id="nav-menu">
        <?php
        // Afficher le badge utilisateur et déterminer si on doit afficher le lien admin
        $is_admin_link = false;
        if (isset($_SESSION['user_id'])) {
            try {
                $stmt = $db->prepare('SELECT prenom, nom, email, role FROM users WHERE id = ?');
                $stmt->execute([$_SESSION['user_id']]);
                $row = $stmt->fetch();
                if ($row) {
                    $initiales = strtoupper(substr($row['prenom'] ?? '', 0, 1)) . strtoupper(substr($row['nom'] ?? '', 0, 1));
                    $nom_complet = htmlspecialchars(($row['prenom'] ?? '') . ' ' . ($row['nom'] ?? ''));
                    echo "<span class='user-badge' title='" . $nom_complet . "'>" . $initiales . "</span>";
                    if ((!empty($row['role']) && $row['role'] === 'admin') || (defined('DEFAULT_ADMIN_EMAIL') && ($row['email'] ?? '') === DEFAULT_ADMIN_EMAIL)) {
                        $is_admin_link = true;
                    }
                }
            } catch (Exception $e) { /* silent */ }
        }
        ?>

        <a href="index.php">🏠 Accueil</a>

        <?php if(isset($_SESSION['user_id'])): ?>
            <?php if($is_admin_link): ?>
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

<script>
    // Hamburger menu toggle
    const hamburger = document.getElementById('hamburger');
    const navMenu = document.getElementById('nav-menu');
    
    if (hamburger) {
        hamburger.addEventListener('click', () => {
            hamburger.classList.toggle('active');
            navMenu.classList.toggle('active');
        });
        
        // Fermer le menu quand on clique sur un lien
        navMenu.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', () => {
                hamburger.classList.remove('active');
                navMenu.classList.remove('active');
            });
        });
    }
</script>

<div class="container">