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
// Navigation Mobile - Correction Professionnelle
document.addEventListener('DOMContentLoaded', function() {
    var navToggle = document.getElementById('navToggle');
    var navLinks = document.getElementById('navLinks');
    
    if (navToggle && navLinks) {
        // Toggle menu
        navToggle.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            navLinks.classList.toggle('open');
            
            // Changer l'icône du bouton
            if (navLinks.classList.contains('open')) {
                navToggle.textContent = '✕';
                navToggle.style.fontSize = '1.5rem';
            } else {
                navToggle.textContent = '☰';
                navToggle.style.fontSize = '1.2rem';
            }
        });
        
        // Fermer au clic sur les liens
        navLinks.querySelectorAll('a').forEach(function(link) {
            link.addEventListener('click', function() {
                navLinks.classList.remove('open');
                navToggle.textContent = '☰';
                navToggle.style.fontSize = '1.2rem';
            });
        });
        
        // Fermer au clic extérieur
        document.addEventListener('click', function(e) {
            if (!e.target.closest('nav')) {
                navLinks.classList.remove('open');
                navToggle.textContent = '☰';
                navToggle.style.fontSize = '1.2rem';
            }
        });
    }
});
</script>

</body>
</html>
