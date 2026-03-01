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
    
    <!-- Lien Accueil -->
    <link rel="home" href="<?php echo BASE_URL; ?>/index.php">
    
    <!-- PWA Manifest Désactivé -->
    <!-- <link rel="manifest" href="<?php echo BASE_URL; ?>/manifest.json"> -->
    
    <!-- Service Worker Désactivé -->
    <script>
    // PWA désactivé pour optimisation responsive
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
    
    <!-- Styles pour les notifications -->
    <style>
        /* Badge de notification */
        .notification-badge {
            position: relative;
        }
        .notification-badge::after {
            content: attr(data-count);
            position: absolute;
            top: -5px;
            right: -5px;
            background: #ff4757;
            color: white;
            border-radius: 50%;
            padding: 2px 6px;
            font-size: 10px;
            font-weight: bold;
            min-width: 18px;
            text-align: center;
            display: none;
        }
        .notification-badge.has-notifications::after {
            display: block;
            animation: pulse 1.5s infinite;
        }
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.2); }
        }
        
        /* Container des notifications toast */
        #notifications-container {
            position: fixed;
            top: 70px;
            right: 20px;
            z-index: 10000;
            max-width: 400px;
            width: 100%;
        }
        
        /* Notification toast */
        .notification-toast {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 12px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.3);
            transform: translateX(120%);
            transition: transform 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .notification-toast.show {
            transform: translateX(0);
        }
        
        .notification-toast.success {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        }
        
        .notification-toast.error {
            background: linear-gradient(135deg, #eb3349 0%, #f45c43 100%);
        }
        
        .notification-toast.info {
            background: linear-gradient(135deg, #2193b0 0%, #6dd5ed 100%);
        }
        
        .notification-toast-icon {
            font-size: 24px;
            flex-shrink: 0;
        }
        
        .notification-toast-content {
            flex: 1;
        }
        
        .notification-toast-title {
            font-weight: bold;
            font-size: 14px;
            margin-bottom: 4px;
        }
        
        .notification-toast-message {
            font-size: 13px;
            opacity: 0.95;
        }
        
        .notification-toast-close {
            background: none;
            border: none;
            color: white;
            font-size: 20px;
            cursor: pointer;
            opacity: 0.7;
            padding: 0;
            line-height: 1;
        }
        
        .notification-toast-close:hover {
            opacity: 1;
        }
        
        /* Indicateur de notification en temps réel */
        .live-indicator {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            color: #38ef7d;
            margin-left: 10px;
        }
        
        .live-dot {
            width: 8px;
            height: 8px;
            background: #38ef7d;
            border-radius: 50%;
            animation: blink 1.5s infinite;
        }
        
        @keyframes blink {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.4; }
        }
    </style>
</head>
<body>
<!-- Container des notifications toast -->
<div id="notifications-container"></div>

<header>
    <nav class="container flex-between">
        <a href="<?php echo BASE_URL; ?>/index.php" class="logo">Impact Emploi</a>
        <button id="navToggle" class="mobile-menu-btn" aria-label="Menu" aria-expanded="false">
            <span class="menu-icon"></span>
        </button>
        <div class="nav-links" id="navLinks">
            <button class="close-menu-btn" id="closeMenuBtn" aria-label="Fermer le menu">✕</button>
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
                <!-- Indicateur temps réel -->
                <span class="live-indicator">
                    <span class="live-dot"></span>
                    Temps réel
                </span>
                
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
                    <a href="<?php echo BASE_URL; ?>/recruteur_dashboard.php" class="notification-badge" style="background: rgba(255,255,255,0.2); padding: 8px 16px; border-radius: 6px;" data-count="0" id="notifications-link">👥 Candidatures <span class="notif-count">0</span></a>
                <?php elseif($_SESSION['auth_role'] === 'candidat'): ?>
                    <a href="<?php echo BASE_URL; ?>/candidat_dashboard.php" class="notification-badge" style="background: rgba(255,255,255,0.2); padding: 8px 16px; border-radius: 6px;" data-count="0" id="notifications-link">📋 Mes Candidatures <span class="notif-count">0</span></a>
                <?php endif; ?>
                
                <a href="<?php echo BASE_URL; ?>/" style="background: rgba(255,255,255,0.2); padding: 8px 16px; border-radius: 6px;" title="Accueil">🏠 Accueil</a>
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
<!-- Script pour les notifications temps réel -->
<?php if(isset($_SESSION['auth_id'])): ?>
<script>
// Système de notifications temps réel inline
(function() {
    let lastCount = 0;
    let checkInterval = null;
    
    // Vérifier les notifications
    async function checkNotifications() {
        try {
            const response = await fetch('<?php echo BASE_URL; ?>/ajax_notifications.php', {
                method: 'GET',
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const data = await response.json();
            
            if (data.success && data.count > 0) {
                // Mettre à jour le compteur
                updateNotificationCount(data.count);
                
                // Afficher une notification toast si nouveau
                if (data.count > lastCount && lastCount > 0) {
                    showNotificationToast('🔔', 'Nouvelle notification', 'Vous avez ' + data.count + ' nouvelle(s) notification(s)');
                    playNotificationSound();
                }
                
                lastCount = data.count;
            } else {
                updateNotificationCount(0);
            }
        } catch(e) {
            console.log('Erreur notification:', e);
        }
    }
    
    // Mettre à jour le compteur dans le header
    function updateNotificationCount(count) {
        const badge = document.getElementById('notifications-link');
        if (badge) {
            const countSpan = badge.querySelector('.notif-count');
            if (countSpan) {
                countSpan.textContent = '(' + count + ')';
            }
            if (count > 0) {
                badge.classList.add('has-notifications');
                badge.setAttribute('data-count', count);
            } else {
                badge.classList.remove('has-notifications');
                badge.setAttribute('data-count', '0');
            }
        }
    }
    
    // Afficher une notification toast
    function showNotificationToast(icon, title, message) {
        const container = document.getElementById('notifications-container');
        const toast = document.createElement('div');
        toast.className = 'notification-toast info';
        toast.innerHTML = `
            <span class="notification-toast-icon">${icon}</span>
            <div class="notification-toast-content">
                <div class="notification-toast-title">${title}</div>
                <div class="notification-toast-message">${message}</div>
            </div>
            <button class="notification-toast-close" onclick="this.parentElement.remove()">×</button>
        `;
        
        container.appendChild(toast);
        
        // Animation d'entrée
        setTimeout(() => toast.classList.add('show'), 10);
        
        // Auto-suppression
        setTimeout(() => {
            toast.classList.remove('show');
            setTimeout(() => toast.remove(), 400);
        }, 8000);
    }
    
    // Son de notification
    function playNotificationSound() {
        try {
            const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
            const oscillator = audioCtx.createOscillator();
            const gainNode = audioCtx.createGain();
            
            oscillator.connect(gainNode);
            gainNode.connect(audioCtx.destination);
            
            oscillator.frequency.setValueAtTime(800, audioCtx.currentTime);
            oscillator.frequency.setValueAtTime(1200, audioCtx.currentTime + 0.1);
            oscillator.frequency.setValueAtTime(800, audioCtx.currentTime + 0.2);
            
            gainNode.gain.setValueAtTime(0.3, audioCtx.currentTime);
            gainNode.gain.exponentialRampToValueAtTime(0.01, audioCtx.currentTime + 0.5);
            
            oscillator.start(audioCtx.currentTime);
            oscillator.stop(audioCtx.currentTime + 0.5);
            
            // Vibration si supporté
            if ('vibrate' in navigator) {
                navigator.vibrate([200, 100, 200]);
            }
        } catch(e) {}
    }
    
    // Démarrer la vérification toutes les 5 secondes
    checkInterval = setInterval(checkNotifications, 5000);
    
    // Vérification immédiate
    checkNotifications();
})();
</script>
<?php endif; ?>
