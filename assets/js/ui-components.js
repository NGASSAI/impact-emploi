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
    const message = encodeURIComponent(`${text}\n${url}`);
    window.open(`https://wa.me/?text=${message}`, '_blank');
}

// Partage Email
function shareEmail(subject, body, url = '') {
    const message = encodeURIComponent(`${body}\n${url}`);
    window.location.href = `mailto:?subject=${encodeURIComponent(subject)}&body=${message}`;
}

// Impression/Partage natif
function nativeShare(title, text, url) {
    if (navigator.share) {
        navigator.share({ title, text, url }).catch(err => console.log('Partage échoué:', err));
    } else {
        showNotification('Partage non supporté sur ce navigateur', 'info');
    }
}

// ===== LAZY LOADING IMAGES =====
// Active les images lazy loading via Intersection Observer
function initLazyLoading() {
    if (!('IntersectionObserver' in window)) {
        // Fallback pour navigateurs anciens
        document.querySelectorAll('img[data-src]').forEach(img => {
            img.src = img.dataset.src;
            img.removeAttribute('data-src');
        });
        return;
    }

    const imageObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                img.src = img.dataset.src;
                img.loading = 'lazy';
                img.addEventListener('load', () => {
                    img.classList.add('loaded');
                });
                img.addEventListener('error', () => {
                    img.classList.add('error');
                });
                observer.unobserve(img);
            }
        });
    }, {
        rootMargin: '50px'
    });

    document.querySelectorAll('img[data-src]').forEach(img => {
        imageObserver.observe(img);
    });
}

// Initialiser au chargement du DOM
document.addEventListener('DOMContentLoaded', function() {
    initLazyLoading();

    // MENU MOBILE : fermeture auto après clic sur un lien
    var navToggle = document.getElementById('navToggle');
    var navLinks = document.getElementById('navLinks');
    if (navToggle && navLinks) {
        navToggle.addEventListener('click', function() {
            navLinks.classList.toggle('open');
            // Empêche le scroll du body quand le menu est ouvert
            if(navLinks.classList.contains('open')) {
                document.body.style.overflow = 'hidden';
            } else {
                document.body.style.overflow = '';
            }
        });
        navLinks.querySelectorAll('a').forEach(function(link) {
            link.addEventListener('click', function() {
                navLinks.classList.remove('open');
                document.body.style.overflow = '';
            });
        });
        // Ferme le menu si on change de taille d'écran (orientation, clavier, etc.)
        window.addEventListener('resize', function() {
            navLinks.classList.remove('open');
            document.body.style.overflow = '';
        });
    }

    // Attacher les event listeners pour les boutons partage
    document.querySelectorAll('.share-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const jobTitle = this.dataset.jobTitle || 'Offre d\'emploi';
            const url = window.location.href;
            
            if (this.classList.contains('share-whatsapp')) {
                const phone = this.dataset.recruiterPhone || '';
                const recruiterName = this.dataset.recruiterName || 'le recruteur';
                shareWhatsAppRecruiter(jobTitle, recruiterName, url);
            } else if (this.classList.contains('share-email')) {
                const email = this.dataset.recruiterEmail || '';
                const recruiterName = this.dataset.recruiterName || 'le recruteur';
                shareEmailRecruiter(jobTitle, email, recruiterName, url);
            } else if (this.classList.contains('share-copy')) {
                copyToClipboard(url, 'Lien copié !');
            } else if (this.classList.contains('share-native')) {
                nativeShare(jobTitle, 'Découvre cette offre d\'emploi sur Impact Emploi', url);
            }
        });
    });
});

// WhatsApp - Contacter le recruteur
function shareWhatsAppRecruiter(jobTitle, recruiterName, url) {
    const phone = document.querySelector('.share-whatsapp').dataset.recruiterPhone;
    if (!phone) {
        showNotification('Numéro WhatsApp du recruteur non disponible', 'warning');
        return;
    }
    
    // Formater le téléphone pour wa.me (format international)
    const waPhone = phone.replace(/[^0-9+]/g, '');
    const message = `Bonjour ${recruiterName},\n\nJ'ai vu votre offre "${jobTitle}" sur Impact Emploi et je suis très intéressé!\n\nLien: ${url}`;
    const encodedMsg = encodeURIComponent(message);
    
    window.open(`https://wa.me/${waPhone}?text=${encodedMsg}`, '_blank');
    showNotification('✅ Ouverture WhatsApp...', 'success');
}

// Email - Envoyer une candidature au recruteur
function shareEmailRecruiter(jobTitle, email, recruiterName, url) {
    if (!email) {
        showNotification('Email du recruteur non disponible', 'warning');
        return;
    }
    
    const subject = encodeURIComponent(`Candidature: ${jobTitle}`);
    const body = encodeURIComponent(
        `Bonjour ${recruiterName},\n\n` +
        `Je suis très intéressé par l'offre "${jobTitle}" publiée sur Impact Emploi.\n\n` +
        `Retrouvez l'offre ici: ${url}\n\n` +
        `Cordialement`
    );
    
    window.location.href = `mailto:${email}?subject=${subject}&body=${body}`;
    showNotification('✅ Ouverture du client email...', 'success');
}
