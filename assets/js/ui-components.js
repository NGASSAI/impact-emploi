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
