// Lightbox JavaScript - Image viewer modal
document.addEventListener('DOMContentLoaded', function() {
    // Créer le modal lightbox
    const lightbox = document.createElement('div');
    lightbox.id = 'lightbox-modal';
    lightbox.innerHTML = `
        <div class="lightbox-overlay" id="lightbox-overlay">
            <div class="lightbox-container">
                <button class="lightbox-close" id="lightbox-close">&times;</button>
                <img id="lightbox-image" src="" alt="Image en détail" class="lightbox-image">
                <div class="lightbox-nav">
                    <button class="lightbox-prev" id="lightbox-prev">❮</button>
                    <button class="lightbox-next" id="lightbox-next">❯</button>
                </div>
            </div>
        </div>
    `;
    document.body.appendChild(lightbox);

    // Variables
    const overlay = document.getElementById('lightbox-overlay');
    const lightboxImg = document.getElementById('lightbox-image');
    const closeBtn = document.getElementById('lightbox-close');
    const prevBtn = document.getElementById('lightbox-prev');
    const nextBtn = document.getElementById('lightbox-next');
    let images = [];
    let currentIndex = 0;

    // Ajouter les images cliquables
    function initLightbox() {
        const clickableImages = document.querySelectorAll('img[data-lightbox]');
        images = Array.from(clickableImages);
        
        clickableImages.forEach((img, index) => {
            img.style.cursor = 'pointer';
            img.addEventListener('click', function() {
                currentIndex = index;
                openLightbox(this.src);
            });
        });
    }

    // Ouvrir la lightbox
    function openLightbox(src) {
        lightboxImg.src = src;
        overlay.classList.add('lightbox-active');
        document.body.style.overflow = 'hidden';
    }

    // Fermer la lightbox
    function closeLightbox() {
        overlay.classList.remove('lightbox-active');
        document.body.style.overflow = 'auto';
    }

    // Navigation
    function showPrev() {
        currentIndex = (currentIndex - 1 + images.length) % images.length;
        lightboxImg.src = images[currentIndex].src;
    }

    function showNext() {
        currentIndex = (currentIndex + 1) % images.length;
        lightboxImg.src = images[currentIndex].src;
    }

    // Event listeners
    closeBtn.addEventListener('click', closeLightbox);
    overlay.addEventListener('click', function(e) {
        if(e.target === overlay) closeLightbox();
    });
    prevBtn.addEventListener('click', showPrev);
    nextBtn.addEventListener('click', showNext);

    // Clavier
    document.addEventListener('keydown', function(e) {
        if(!overlay.classList.contains('lightbox-active')) return;
        if(e.key === 'Escape') closeLightbox();
        if(e.key === 'ArrowLeft') showPrev();
        if(e.key === 'ArrowRight') showNext();
    });

    // Initialiser au chargement
    initLightbox();
    // Réinitialiser après AJAX si le site l'utilise
    window.addEventListener('load', initLightbox);
});
