</main>
<footer>
    <div class="footer-wrapper">
        <!-- Top Section: Logo + About + Contact Icons -->
        <div class="footer-top">
            <div>
                <h3 class="footer-logo">⚡ Impact Emploi</h3>
                <p style="color: #D1D5DB; margin-top: 8px; font-size: 0.9rem; max-width: 300px;">Reliez-vous avec les meilleures opportunités d'emploi</p>
            </div>
            <div class="footer-contact-icons">
                <a href="mailto:<?php echo ADMIN_EMAIL; ?>" title="Nous envoyer un email" class="icon-link email-icon">✉️</a>
                <a href="https://wa.me/<?php echo str_replace(['+', ' ', '-'], '', WHATSAPP_NUMBER); ?>" target="_blank" title="Nous contacter sur WhatsApp" class="icon-link whatsapp-icon">💬</a>
            </div>
        </div>

        <!-- Middle Section: Links -->
        <div class="footer-content">
            <div class="footer-section">
                <h4>Navigation</h4>
                <ul class="footer-links">
                    <li><a href="<?php echo BASE_URL; ?>/index.php">Accueil</a></li>
                    <li><a href="<?php echo BASE_URL; ?>/register.php">S'inscrire</a></li>
                    <li><a href="<?php echo BASE_URL; ?>/login.php">Connexion</a></li>
                </ul>
            </div>
            <div class="footer-section">
                <h4>Légal</h4>
                <ul class="footer-links">
                    <li><a href="<?php echo BASE_URL; ?>/terms.php">Conditions d'Utilisation</a></li>
                    <li><a href="<?php echo BASE_URL; ?>/privacy.php">Politique de Confidentialité</a></li>
                </ul>
            </div>
        </div>

        <!-- Bottom Section: Copyright -->
        <div class="footer-bottom">
            <span>© 2026 Impact Emploi - Tous droits réservés</span>
        </div>
    </div>
</footer>

<script src="<?php echo BASE_URL; ?>/assets/js/lightbox.js"></script>
<script>
// Navigation Mobile - Bouton de Fermeture Simple
document.addEventListener('DOMContentLoaded', function() {
    const navToggle = document.getElementById('navToggle');
    const navLinks = document.getElementById('navLinks');
    const closeMenuBtn = document.getElementById('closeMenuBtn');
    
    if (navToggle && navLinks && closeMenuBtn) {
        // Ouvrir le menu
        navToggle.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            navLinks.classList.add('open');
            navToggle.setAttribute('aria-expanded', 'true');
            document.body.style.overflow = 'hidden';
        });
        
        // Fermer le menu avec le bouton X
        closeMenuBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            navLinks.classList.remove('open');
            navToggle.setAttribute('aria-expanded', 'false');
            document.body.style.overflow = '';
        });
        
        // Fermer au clic sur les liens
        navLinks.querySelectorAll('a').forEach(function(link) {
            link.addEventListener('click', function() {
                // Ne pas fermer pour liens externes
                if (link.href.includes('mailto:') || link.href.includes('tel:') || link.href.includes('wa.me')) {
                    return;
                }
                
                // Fermer le menu
                navLinks.classList.remove('open');
                navToggle.setAttribute('aria-expanded', 'false');
                document.body.style.overflow = '';
            });
        });
        
        // Fermer au clic extérieur
        document.addEventListener('click', function(e) {
            if (!e.target.closest('nav') && navLinks.classList.contains('open')) {
                navLinks.classList.remove('open');
                navToggle.setAttribute('aria-expanded', 'false');
                document.body.style.overflow = '';
            }
        });
        
        // Fermer avec Escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && navLinks.classList.contains('open')) {
                navLinks.classList.remove('open');
                navToggle.setAttribute('aria-expanded', 'false');
                document.body.style.overflow = '';
            }
        });
    }
});
</script>

</body>
</html>
