/**
 * UI Components - Notifications & Modals
 * Système réutilisable pour notifications toast et modales de confirmation
 */

// ===== NOTIFICATIONS TOAST =====
// Appel: showNotification("Message", "success|info|warning|error")
function showNotification(message, type = 'info', duration = 3000) {
    const container = document.getElementById('toast-container');
    if (!container) {
        const div = document.createElement('div');
        div.id = 'toast-container';
        document.body.appendChild(div);
    }

    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.innerHTML = `
        <div class="toast-content">
            <span class="toast-icon">${getToastIcon(type)}</span>
            <span class="toast-message">${escapeHtml(message)}</span>
            <button class="toast-close" onclick="this.parentElement.parentElement.remove()">×</button>
        </div>
    `;

    document.getElementById('toast-container').appendChild(toast);

    // Animation d'entrée
    setTimeout(() => toast.classList.add('show'), 10);

    // Suppression auto
    if (duration > 0) {
        setTimeout(() => {
            toast.classList.remove('show');
            setTimeout(() => toast.remove(), 300);
        }, duration);
    }
}

function getToastIcon(type) {
    const icons = {
        success: '✅',
        error: '❌',
        warning: '⚠️',
        info: 'ℹ️'
    };
    return icons[type] || icons['info'];
}

// ===== MODAL DE CONFIRMATION =====
// Appel: confirmModal("Êtes-vous sûr ?", "Supprimer", onConfirm, onCancel)
function confirmModal(message, confirmText = 'Confirmer', onConfirm = null, onCancel = null) {
    const backdrop = document.createElement('div');
    backdrop.className = 'modal-backdrop show';
    backdrop.id = 'confirm-backdrop';

    const modal = document.createElement('div');
    modal.className = 'confirm-modal show';
    modal.innerHTML = `
        <div class="modal-content">
            <h2>Confirmation</h2>
            <p>${escapeHtml(message)}</p>
            <div class="modal-actions">
                <button class="btn-secondary" id="cancel-btn">Annuler</button>
                <button class="btn-danger" id="confirm-btn">${confirmText}</button>
            </div>
        </div>
    `;

    document.body.appendChild(backdrop);
    document.body.appendChild(modal);

    // Empêcher scroll
    document.body.style.overflow = 'hidden';

    const closeModal = () => {
        modal.classList.remove('show');
        backdrop.classList.remove('show');
        setTimeout(() => {
            modal.remove();
            backdrop.remove();
            document.body.style.overflow = '';
        }, 300);
    };

    // Boutons
    document.getElementById('confirm-btn').onclick = () => {
        closeModal();
        if (onConfirm) onConfirm();
    };

    document.getElementById('cancel-btn').onclick = () => {
        closeModal();
        if (onCancel) onCancel();
    };

    // Fermer avec ESC
    const escKeyListener = (e) => {
        if (e.key === 'Escape') {
            document.removeEventListener('keydown', escKeyListener);
            closeModal();
        }
    };
    document.addEventListener('keydown', escKeyListener);

    // Fermer en cliquant sur fond
    backdrop.onclick = closeModal;
}

// ===== UTILITAIRES =====
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Copier au presse-papiers
function copyToClipboard(text, message = 'Copié !') {
    navigator.clipboard.writeText(text).then(() => {
        showNotification(message, 'success', 2000);
    }).catch(() => {
        showNotification('Erreur lors de la copie', 'error');
    });
}

// Partage WhatsApp
function shareWhatsApp(text, url = '') {
    const message = encodeURIComponent(text + '\n' + url);
    window.open('https://wa.me/?text=' + message, '_blank');
}

// Partage Email
function shareEmail(subject, body, url = '') {
    const message = encodeURIComponent(body + '\n' + url);
    window.location.href = 'mailto:?subject=' + encodeURIComponent(subject) + '&body=' + message;
}

