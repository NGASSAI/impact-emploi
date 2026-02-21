<!-- 404 Page - Erreur 404 -->
<?php
require_once 'config.php';
include 'includes/header.php';
?>

<div class="container flex-center" style="padding: 60px 0;">
    <div class="card" style="max-width: 600px; text-align: center; padding: 60px 30px;">
        <div style="font-size: 5rem; margin-bottom: 20px;">😕</div>
        <h1 style="color: var(--primary); font-size: 2.5rem; margin-bottom: 10px;">404</h1>
        <h2 style="color: var(--text-secondary); margin-bottom: 20px;">Page Non Trouvée</h2>
        
        <p style="color: var(--text-secondary); font-size: 1.1rem; line-height: 1.6; margin-bottom: 30px;">
            Désolé, la page que vous recherchez n'existe pas ou a été déplacée.
        </p>

        <div style="display: flex; gap: 10px; justify-content: center;">
            <a href="<?php echo BASE_URL; ?>/index.php" class="btn btn-primary">🏠 Aller à l'Accueil</a>
            <button onclick="history.back()" class="btn btn-outline">← Retour</button>
        </div>

        <div style="margin-top: 40px; padding: 20px; background: var(--light); border-radius: 8px;">
            <p style="color: var(--text-secondary); margin: 0;">
                Besoin d'aide? <a href="mailto:nathanngassai885@gmail.com" style="color: var(--primary);">Contactez le support</a>
            </p>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>