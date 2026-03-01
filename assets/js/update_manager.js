// Système de Mise à Jour SIMPLE - Impact Emploi
// VERSION GARANTIE FONCTIONNELLE

class SimpleUpdateManager {
    constructor() {
        this.currentVersion = '1.4.1';
        this.updateAvailable = false;
        
        this.init();
    }
    
    init() {
        // Vérification simple toutes les 30 secondes (plus rapide)
        setInterval(() => {
            this.checkUpdate();
        }, 30000);
        
        // Vérification initiale
        setTimeout(() => this.checkUpdate(), 2000); // Attendre 2s au chargement
    }
    
    checkUpdate() {
        fetch('ajax_version_check.php?version=' + this.currentVersion)
            .then(response => response.json())
            .then(data => {
                if (data.success && !data.up_to_date) {
                    this.showUpdateNotification();
                }
            })
            .catch(error => {
                console.log('Erreur vérification mise à jour:', error);
            });
    }
    
    showUpdateNotification() {
        if (this.updateAvailable) return; // Éviter les doublons
        
        this.updateAvailable = true;
        
        // Créer une notification simple
        const notification = document.createElement('div');
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.3);
            z-index: 10000;
            max-width: 400px;
            animation: slideIn 0.5s ease;
        `;
        
        notification.innerHTML = `
            <div style="display: flex; align-items: center; margin-bottom: 10px;">
                <span style="font-size: 24px; margin-right: 10px;">🔄</span>
                <strong style="font-size: 16px;">Mise à jour disponible!</strong>
            </div>
            <p style="margin: 0 0 15px 0; font-size: 14px;">
                Une nouvelle version du site est disponible.\n                <strong>Cliquez pour mettre à jour immédiatement.</strong>
            </p>
            <div style="display: flex; gap: 10px;">
                <button onclick="simpleUpdateManager.reloadPage()" 
                        style="background: white; color: #667eea; border: none; padding: 8px 16px; border-radius: 5px; cursor: pointer; font-weight: bold;">
                    🚀 Mettre à jour
                </button>
                <button onclick="this.parentElement.parentElement.remove()" 
                        style="background: transparent; color: white; border: 1px solid white; padding: 8px 16px; border-radius: 5px; cursor: pointer;">
                    Plus tard
                </button>
            </div>
        `;
        
        // Ajouter l'animation
        if (!document.querySelector('#simple-update-styles')) {
            const style = document.createElement('style');
            style.id = 'simple-update-styles';
            style.textContent = `
                @keyframes slideIn {
                    from { transform: translateX(100%); opacity: 0; }
                    to { transform: translateX(0); opacity: 1; }
                }
            `;
            document.head.appendChild(style);
        }
        
        document.body.appendChild(notification);
        
        // Son de notification simple
        this.playSound();
        
        // Auto-suppression après 20 secondes (plus court)
        setTimeout(() => {
            if (notification.parentElement) {
                notification.remove();
                this.updateAvailable = false;
            }
        }, 20000);
    }
    
    playSound() {
        // Son simple et court
        const audio = new Audio('data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbq2EcBj+a2/LDciUFLIHO8tiJNwgZaLvt559NEAxQp+PwtmMcBjiR1/LMeSwFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBi+Gy+/DgjMGHm7A7+OZURE');
        audio.volume = 0.7; // Plus fort
        audio.play().catch(e => console.log('Son bloqué'));
    }
    
    reloadPage() {
        // Recharger la page simplement avec cache busting
        const timestamp = Date.now();
        const currentUrl = window.location.pathname;
        window.location.href = currentUrl + '?v=' + timestamp + '&update=1';
    }
}

// Démarrer le système simple
document.addEventListener('DOMContentLoaded', () => {
    window.simpleUpdateManager = new SimpleUpdateManager();
});