// Impression/Partage natif
function nativeShare(title, text, url) {
    if (navigator.share) {
        navigator.share({ title: title, text: text, url: url }).catch(function(err) { console.log('Partage échoué:', err); });
    } else {
        showNotification('Partage non supporté sur ce navigateur', 'info');
    }
}

// ===== LAZY LOADING IMAGES AMÉLIORÉ =====
function initLazyLoading() {
    // Configuration de l'Intersection Observer pour le lazy loading
    if (!('IntersectionObserver' in window)) {
        // Fallback pour navigateurs anciens
        document.querySelectorAll('img[data-src]').forEach(function(img) {
            if (img.dataset.src) {
                img.src = img.dataset.src;
                img.removeAttribute('data-src');
            }
        });
        // Ajouter loading="lazy" à toutes les images qui n'en ont pas
        document.querySelectorAll('img:not([loading])').forEach(function(img) {
            img.setAttribute('loading', 'lazy');
        });
        return;
    }

    var imageObserver = new IntersectionObserver(function(entries, observer) {
        entries.forEach(function(entry) {
            if (entry.isIntersecting) {
                var img = entry.target;
                if (img.dataset.src) {
                    img.src = img.dataset.src;
                    img.removeAttribute('data-src');
                }
                img.loading = 'lazy';
                img.addEventListener('load', function() {
                    img.classList.add('loaded');
                });
                img.addEventListener('error', function() {
                    img.classList.add('error');
                });
                observer.unobserve(img);
            }
        });
    }, {
        rootMargin: '100px',
        threshold: 0.01
    });

    // Observer les images avec data-src
    document.querySelectorAll('img[data-src]').forEach(function(img) {
        imageObserver.observe(img);
    });

    // Ajouter loading="lazy" à toutes les images sans data-src
    document.querySelectorAll('img:not([loading]):not([data-src])').forEach(function(img) {
        img.setAttribute('loading', 'lazy');
    });
}

// ===== MOBILE MENU AMÉLIORÉ =====
function initMobileMenu() {
    var navToggle = document.getElementById('navToggle');
    var navLinks = document.getElementById('navLinks');
    
    if (!navToggle || !navLinks) return;

    // Fonction pour ouvrir/fermer le menu
    var toggleMenu = function(forceClose) {
        if (forceClose) {
            navLinks.classList.remove('open');
            document.body.style.overflow = '';
            navToggle.setAttribute('aria-expanded', 'false');
        } else {
            var isOpen = navLinks.classList.contains('open');
            navLinks.classList.toggle('open');
            document.body.style.overflow = isOpen ? '' : 'hidden';
            navToggle.setAttribute('aria-expanded', isOpen ? 'false' : 'true');
        }
    };

    // Toggle au clic sur le hamburger
    navToggle.addEventListener('click', function(e) {
        e.stopPropagation();
        toggleMenu();
    });

    // Ajouter un bouton de fermeture réel dans le menu
    var closeBtn = document.createElement('button');
    closeBtn.className = 'nav-close-btn';
    closeBtn.innerHTML = '✕';
    closeBtn.setAttribute('aria-label', 'Fermer le menu');
    closeBtn.style.cssText = 'position:absolute;top:18px;right:20px;font-size:1.8rem;background:transparent;border:none;color:white;cursor:pointer;padding:8px;line-height:1;z-index:1002;';
    navLinks.insertBefore(closeBtn, navLinks.firstChild);

    // Gestionnaire pour le bouton de fermeture
    closeBtn.addEventListener('click', function(e) {
        e.stopPropagation();
        toggleMenu(true);
    });

    // Fermer après clic sur un lien
    navLinks.querySelectorAll('a').forEach(function(link) {
        link.addEventListener('click', function() {
            toggleMenu(true);
        });
    });

    // Fermer si on change de taille d'écran
    window.addEventListener('resize', function() {
        if (window.innerWidth > 768) {
            toggleMenu(true);
        }
    });

    // Fermer avec la touche ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && navLinks.classList.contains('open')) {
            toggleMenu(true);
        }
    });
}

