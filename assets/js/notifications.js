// Système de Notifications Temps Réel - Impact Emploi

class NotificationSystem {
    constructor() {
        this.userId = null;
        this.userRole = null;
        this.notificationSound = null;
        this.checkInterval = null;
        this.isTabVisible = true;
        this.unreadCount = 0;
        
        this.init();
    }
    
    init() {
        // Charger le son de notification (très fort)
        this.loadNotificationSound();
        
        // Détecter la visibilité de l'onglet
        document.addEventListener('visibilitychange', () => {
            this.isTabVisible = !document.hidden;
            if (this.isTabVisible) {
                this.checkNotifications(); // Vérifier au retour sur l'onglet
            }
        });
        
        // Initialiser le système
        this.startNotificationSystem();
    }
    
    loadNotificationSound() {
        // Créer un son de notification puissant avec Web Audio API
        this.audioContext = new (window.AudioContext || window.webkitAudioContext)();
        
        // Son de notification très fort et perçant
        this.notificationSound = () => {
            const oscillator = this.audioContext.createOscillator();
            const gainNode = this.audioContext.createGain();
            
            oscillator.connect(gainNode);
            gainNode.connect(this.audioContext.destination);
            
            // Fréquences très perçantes et fortes
            oscillator.frequency.setValueAtTime(800, this.audioContext.currentTime);
            oscillator.frequency.setValueAtTime(1200, this.audioContext.currentTime + 0.1);
            oscillator.frequency.setValueAtTime(800, this.audioContext.currentTime + 0.2);
            
            // Volume très élevé
            gainNode.gain.setValueAtTime(0.8, this.audioContext.currentTime);
            gainNode.gain.exponentialRampToValueAtTime(0.01, this.audioContext.currentTime + 0.5);
            
            oscillator.start(this.audioContext.currentTime);
            oscillator.stop(this.audioContext.currentTime + 0.5);
            
            // Double son pour plus d'impact
            setTimeout(() => {
                const osc2 = this.audioContext.createOscillator();
                const gain2 = this.audioContext.createGain();
                
                osc2.connect(gain2);
                gain2.connect(this.audioContext.destination);
                
                osc2.frequency.setValueAtTime(1000, this.audioContext.currentTime);
                osc2.frequency.setValueAtTime(1500, this.audioContext.currentTime + 0.05);
                
                gain2.gain.setValueAtTime(0.9, this.audioContext.currentTime);
                gain2.gain.exponentialRampToValueAtTime(0.01, this.audioContext.currentTime + 0.3);
                
                osc2.start(this.audioContext.currentTime);
                osc2.stop(this.audioContext.currentTime + 0.3);
            }, 200);
        };
    }
    
    startNotificationSystem() {
        // Vérifier les notifications toutes les 3 secondes
        this.checkInterval = setInterval(() => {
            if (this.isTabVisible) {
                this.checkNotifications();
            }
        }, 3000);
        
        // Vérification initiale
        this.checkNotifications();
    }
    
