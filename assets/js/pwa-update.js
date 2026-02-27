/**
 * PWA Update Notifier - Impact Emploi
 * Affiche une notification quand une mise à jour est disponible
 * Se répète quotidiennement si l'utilisateur ne met pas à jour
 */

(function() {
    'use strict';
    
    // Clés pour le stockage local
    const LAST_UPDATE_CHECK = 'pwa_last_update_check';
    const LAST_UPDATE_REMINDER = 'pwa_last_update_reminder';
    const UPDATE_DAY_INTERVAL = 1; // Jours entre les rappels
    
    // Créer la notification de mise à jour
    function createUpdateBanner() {
        // Vérifier si la banner existe déjà
        if (document.getElementById('pwa-update-banner')) {
            return;
        }
        
        const banner = document.createElement('div');
        banner.id = 'pwa-update-banner';
        banner.innerHTML = `
            <style>
                #pwa-update-banner {
                    position: fixed;
                    bottom: 0;
                    left: 0;
                    right: 0;
                    background: linear-gradient(135deg, #0052A3 0%, #004080 100%);
                    color: white;
                    padding: 16px 20px;
                    display: flex;
                    align-items: center;
                    justify-content: space-between;
                    box-shadow: 0 -4px 20px rgba(0,0,0,0.3);
                    z-index: 99999;
                    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
                    animation: slideUp 0.3s ease-out;
                }
                @keyframes slideUp {
                    from { transform: translateY(100%); }
                    to { transform: translateY(0); }
                }
                .pwa-update-content {
                    display: flex;
                    align-items: center;
                    gap: 12px;
                    flex: 1;
                }
                .pwa-update-icon {
                    font-size: 24px;
                }
                .pwa-update-text {
                    flex: 1;
                }
                .pwa-update-title {
                    font-weight: 600;
                    font-size: 15px;
                    margin-bottom: 2px;
                }
                .pwa-update-desc {
                    font-size: 13px;
                    opacity: 0.9;
                }
                .pwa-update-actions {
                    display: flex;
                    gap: 10px;
                }
                .pwa-update-btn {
                    padding: 10px 18px;
                    border: none;
                    border-radius: 8px;
                    font-size: 14px;
                    font-weight: 600;
                    cursor: pointer;
                    transition: all 0.2s;
                }
                .pwa-update-btn-primary {
                    background: #fff;
                    color: #0052A3;
                }
                .pwa-update-btn-primary:hover {
                    transform: scale(1.05);
                }
                .pwa-update-btn-secondary {
                    background: rgba(255,255,255,0.2);
                    color: white;
                }
                .pwa-update-btn-secondary:hover {
                    background: rgba(255,255,255,0.3);
                }
                @media (max-width: 480px) {
                    #pwa-update-banner {
                        flex-direction: column;
                        gap: 12px;
                        text-align: center;
                    }
                    .pwa-update-actions {
                        width: 100%;
                        justify-content: center;
                    }
                }
            </style>
            <div class="pwa-update-content">
                <span class="pwa-update-icon">🔄</span>
                <div class="pwa-update-text">
                    <div class="pwa-update-title">Nouvelle version disponible !</div>
                    <div class="pwa-update-desc">Une mise à jour du site est prête. Rechargez pour bénéficier des dernières améliorations.</div>
                </div>
            </div>
            <div class="pwa-update-actions">
                <button class="pwa-update-btn pwa-update-btn-secondary" id="pwa-update-later">Plus tard</button>
                <button class="pwa-update-btn pwa-update-btn-primary" id="pwa-update-now">Recharger</button>
            </div>
        `;
        
        document.body.appendChild(banner);
        
        // Ajouter les événements
        document.getElementById('pwa-update-now').addEventListener('click', function() {
            // Mettre à jour la date du dernier rappel
            localStorage.setItem(LAST_UPDATE_REMINDER, Date.now());
            // Recharger la page
            window.location.reload();
        });
        
        document.getElementById('pwa-update-later').addEventListener('click', function() {
            // Enregistrer le rappel pour demain
            localStorage.setItem(LAST_UPDATE_REMINDER, Date.now());
            // Supprimer la banner
            removeUpdateBanner();
        });
    }
    
    // Supprimer la banner de mise à jour
    function removeUpdateBanner() {
        const banner = document.getElementById('pwa-update-banner');
        if (banner) {
            banner.style.animation = 'slideDown 0.3s ease-out';
            setTimeout(() => banner.remove(), 300);
        }
    }
    
    // Vérifier si on doit afficher le rappel
    function shouldShowReminder() {
        const lastReminder = localStorage.getItem(LAST_UPDATE_REMINDER);
        
        if (!lastReminder) {
            // Pas encore de rappel, afficher
            return true;
        }
        
        const daysSinceReminder = (Date.now() - parseInt(lastReminder)) / (1000 * 60 * 60 * 24);
        return daysSinceReminder >= UPDATE_DAY_INTERVAL;
    }
    
    // Écouter les messages du Service Worker
    if ('serviceWorker' in navigator && navigator.serviceWorker.controller) {
        navigator.serviceWorker.addEventListener('message', function(event) {
            console.log('[PWA Update] Message received:', event.data);
            
            if (event.data && event.data.type === 'UPDATE_CHECK') {
                // Enregistrer le timestamp
                localStorage.setItem(LAST_UPDATE_CHECK, event.data.timestamp);
                
                // Afficher la notification de mise à jour si assez de temps s'est écoulé
                if (shouldShowReminder()) {
                    createUpdateBanner();
                }
            }
        });
    }
    
    // Fonction pour vérifier manuellement les mises à jour (exportée globalement)
    window.checkForPWAUpdate = function() {
        if ('serviceWorker' in navigator && navigator.serviceWorker.controller) {
            navigator.serviceWorker.controller.postMessage({
                type: 'CHECK_UPDATE'
            });
        }
    };
    
    // Au chargement de la page, vérifier si on doit afficher un rappel
    document.addEventListener('DOMContentLoaded', function() {
        // Petit délai pour laisser le temps au Service Worker de démarrer
        setTimeout(function() {
            if (shouldShowReminder()) {
                // Demander une vérification de mise à jour
                window.checkForPWAUpdate();
            }
        }, 3000);
    });
    
    // Émettre un événement personnalisé pour signaler que le script est prêt
    window.dispatchEvent(new Event('pwaUpdateReady'));
})();

