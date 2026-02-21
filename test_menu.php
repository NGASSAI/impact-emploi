<?php
require_once 'includes/header.php';
?>

<div class="container">
    <h2>Test Menu Hamburger</h2>
    <p>Sur mobile (< 769px), clique sur le bouton ≡ pour tester le menu</p>
    <p style="background: #e0f2fe; padding: 15px; border-radius: 8px; margin-top: 20px;">
        ✅ Le menu doit afficher les liens: Accueil + Motivational text (si pas connecté)
    </p>
    
    <h3>Infos Debug:</h3>
    <pre>
Largeur écran: <script>document.write(window.innerWidth)</script>px
Session: <?php echo isset($_SESSION['user_id']) ? 'CONNECTÉ (ID: ' . $_SESSION['user_id'] . ')' : 'NON CONNECTÉ'; ?>
    </pre>
    
    <h3>Vérification HTML</h3>
    <p>Hamburger ID: <code id="hamburger-check">en attente...</code></p>
    <p>Nav-menu ID: <code id="navmenu-check">en attente...</code></p>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('hamburger-check').textContent = 
                document.getElementById('hamburger') ? '✅ TROUVÉ' : '❌ MANQUANT';
            document.getElementById('navmenu-check').textContent = 
                document.getElementById('nav-menu') ? '✅ TROUVÉ' : '❌ MANQUANT';
            
            const hamburger = document.getElementById('hamburger');
            if (hamburger) {
                console.log('Hamburger button found, testing click...');
                hamburger.addEventListener('click', function() {
                    console.log('✅ Hamburger click detected! Menu active?', 
                        document.getElementById('nav-menu').classList.contains('active'));
                });
            }
        });
    </script>
</div>

<?php require_once 'includes/footer.php'; ?>
