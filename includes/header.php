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
        
        /* Styles pour la cloche de notifications */
        .notification-bell {
            cursor: pointer;
            transition: transform 0.2s ease;
        }
        
        .notification-bell:hover {
            transform: scale(1.1);
        }
        
        .notification-badge-dot {
            position: absolute;
            top: -2px;
            right: -2px;
            background: #ff4757;
            color: white;
            border-radius: 50%;
            padding: 2px 5px;
            font-size: 10px;
            font-weight: bold;
            min-width: 16px;
            text-align: center;
            animation: pulse 1.5s infinite;
        }
        
        /* Dropdown des notifications */
        .notifications-dropdown {
            position: absolute;
            top: 60px;
            right: 20px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            width: 350px;
            max-height: 450px;
            overflow: hidden;
            z-index: 9999;
            animation: slideDown 0.3s ease;
        }
        
        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .notifications-dropdown .dropdown-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 20px;
            background: linear-gradient(135deg, #0052A3 0%, #0066CC 100%);
            color: white;
        }
        
        .notifications-dropdown .dropdown-list {
            max-height: 350px;
            overflow-y: auto;
        }
        
        .notification-item {
            padding: 15px 20px;
            border-bottom: 1px solid #f0f0f0;
            cursor: pointer;
            transition: background 0.2s ease;
        }
        
        .notification-item:hover {
            background: #f8f9fa;
        }
        
        .notification-item.unread {
            background: #e3f2fd;
            border-left: 3px solid #0052A3;
        }
        
        .notification-item-title {
            font-weight: 600;
            color: #333;
            margin-bottom: 5px;
            font-size: 14px;
        }
        
        .notification-item-message {
            color: #666;
            font-size: 13px;
            margin-bottom: 5px;
        }
        
        .notification-item-time {
            color: #999;
            font-size: 11px;
        }
        
        .notification-empty {
            padding: 40px 20px;
            text-align: center;
            color: #999;
        }
        
        .notification-empty-icon {
            font-size: 3rem;
            margin-bottom: 10px;
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
                
                <!-- Zone utilisateur groupée (PC) -->
                <div class="user-section">
                <!-- Avatar cliquable seul (renvoie vers profil) -->
                <?php 
                $avatar_to_show = $avatar ?? '';
                if(!empty($avatar)) {
                    $avatar_path = __DIR__ . '/../uploads/profiles/' . htmlspecialchars($avatar);
                    if(!file_exists($avatar_path)) {
                        $avatar_to_show = '';
                    }
                }
                ?>
                <?php if(!empty($avatar_to_show)): ?>
                    <a href="<?php echo BASE_URL; ?>/profil.php" class="avatar-link">
                        <img src="<?php echo BASE_URL; ?>/uploads/profiles/<?php echo htmlspecialchars($avatar_to_show); ?>" 
                             alt="avatar" 
                             class="nav-avatar"
                             loading="lazy" 
                             decoding="async"
                             onerror="this.onerror=null; this.src='<?php echo BASE_URL; ?>/default-avatar.php';">
                        <span class="user-name"><?php echo htmlspecialchars($_SESSION['auth_nom']); ?></span></a>
                <?php else: ?>
                    <a href="<?php echo BASE_URL; ?>/profil.php" class="avatar-link">
                        <img src="<?php echo BASE_URL; ?>/default-avatar.php" 
                             alt="avatar par défaut" 
                             class="nav-avatar">
                        <span class="user-name"><?php echo htmlspecialchars($_SESSION['auth_nom']); ?></span></a>
                <?php endif; ?>
                    
                    <?php if($_SESSION['auth_role'] === 'admin'): ?>
                        <a href="<?php echo BASE_URL; ?>/admin_dashboard.php" class="nav-link nav-link-primary">📊 Tableau de Bord</a>
                        <a href="<?php echo BASE_URL; ?>/sante.php" class="nav-link">✅ Santé</a>
                        
                        <!-- Cloche de notifications -->
                        <a href="#" id="notificationBell" class="notification-bell" onclick="showNotificationsDropdown(event)">
                            <span class="bell-icon">🔔</span>
                            <span class="notification-badge-dot" id="notificationBadge" style="display: none;">0</span>
                        </a>
                        
                        <!-- Dropdown des notifications -->
                        <div id="notificationsDropdown" class="notifications-dropdown" style="display: none;">
                            <div class="dropdown-header">
                                <span style="font-weight: bold;">Notifications</span>
                                <button onclick="closeNotificationsDropdown(event)" style="background: none; border: none; cursor: pointer; font-size: 1.2rem;">✕</button>
                            </div>
                            <div id="notificationsList" class="dropdown-list">
                                <div style="padding: 20px; text-align: center; color: #999;">Chargement...</div>
                            </div>
                        </div>
                        
                    <?php elseif($_SESSION['auth_role'] === 'recruteur'): ?>
                        <a href="<?php echo BASE_URL; ?>/recruteur_dashboard.php" class="nav-link notification-badge" data-count="0" id="notifications-link">👥 Candidatures <span class="notif-count">0</span></a>
                        
                        <!-- Cloche de notifications -->
                        <a href="#" id="notificationBell" class="notification-bell" onclick="showNotificationsDropdown(event)">
                            <span class="bell-icon">🔔</span>
                            <span class="notification-badge-dot" id="notificationBadge" style="display: none;">0</span>
                        </a>
                        
                        <!-- Dropdown des notifications -->
                        <div id="notificationsDropdown" class="notifications-dropdown" style="display: none;">
                            <div class="dropdown-header">
                                <span style="font-weight: bold;">Notifications</span>
                                <button onclick="closeNotificationsDropdown(event)" style="background: none; border: none; cursor: pointer; font-size: 1.2rem;">✕</button>
                            </div>
                            <div id="notificationsList" class="dropdown-list">
                                <div style="padding: 20px; text-align: center; color: #999;">Chargement...</div>
                            </div>
                        </div>
                        
                    <?php elseif($_SESSION['auth_role'] === 'candidat'): ?>
                        <a href="<?php echo BASE_URL; ?>/candidat_dashboard.php" class="nav-link notification-badge" data-count="0" id="notifications-link">📋 Mes Candidatures <span class="notif-count">0</span></a>
                        
                        <!-- Cloche de notifications -->
                        <a href="#" id="notificationBell" class="notification-bell" onclick="showNotificationsDropdown(event)">
                            <span class="bell-icon">🔔</span>
                            <span class="notification-badge-dot" id="notificationBadge" style="display: none;">0</span>
                        </a>
                        
                        <!-- Dropdown des notifications -->
                        <div id="notificationsDropdown" class="notifications-dropdown" style="display: none;">
                            <div class="dropdown-header">
                                <span style="font-weight: bold;">Notifications</span>
                                <button onclick="closeNotificationsDropdown(event)" style="background: none; border: none; cursor: pointer; font-size: 1.2rem;">✕</button>
                            </div>
                            <div id="notificationsList" class="dropdown-list">
                                <div style="padding: 20px; text-align: center; color: #999;">Chargement...</div>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Bouton Plus (Dropdown) -->
                    <div class="more-dropdown">
                        <button class="nav-link nav-more-btn" id="moreBtn" onclick="toggleMoreMenu(event)">
                            <span>➕ Plus</span>
                            <span class="dropdown-arrow">▼</span>
                        </button>
                        <div class="more-menu" id="moreMenu">
                            <a href="<?php echo BASE_URL; ?>/index.php" class="more-link">🏠 Accueil</a>
                            <a href="<?php echo BASE_URL; ?>/aide.php" class="more-link">❓ Aide</a>
                            <a href="<?php echo BASE_URL; ?>/resources.php" class="more-link">📚 Ressources</a>
                            <a href="<?php echo BASE_URL; ?>/feedback.php" class="more-link">💬 Feedback</a>
                            <a href="<?php echo BASE_URL; ?>/profil.php" class="more-link">👤 Mon Profil</a>
                        </div>
                    </div>
                    
                    <!-- Bouton Déconnexion (toujours visible) -->
                    <a href="<?php echo BASE_URL; ?>/logout.php" class="btn-logout">🚪 Déconnexion</a>
                </div>
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
// Toggle Menu Plus - avec vérification de sécurité
function toggleMoreMenu(event) {
    if (event) {
        event.preventDefault();
        event.stopPropagation();
    }
    
    const btn = document.getElementById('moreBtn');
    const menu = document.getElementById('moreMenu');
    
    if (!btn || !menu) return;
    
    if (menu.classList.contains('show')) {
        menu.classList.remove('show');
        btn.classList.remove('active');
    } else {
        menu.classList.add('show');
        btn.classList.add('active');
    }
    
    // Fermer en clic outside
    setTimeout(() => {
        document.addEventListener('click', closeMoreMenuOutside);
    }, 100);
}

function closeMoreMenuOutside(event) {
    const menu = document.getElementById('moreMenu');
    const btn = document.getElementById('moreBtn');
    if (menu && !menu.contains(event.target) && btn && !btn.contains(event.target)) {
        menu.classList.remove('show');
        if (btn) btn.classList.remove('active');
        document.removeEventListener('click', closeMoreMenuOutside);
    }
}

// Système de notifications temps réel inline
(function() {
    let lastCount = 0;
    let checkInterval = null;
    let notificationsViewed = false;
    
    // Vérifier les notifications - Sans afficher d'erreurs
    async function checkNotifications() {
        try {
            const response = await fetch('<?php echo BASE_URL; ?>/ajax_notifications.php', {
                method: 'GET',
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const data = await response.json();
            
            if (data.success && data.count > 0) {
                // Mettre à jour le compteur de la cloche
                updateBellBadge(data.count);
                
                // Afficher une notification toast si nouveau
                if (data.count > lastCount && lastCount > 0) {
                    showNotificationToast('🔔', 'Nouvelle notification', 'Vous avez ' + data.count + ' nouvelle(s) notification(s)');
                    playNotificationSound();
                }
                
                lastCount = data.count;
            } else {
                updateBellBadge(0);
            }
        } catch(e) {
            // Silencieux - pas d'erreur pour l'utilisateur
            updateBellBadge(0);
        }
    }
    
    // Mettre à jour le badge de la cloche
    function updateBellBadge(count) {
        const badge = document.getElementById('notificationBadge');
        const bell = document.getElementById('notificationBell');
        
        if (badge && bell) {
            if (count > 0) {
                badge.style.display = 'block';
                badge.textContent = count > 99 ? '99+' : count;
                
                // Animation de la cloche
                bell.style.animation = 'bellRing 0.5s ease';
                setTimeout(() => bell.style.animation = '', 500);
            } else {
                badge.style.display = 'none';
            }
        }
        
        // Mettre à jour aussi le lien des candidatures
        const badgeLink = document.getElementById('notifications-link');
        if (badgeLink) {
            const countSpan = badgeLink.querySelector('.notif-count');
            if (countSpan) {
                countSpan.textContent = '(' + count + ')';
            }
            if (count > 0) {
                badgeLink.classList.add('has-notifications');
                badgeLink.setAttribute('data-count', count);
            } else {
                badgeLink.classList.remove('has-notifications');
                badgeLink.setAttribute('data-count', '0');
            }
        }
    }
    
    // Afficher le dropdown des notifications
    window.showNotificationsDropdown = async function(event) {
        if (event) {
            event.preventDefault();
            event.stopPropagation();
        }
        
        const dropdown = document.getElementById('notificationsDropdown');
        const list = document.getElementById('notificationsList');
        
        if (!dropdown || !list) return;
        
        if (dropdown.style.display === 'block') {
            dropdown.style.display = 'none';
            return;
        }
        
        // Charger les notifications - Sans erreur affichée
        try {
            const response = await fetch('<?php echo BASE_URL; ?>/ajax_notifications.php', {
                method: 'GET',
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const data = await response.json();
            
            if (data.success && data.notifications && data.notifications.length > 0) {
                let html = '';
                data.notifications.forEach(function(notif) {
                    const title = notif.title || notif.titre || 'Notification';
                    const message = notif.message || notif.recruteur_message || '';
                    const type = notif.type || 'info';
                    const time = notif.created_at || notif.date_postulation || '';
                    
                    // Formater le temps
                    let timeAgo = '';
                    if (time) {
                        const date = new Date(time);
                        const now = new Date();
                        const diff = Math.floor((now - date) / 1000);
                        
                        if (diff < 60) timeAgo = 'À l\'instant';
                        else if (diff < 3600) timeAgo = 'Il y a ' + Math.floor(diff/60) + ' min';
                        else if (diff < 86400) timeAgo = 'Il y a ' + Math.floor(diff/3600) + ' h';
                        else timeAgo = 'Il y a ' + Math.floor(diff/86400) + ' j';
                    }
                    
                    // Icône selon le type
                    let icon = '🔔';
                    if (type === 'new_candidature') icon = '📨';
                    else if (type === 'candidature_response') icon = '💬';
                    else if (type === 'accepted') icon = '✅';
                    else if (type === 'refused') icon = '❌';
                    
                    html += `
                        <div class="notification-item" onclick="handleNotificationClick('${type}', ${notif.id || 0})">
                            <div class="notification-item-title">${icon} ${escapeHtml(title)}</div>
                            <div class="notification-item-message">${escapeHtml(message.substring(0, 100))}${message.length > 100 ? '...' : ''}</div>
                            <div class="notification-item-time">${timeAgo}</div>
                        </div>
                    `;
                });
                list.innerHTML = html;
            } else {
                list.innerHTML = `
                    <div class="notification-empty">
                        <div class="notification-empty-icon">🔕</div>
                        <p>Aucune notification</p>
                    </div>
                `;
            }
            
            // Marquer les notifications comme vues
            notificationsViewed = true;
            updateBellBadge(0);
            
        } catch(e) {
            // Pas d'erreur affichée - montrer "Aucune notification"
            list.innerHTML = `
                <div class="notification-empty">
                    <div class="notification-empty-icon">🔕</div>
                    <p>Aucune notification</p>
                </div>
            `;
        }
        
        dropdown.style.display = 'block';
        
        // Fermer en clic outside
        setTimeout(() => {
            document.addEventListener('click', closeDropdownOutside);
        }, 100);
    };
    
    // Fermer le dropdown
    window.closeNotificationsDropdown = function(event) {
        if (event) {
            event.preventDefault();
            event.stopPropagation();
        }
        const dropdown = document.getElementById('notificationsDropdown');
        if (dropdown) {
            dropdown.style.display = 'none';
        }
        document.removeEventListener('click', closeDropdownOutside);
    };
    
    // Fermer en cliquant à l'extérieur
    function closeDropdownOutside(event) {
        const dropdown = document.getElementById('notificationsDropdown');
        const bell = document.getElementById('notificationBell');
        if (dropdown && !dropdown.contains(event.target) && bell && !bell.contains(event.target)) {
            dropdown.style.display = 'none';
            document.removeEventListener('click', closeDropdownOutside);
        }
    }
    
    // Gérer le clic sur une notification
    window.handleNotificationClick = function(type, id) {
        // Rediriger selon le type
        if (type === 'new_candidature' || type === 'candidature_response') {
            window.location.href = '<?php echo BASE_URL; ?>/candidat_dashboard.php';
        } else if (type === 'accepted' || type === 'refused') {
            window.location.href = '<?php echo BASE_URL; ?>/candidat_dashboard.php';
        }
        closeNotificationsDropdown();
    };
    
    // Échapper le HTML
    function escapeHtml(text) {
        if (!text) return '';
        return text
            .replace(/&/g, '&amp;')
            .replace(/</g, '<')
            .replace(/>/g, '>')
            .replace(/"/g, '"')
            .replace(/'/g, '&#039;');
    }
    
    // Afficher une notification toast
    function showNotificationToast(icon, title, message) {
        const container = document.getElementById('notifications-container');
        if (!container) return;
        
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
    
    // Animation de la cloche
    const style = document.createElement('style');
    style.textContent = `
        @keyframes bellRing {
            0%, 100% { transform: rotate(0); }
            25% { transform: rotate(15deg); }
            75% { transform: rotate(-15deg); }
        }
    `;
    document.head.appendChild(style);
    
    // Démarrer la vérification toutes les 3 secondes (plus rapide)
    checkInterval = setInterval(checkNotifications, 3000);
    
    // Vérification immédiate
    checkNotifications();
})();
</script>
<?php endif; ?>
