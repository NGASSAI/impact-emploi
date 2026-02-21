<?php
require_once 'config.php';
include 'includes/header.php';
?>

<div class="container" style="padding: 40px 0; max-width: 900px;">
    <h1 style="color: var(--primary);">❓ Aide</h1>
    <p class="text-muted">Besoin d'aide ? Voici quelques ressources rapides pour commencer :</p>

    <div class="card" style="margin-top: 20px;">
        <h3>FAQ</h3>
        <ul>
            <li>Comment créer un compte ? → Utilisez le formulaire d'inscription.</li>
            <li>Où poster une offre ? → Connectez-vous en tant que recruteur.</li>
            <li>Problèmes techniques ? → Contactez l'administrateur via l'email en bas de page.</li>
        </ul>
    </div>

    <div class="card" style="margin-top: 20px;">
        <h3>Contact</h3>
        <p>Pour toute assistance, envoyez un email à <?php echo ADMIN_EMAIL; ?>.</p>
    </div>

</div>

<?php include 'includes/footer.php'; ?>