    async checkNotifications() {
        try {
            const response = await fetch('ajax_notifications.php', {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            
            const data = await response.json();
            
            if (data.success && data.notifications.length > 0) {
                this.handleNewNotifications(data.notifications);
            }
            
            // Mettre à jour le compteur
            this.updateNotificationCount(data.count || 0);
            
        } catch (error) {
            console.error('Erreur de notification:', error);
        }
    }
    
    handleNewNotifications(notifications) {
        notifications.forEach(notification => {
            this.showNotification(notification);
            this.playNotificationSound();
        });
    }
    
    playNotificationSound() {
        if (this.notificationSound && this.audioContext) {
            // Réactiver l'AudioContext si nécessaire (Chrome)
            if (this.audioContext.state === 'suspended') {
                this.audioContext.resume();
            }
            
            this.notificationSound();
            
            // Faire vibrer le téléphone si supporté
            if ('vibrate' in navigator) {
                navigator.vibrate([200, 100, 200, 100, 200]);
            }
        }
    }
    
    showNotification(notification) {
        // Notification browser native
        if ('Notification' in window && Notification.permission === 'granted') {
            const browserNotification = new Notification(notification.title || 'Nouvelle notification', {
                body: notification.message || 'Vous avez une nouvelle notification',
                icon: '/assets/images/favicon.png',
                tag: 'impact-emploi',
                requireInteraction: true
            });
            
            browserNotification.onclick = () => {
                window.focus();
                browserNotification.close();
                this.handleNotificationClick(notification);
            };
            
            // Auto-fermeture après 10 secondes
            setTimeout(() => browserNotification.close(), 10000);
        }
        
        // Notification dans l'interface
        this.showUINotification(notification);
        
        // Animation visuelle forte
        this.showNotificationAnimation();
    }
    
    showUINotification(notification) {
        // Créer la notification dans l'interface
        const notificationEl = document.createElement('div');
        notificationEl.className = 'notification-item';
        notificationEl.innerHTML = `
            <div class="notification-content">
                <h4>${notification.title || 'Nouvelle notification'}</h4>
                <p>${notification.message || 'Vous avez une nouvelle notification'}</p>
                <small>${new Date().toLocaleTimeString()}</small>
            </div>
            <button onclick="this.parentElement.remove()" class="notification-close">×</button>
        `;
        
        // Ajouter au conteneur de notifications
        let container = document.getElementById('notifications-container');
        if (!container) {
            container = document.createElement('div');
            container.id = 'notifications-container';
            container.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                z-index: 10000;
                max-width: 400px;
            `;
            document.body.appendChild(container);
        }
        
        container.appendChild(notificationEl);
        
        // Auto-suppression après 8 secondes
        setTimeout(() => {
            if (notificationEl.parentElement) {
                notificationEl.remove();
            }
        }, 8000);
        
        // Animation d'entrée
        notificationEl.style.cssText = `
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 10px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.3);
            transform: translateX(100%);
            transition: transform 0.3s ease;
            cursor: pointer;
        `;
        
        setTimeout(() => {
            notificationEl.style.transform = 'translateX(0)';
        }, 100);
        
        // Clic sur la notification
        notificationEl.addEventListener('click', () => {
            this.handleNotificationClick(notification);
            notificationEl.remove();
        });
    }
    
    showNotificationAnimation() {
        // Animation très visible sur toute la page
        const flash = document.createElement('div');
        flash.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle, rgba(102, 126, 234, 0.3) 0%, transparent 70%);
            z-index: 9999;
            pointer-events: none;
            animation: notificationFlash 1s ease-out;
        `;
        
        // Ajouter l'animation CSS si elle n'existe pas
        if (!document.querySelector('#notification-flash-style')) {
            const style = document.createElement('style');
            style.id = 'notification-flash-style';
            style.textContent = `
                @keyframes notificationFlash {
                    0% { opacity: 0; transform: scale(0.8); }
                    50% { opacity: 1; transform: scale(1.1); }
                    100% { opacity: 0; transform: scale(1); }
                }
            `;
            document.head.appendChild(style);
        }
        
        document.body.appendChild(flash);
        setTimeout(() => flash.remove(), 1000);
    }
    
    updateNotificationCount(count) {
        this.unreadCount = count;
        
        // Mettre à jour le badge dans l'interface
        let badge = document.getElementById('notification-badge');
        if (!badge) {
            badge = document.createElement('span');
            badge.id = 'notification-badge';
            badge.style.cssText = `
                background: #ff4757;
                color: white;
                border-radius: 50%;
                padding: 2px 6px;
                font-size: 12px;
                font-weight: bold;
                position: fixed;
                top: 20px;
                right: 20px;
                z-index: 10001;
                min-width: 20px;
                text-align: center;
                box-shadow: 0 2px 10px rgba(255, 71, 87, 0.5);
            `;
            document.body.appendChild(badge);
        }
        
        if (count > 0) {
            badge.textContent = count > 99 ? '99+' : count;
            badge.style.display = 'block';
            badge.style.animation = 'pulse 1s infinite';
        } else {
            badge.style.display = 'none';
        }
        
        // Mettre à jour le titre de la page
        if (count > 0) {
            document.title = `(${count}) Impact Emploi - Trouvez l'emploi de vos rêves`;
        } else {
            document.title = 'Impact Emploi - Trouvez l\'emploi de vos rêves';
        }
    }
    
    handleNotificationClick(notification) {
        // Rediriger vers la page appropriée selon le type de notification
        if (notification.type === 'new_candidature') {
            window.location.href = '/recruteur_dashboard.php';
        } else if (notification.type === 'recruiter_response') {
            window.location.href = '/mon_espace.php';
        }
    }
    
    // Demander la permission pour les notifications browser
    static requestPermission() {
        if ('Notification' in window && Notification.permission === 'default') {
            Notification.requestPermission().then(permission => {
                if (permission === 'granted') {
                    console.log('Notifications autorisées');
                }
            });
        }
    }
    
    // Arrêter le système
    stop() {
        if (this.checkInterval) {
            clearInterval(this.checkInterval);
        }
    }
}

// Démarrer le système quand la page est chargée
document.addEventListener('DOMContentLoaded', () => {
    // Demander la permission dès le chargement
    NotificationSystem.requestPermission();
    
    // Démarrer le système de notifications
    window.notificationSystem = new NotificationSystem();
});

// Nettoyer quand on quitte la page
window.addEventListener('beforeunload', () => {
    if (window.notificationSystem) {
        window.notificationSystem.stop();
    }
});
