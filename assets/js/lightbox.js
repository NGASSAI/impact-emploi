// Lightbox JavaScript - Version FINALE et TESTÉE
(function() {
    'use strict';
    
    console.log('Lightbox initialization started...');
    
    // Créer la lightbox
    function createLightbox() {
        const existing = document.getElementById('lightbox-modal');
        if (existing) existing.remove();
        
        const lightbox = document.createElement('div');
        lightbox.id = 'lightbox-modal';
        lightbox.innerHTML = `
            <div class="lightbox-overlay" id="lightbox-overlay">
                <div class="lightbox-content">
                    <button class="lightbox-close" id="lightbox-close">&times;</button>
                    <img id="lightbox-image" src="" alt="Image" class="lightbox-image">
                </div>
            </div>
        `;
        document.body.appendChild(lightbox);
        console.log('Lightbox DOM created');
        return lightbox;
    }
    
    const lightbox = createLightbox();
    const overlay = document.getElementById('lightbox-overlay');
    const lightboxImg = document.getElementById('lightbox-image');
    const closeBtn = document.getElementById('lightbox-close');
    
    // Initialiser les images cliquables
    function initLightbox() {
        const images = document.querySelectorAll('img[data-lightbox]');
        console.log('Found images with data-lightbox:', images.length);
        
        images.forEach((img, index) => {
            if (img.dataset.lightboxInitialized) return;
            img.dataset.lightboxInitialized = 'true';
            img.style.cursor = 'pointer';
            
            console.log('Initializing image:', img.src);
            
            img.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                console.log('Image clicked:', this.src);
                openLightbox(this.src, this.alt || 'Image');
            });
        });
    }
    
    // Ouvrir la lightbox
    function openLightbox(src, alt) {
        if (!src) {
            console.error('No src provided for lightbox');
            return;
        }
        
        console.log('Opening lightbox with:', src);
        
        lightboxImg.src = src;
        lightboxImg.alt = alt;
        overlay.classList.add('lightbox-active');
        
        // Focus sur le bouton fermer
        setTimeout(() => closeBtn.focus(), 100);
    }
    
    // Fermer la lightbox
    function closeLightbox() {
        console.log('Closing lightbox');
        overlay.classList.remove('lightbox-active');
        lightboxImg.src = '';
    }
    
    // Event listeners
    closeBtn.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        console.log('Close button clicked');
        closeLightbox();
    });
    
    overlay.addEventListener('click', function(e) {
        if (e.target === overlay) {
            console.log('Overlay clicked');
            closeLightbox();
        }
    });
    
    // Clavier
    document.addEventListener('keydown', function(e) {
        if (overlay.classList.contains('lightbox-active')) {
            if (e.key === 'Escape') {
                console.log('Escape key pressed');
                closeLightbox();
            }
        }
    });
    
    // Initialisation
    function init() {
        console.log('Initializing lightbox...');
        initLightbox();
    }
    
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
    
    // Réinitialiser après chargement
    window.addEventListener('load', init);
    
    // Fonctions globales
    window.openLightbox = openLightbox;
    window.closeLightbox = closeLightbox;
    
    console.log('Lightbox script loaded successfully');
})();
