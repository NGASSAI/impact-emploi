<?php
// Vérifier la session
if(!isset($_SESSION)) {
    session_start();
}
require_once dirname(__DIR__) . '/config.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="theme-color" content="#0052A3">
    <meta name="description" content="Impact Emploi - La plateforme d'emploi au Congo - Trouvez votre emploi idéal ou recrutez les meilleurs talents">
    <title>Impact Emploi - Trouvez votre emploi</title>
    
    <!-- Favicons -->
    <link rel="icon" type="image/png" sizes="192x192" href="<?php echo BASE_URL; ?>/assets/img/icon-192.png">
    <link rel="icon" type="image/png" sizes="512x512" href="<?php echo BASE_URL; ?>/assets/img/icon-512.png">
    
    <!-- PWA Manifest -->
    <link rel="manifest" href="<?php echo BASE_URL; ?>/manifest.json">
    
    <!-- Service Worker Registration -->
    <script>
    // Désactiver temporairement le Service Worker pour tester
    // if ('serviceWorker' in navigator) {
    //     window.addEventListener('load', function() {
    //         navigator.serviceWorker.register('<?php echo BASE_URL; ?>/sw.js')
    //             .then(function(registration) {
    //                 console.log('SW registered: ', registration);
    //             })
    //             .catch(function(registrationError) {
    //                 console.log('SW registration failed: ', registrationError);
    //             });
    //     });
    // }
    </script>
    
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css?v=<?php echo CACHE_BUST; ?>">
</head>
<body>
<header>
    <nav class="container flex-between">
        <a href="<?php echo BASE_URL; ?>/index.php" class="logo">Impact Emploi</a>
        <button id="navToggle" class="hamburger" aria-label="Ouvrir le menu" aria-expanded="false">☰</button>
        <div class="nav-links" id="navLinks">
            <?php if(isset($_SESSION['auth_id'])): ?>
                <?php
                $avatar = $_SESSION['auth_photo'] ?? null;
                if (isset($_SESSION['auth_id']) && empty($avatar)) {
                    try {
                        $stmtA = $pdo->prepare('SELECT photo_profil FROM users WHERE id = ?');
                        $stmtA->execute([$_SESSION['auth_id']]);
                        $rA = $stmtA->fetch();
                        if($rA && !empty($rA['photo_profil'])) {
                            $avatar = $rA['photo_profil'];
                            $_SESSION['auth_photo'] = $avatar;
                        }
                    } catch(Exception $e) {}
                }
                ?>
                <?php if(!empty($avatar)): ?>
                    <a href="<?php echo BASE_URL; ?>/profil.php" style="display:inline-block; margin-right:8px;">
                    <?php
                        $avatar_path = __DIR__ . '/../uploads/profiles/' . htmlspecialchars($avatar);
                        if(!file_exists($avatar_path)) {
                            $avatar = 'default-avatar.php';
                        }
                    ?>
                    <img src="<?php echo BASE_URL; ?>/uploads/profiles/<?php echo htmlspecialchars($avatar); ?>" 
                         alt="avatar" 
                         width="36" height="36"
                         loading="lazy" 
                         decoding="async"
                         onerror="this.src='<?php echo BASE_URL; ?>/default-avatar.php'"
                         style="width:36px;height:36px;border-radius:50%;object-fit:cover;border:2px solid rgba(255,255,255,0.15);vertical-align:middle;margin-right:8px;background:#eee;"></a>
                <?php else: ?>
                    <a href="<?php echo BASE_URL; ?>/profil.php" style="display:inline-block; margin-right:8px;">
                    <img src="<?php echo BASE_URL; ?>/default-avatar.php" 
                         alt="avatar par défaut" 
                         width="36" height="36"
                         style="width:36px;height:36px;border-radius:50%;object-fit:cover;border:2px solid rgba(255,255,255,0.15);vertical-align:middle;margin-right:8px;"></a>
                <?php endif; ?>
                <span class="text-muted" style="color: white; white-space: nowrap;">Bienvenue, <?php echo htmlspecialchars($_SESSION['auth_nom']); ?></span>
                
                <?php if($_SESSION['auth_role'] === 'admin'): ?>
                    <a href="<?php echo BASE_URL; ?>/admin_dashboard.php" style="background: rgba(255,255,255,0.2); padding: 8px 16px; border-radius: 6px;">📊 Tableau de Bord</a>
                    <a href="<?php echo BASE_URL; ?>/sante.php" style="background: rgba(255,255,255,0.2); padding: 8px 16px; border-radius: 6px;">✅ Santé du Site</a>
                <?php elseif($_SESSION['auth_role'] === 'recruteur'): ?>
                    <a href="<?php echo BASE_URL; ?>/recruteur_dashboard.php" style="background: rgba(255,255,255,0.2); padding: 8px 16px; border-radius: 6px;">👥 Candidatures</a>
                <?php elseif($_SESSION['auth_role'] === 'candidat'): ?>
                    <a href="<?php echo BASE_URL; ?>/candidat_dashboard.php" style="background: rgba(255,255,255,0.2); padding: 8px 16px; border-radius: 6px;">📋 Mes Candidatures</a>
                <?php endif; ?>
                
                <a href="<?php echo BASE_URL; ?>/aide.php" style="background: rgba(255,255,255,0.2); padding: 8px 16px; border-radius: 6px;" title="Afficher l'aide">❓ Aide</a>
                <a href="<?php echo BASE_URL; ?>/resources.php" style="background: rgba(255,255,255,0.2); padding: 8px 16px; border-radius: 6px;" title="Ressources">📚 Ressources</a>
                <a href="<?php echo BASE_URL; ?>/feedback.php" style="background: rgba(255,255,255,0.2); padding: 8px 16px; border-radius: 6px;" title="Envoyer un feedback">💬 Feedback</a>
                
                <a href="<?php echo BASE_URL; ?>/profil.php" style="background: rgba(255,255,255,0.2); padding: 8px 16px; border-radius: 6px;">👤 Profil</a>
                <a href="<?php echo BASE_URL; ?>/logout.php" class="btn btn-small" style="background: #EF4444; color: white;">Déconnexion</a>
            <?php else: ?>
                <a href="<?php echo BASE_URL; ?>/login.php" style="color: white; text-decoration: none;">Connexion</a>
                <a href="<?php echo BASE_URL; ?>/register.php" class="btn btn-primary btn-small">S'inscrire</a>
            <?php endif; ?>
        </div>
    </nav>
</header>
<main>