// ===== OPTIMISATION DES IMAGES =====
function optimizeImages() {
    // Ajouter decode="async" aux images pour un meilleur chargement
    document.querySelectorAll('img').forEach(function(img) {
        if (!img.hasAttribute('decode')) {
            img.setAttribute('decode', 'async');
        }
    });

    // Gestion des erreurs d'images
    document.querySelectorAll('img').forEach(function(img) {
        img.addEventListener('error', function() {
            this.classList.add('image-error');
        });
    });
}

// ===== PRE-LOAD CRITICAL IMAGES =====
function preloadCriticalImages() {
    // Précharger les images critiques (logo, icônes)
    var criticalImages = [
        './assets/img/icon-192.png',
        './assets/img/icon-512.png'
    ];
    
    criticalImages.forEach(function(src) {
        var img = new Image();
        img.src = src;
    });
}

// Initialiser au chargement du DOM
document.addEventListener('DOMContentLoaded', function() {
    initLazyLoading();
    initMobileMenu();
    optimizeImages();

    // Attacher les event listeners pour les boutons partage
    document.querySelectorAll('.share-btn').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            var jobTitle = this.dataset.jobTitle || 'Offre d\'emploi';
            var url = window.location.href;
            
            if (this.classList.contains('share-whatsapp')) {
                var phone = this.dataset.recruiterPhone || '';
                var recruiterName = this.dataset.recruiterName || 'le recruteur';
                shareWhatsAppRecruiter(jobTitle, recruiterName, url);
            } else if (this.classList.contains('share-email')) {
                var email = this.dataset.recruiterEmail || '';
                var recruiterName = this.dataset.recruiterName || 'le recruteur';
                shareEmailRecruiter(jobTitle, email, recruiterName, url);
            } else if (this.classList.contains('share-copy')) {
                copyToClipboard(url, 'Lien copié !');
            } else if (this.classList.contains('share-native')) {
                nativeShare(jobTitle, 'Découvre cette offre d\'emploi sur Impact Emploi', url);
            }
        });
    });
});

// Exécuter le préchargement ASAP
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', preloadCriticalImages);
} else {
    preloadCriticalImages();
}

// WhatsApp - Contacter le recruteur
function shareWhatsAppRecruiter(jobTitle, recruiterName, url) {
    var shareBtn = document.querySelector('.share-whatsapp');
    if (!shareBtn) {
        showNotification('Numéro WhatsApp du recruteur non disponible', 'warning');
        return;
    }
    
    var phone = shareBtn.dataset.recruiterPhone;
    if (!phone) {
        showNotification('Numéro WhatsApp du recruteur non disponible', 'warning');
        return;
    }
    
    // Formater le téléphone pour wa.me (format international)
    var waPhone = phone.replace(/[^0-9+]/g, '');
    var message = 'Bonjour ' + recruiterName + ',\n\nJ\'ai vu votre offre "' + jobTitle + '" sur Impact Emploi et je suis très intéressé!\n\nLien: ' + url;
    var encodedMsg = encodeURIComponent(message);
    
    window.open('https://wa.me/' + waPhone + '?text=' + encodedMsg, '_blank');
    showNotification('✅ Ouverture WhatsApp...', 'success');
}

// Email - Envoyer une candidature au recruteur
function shareEmailRecruiter(jobTitle, email, recruiterName, url) {
    if (!email) {
        showNotification('Email du recruteur non disponible', 'warning');
        return;
    }
    
    var subject = encodeURIComponent('Candidature: ' + jobTitle);
    var body = encodeURIComponent(
        'Bonjour ' + recruiterName + ',\n\n' +
        'Je suis très intéressé par l\'offre "' + jobTitle + '" publiée sur Impact Emploi.\n\n' +
        'Retrouver l\'offre ici: ' + url + '\n\n' +
        'Cordialement'
    );
    
    window.location.href = 'mailto:' + email + '?subject=' + subject + '&body=' + body;
    showNotification('✅ Ouverture du client email...', 'success');
}

