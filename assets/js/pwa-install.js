/**
 * PWA Install Handler - Impact Emploi
 * Gère l'installation manuelle du PWA sur mobile
 * Plus fiable que l'installation automatique sur Android Chrome
 */

(function() {
    'use strict';

    // Variables globales
    let deferredPrompt = null;
    let installButton = null;

    // Logging pour debugging mobile
    function logPWA(message, data = null) {
        const prefix = '[PWA Install]';
        if (data) {
            console.log(prefix, message, data);
        } else {
            console.log(prefix, message);
        }
    }

    // Écouter l'événement beforeinstallprompt
    window.addEventListener('beforeinstallprompt', function(e) {
        logPWA('beforeinstallprompt triggered', { userAgent: navigator.userAgent.substring(0, 50) });
        
        // Empêcher l'affichage automatique de la bannière d'installation
        e.preventDefault();
        
        // Stocker l'événement pour pouvoir l'utiliser plus tard
        deferredPrompt = e;
        
        // Afficher le bouton d'installation
        showInstallButton();
    });

    // Écouter quand l'application est installée
    window.addEventListener('appinstalled', function(e) {
        console.log('[PWA Install] App installed successfully');
        
        // Masquer le bouton d'installation
        hideInstallButton();
        
        // Nettoyage
        deferredPrompt = null;
        
        // Afficher un message de succès
        showInstallSuccessMessage();
    });

    // Fonction pour afficher le bouton d'installation
    function showInstallButton() {
        // Ne pas afficher si déjà installé
        if (isAppInstalled()) {
            return;
        }

        // Vérifier si le bouton existe déjà
        installButton = document.getElementById('pwa-install-button');
        if (installButton) {
            installButton.style.display = 'flex';
            return;
        }

        // Créer le bouton d'installation
        installButton = document.createElement('div');
        installButton.id = 'pwa-install-container';
        installButton.innerHTML = `
            <button id="pwa-install-button" class="pwa-install-fab" aria-label="Installer l'application">
                <span class="pwa-install-icon">📲</span>
                <span class="pwa-install-text">Installer</span>
            </button>
        `;

        // Ajouter les styles
        const style = document.createElement('style');
        style.textContent = `
            #pwa-install-container {
                position: fixed;
                bottom: 20px;
                right: 20px;
                z-index: 9999;
                animation: pwaSlideIn 0.4s ease-out;
            }
            
            @keyframes pwaSlideIn {
                from {
                    opacity: 0;
                    transform: translateY(50px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
            
            .pwa-install-fab {
                display: flex;
                align-items: center;
                gap: 10px;
                background: linear-gradient(135deg, #0052A3 0%, #004080 100%);
                color: white;
                border: none;
                padding: 14px 24px;
                border-radius: 50px;
                font-size: 16px;
                font-weight: 600;
                cursor: pointer;
                box-shadow: 0 6px 24px rgba(0, 82, 163, 0.5);
                transition: all 0.3s ease;
                font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            }
            
            .pwa-install-fab:hover {
                transform: translateY(-3px);
                box-shadow: 0 10px 30px rgba(0, 82, 163, 0.6);
            }
            
            .pwa-install-fab:active {
                transform: translateY(0);
            }
            
            .pwa-install-icon {
                font-size: 20px;
            }
            
            .pwa-install-text {
                font-size: 15px;
            }
            
            /* Version compacte pour très petits écrans */
            @media (max-width: 360px) {
                .pwa-install-fab {
                    padding: 12px 20px;
                    font-size: 14px;
                }
                
                .pwa-install-text {
                    display: none;
                }
                
                .pwa-install-icon {
                    font-size: 24px;
                }
            }
            
            /* Masquer sur desktop */
            @media (min-width: 769px) {
                #pwa-install-container {
                    display: none !important;
                }
            }
        `;
        document.head.appendChild(style);
        document.body.appendChild(installButton);

        // Ajouter l'événement de clic
        document.getElementById('pwa-install-button').addEventListener('click', installPWA);
    }

    // Fonction pour masquer le bouton d'installation
    function hideInstallButton() {
        if (installButton) {
            installButton.style.display = 'none';
        }
    }

    // Fonction pour installer le PWA
    async function installPWA() {
        if (!deferredPrompt) {
            console.log('[PWA Install] No deferred prompt available');
            
            // Essayer une autre méthode d'installation
            if (navigator.standalone || window.matchMedia('(display-mode: standalone)').matches) {
                alert('L\'application est déjà installée !');
            } else {
                alert('Pour installer cette application :\n\n1. Ouvrez le menu Chrome (3 points)\n2. Cliquez sur "Installer l\'application" ou "Ajouter à l\'écran d\'accueil"');
            }
            return;
        }

        // Afficher l'invite d'installation
        deferredPrompt.prompt();

        // Attendre la réponse de l'utilisateur
        const { outcome } = await deferredPrompt.userChoice;
        console.log('[PWA Install] User choice:', outcome);

        // Nettoyer
        deferredPrompt = null;

        if (outcome === 'accepted') {
            hideInstallButton();
        }
    }

    // Fonction pour afficher un message de succès
    function showInstallSuccessMessage() {
        const message = document.createElement('div');
        message.id = 'pwa-install-success';
        message.innerHTML = `
            <style>
                #pwa-install-success {
                    position: fixed;
                    top: 20px;
                    left: 50%;
                    transform: translateX(-50%);
                    background: linear-gradient(135deg, #10B981 0%, #059669 100%);
                    color: white;
                    padding: 16px 24px;
                    border-radius: 12px;
                    box-shadow: 0 6px 24px rgba(16, 185, 129, 0.4);
                    z-index: 99999;
                    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
                    font-size: 15px;
                    font-weight: 500;
                    display: flex;
                    align-items: center;
                    gap: 10px;
                    animation: pwaSuccessSlide 0.4s ease-out;
                }
                
                @keyframes pwaSuccessSlide {
                    from {
                        opacity: 0;
                        transform: translateX(-50%) translateY(-20px);
                    }
                    to {
                        opacity: 1;
                        transform: translateX(-50%) translateY(0);
                    }
                }
                
                #pwa-install-success .icon {
                    font-size: 20px;
                }
            </style>
            <span class="icon">✅</span>
            <span>Application installée avec succès !</span>
        `;
        
        document.body.appendChild(message);
        
        // Masquer automatiquement après 4 secondes
        setTimeout(() => {
            message.style.animation = 'pwaSuccessFade 0.3s ease-out forwards';
            setTimeout(() => message.remove(), 300);
        }, 4000);
    }

    // Fonction pour vérifier si l'app est déjà installée
    function isAppInstalled() {
        // Vérifier différentes conditions
        if (navigator.standalone === true) {
            return true;
        }
        
        if (window.matchMedia('(display-mode: standalone)').matches) {
            return true;
        }
        
        if (window.matchMedia('(display-mode: fullscreen)').matches) {
            return true;
        }
        
        if (window.matchMedia('(display-mode: minimal-ui)').matches) {
            return true;
        }
        
        return false;
    }

    // Au chargement de la page
    document.addEventListener('DOMContentLoaded', function() {
        // Petit délai pour laisser le temps au navigateur de déclencher beforeinstallprompt
        setTimeout(function() {
            // Si l'app est déjà installée, ne rien faire
            if (isAppInstalled()) {
                console.log('[PWA Install] App is already installed');
                return;
            }
            
            // Forcer la vérification sur Android
            // Certains navigateurs ne déclenchent pas beforeinstallprompt automatiquement
            checkAndroidInstallability();
        }, 2000);
    });

    // Vérifier manuellement si l'installation est possible
    async function checkAndroidInstallability() {
        // Essayer de détecter si on est sur Android Chrome
        const isAndroid = /Android/i.test(navigator.userAgent);
        const isChrome = /Chrome/i.test(navigator.userAgent) && !/Edge/i.test(navigator.userAgent);
        
        if (isAndroid && isChrome && !deferredPrompt) {
            // Sur Android Chrome, essayer d'afficher le bouton quand même
            // car l'événement beforeinstallprompt peut ne pas se déclencher
            console.log('[PWA Install] Android Chrome detected, showing install button');
            showInstallButton();
        }
    }

    // Exporter des fonctions pour usage externe
    window.PWAInstall = {
        install: installPWA,
        isInstalled: isAppInstalled,
        showButton: showInstallButton,
        hideButton: hideInstallButton
    };

    console.log('[PWA Install] Handler initialized');
})();

