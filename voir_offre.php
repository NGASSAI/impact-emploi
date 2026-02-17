<?php
require_once 'includes/header.php';
require_once 'includes/config.php';

// On récupère l'ID de l'offre dans l'URL
$job_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// On récupère l'offre ET les infos du recruteur grâce à une JOINTURE SQL (JOIN)
$stmt = $db->prepare("
    SELECT jobs.*, users.nom, users.prenom, users.email, users.telephone, users.has_whatsapp
    FROM jobs 
    JOIN users ON jobs.user_id = users.id 
    WHERE jobs.id = ?
");
$stmt->execute([$job_id]);
$job = $stmt->fetch();

if (!$job) {
    echo "<div class='alert alert-error'>Offre introuvable.</div>";
    require_once 'includes/footer.php';
    exit;
}
?>

<div class="container">
    <a href="index.php" style="text-decoration: none; color: var(--secondary); display: inline-block; margin-bottom: 20px;">← Retour aux offres</a>

    <div class="offer-container">
        <div class="offer-header">
            <div>
                <?php if (!empty($job['image'])): ?>
                    <img src="assets/uploads/jobs/<?php echo htmlspecialchars($job['image']); ?>" alt="<?php echo htmlspecialchars($job['titre']); ?>" style="max-width:360px; border-radius:8px; margin-bottom:12px; display:block;">
                <?php endif; ?>
                <span class="badge"><?php echo htmlspecialchars($job['type_contrat']); ?></span>
                <h1 style="color: var(--primary); margin-top: 10px;"><?php echo htmlspecialchars($job['titre']); ?></h1>
                <p style="font-size: 1.1rem; color: var(--secondary);">📍 <?php echo htmlspecialchars($job['lieu']); ?></p>
            </div>
            <div style="text-align: right; color: var(--success); font-weight: bold; font-size: 1.2rem;">
                <?php echo htmlspecialchars($job['salaire'] ?: 'Salaire à débattre'); ?>
            </div>
        </div>

        <hr style="border: 0; border-top: 1px solid #eee; margin: 20px 0;">

        <h3>Description du poste</h3>
        <p style="white-space: pre-wrap; margin-top: 15px; color: #475569; line-height: 1.8;">
            <?php echo htmlspecialchars($job['description']); ?>
        </p>

        <div class="offer-contact">
            <h3 style="color: var(--primary); margin-bottom: 15px;">Contact pour postuler</h3>
            
            <?php if (isset($_SESSION['user_id'])): ?>
                <p><strong>Recruteur :</strong> <?php echo htmlspecialchars($job['prenom'] . ' ' . $job['nom']); ?></p>
                <p><strong>📧 Email :</strong> <a href="mailto:<?php echo $job['email']; ?>"><?php echo htmlspecialchars($job['email']); ?></a></p>
                <p><strong>📞 Téléphone :</strong> <a href="tel:<?php echo $job['telephone']; ?>"><?php echo htmlspecialchars($job['telephone']); ?></a></p>
                    <?php
                    // Normaliser le numéro pour wa.me en utilisant la fonction du config
                    $waPhone = '';
                    if (!empty($job['telephone'])) {
                        $waPhone = format_phone_for_wa($job['telephone']);
                    }

                    if (!empty($job['has_whatsapp']) && !empty($waPhone)):
                ?>
                    <p><strong>💬 WhatsApp :</strong> <a href="https://wa.me/<?php echo $waPhone; ?>" target="_blank" rel="noopener">Contacter sur WhatsApp</a></p>
                <?php else: ?>
                    <p style="color: #9ca3af;">Le recruteur n'a pas indiqué WhatsApp ou le numéro est invalide.</p>
                <?php endif; ?>
                <p style="margin-top: 10px; font-size: 0.9rem; color: #64748b;"><em>Dites que vous appelez de la part de "Impact Emploi".</em></p>
            <?php else: ?>
                <p style="text-align: center; padding: 10px;">
                    <strong>Connectez-vous pour voir les coordonnées du recruteur.</strong><br><br>
                    <a href="connexion.php" class="button" style="background: var(--primary); color: white; padding: 8px 15px; text-decoration: none; border-radius: 5px;">Se connecter</a>
                </p>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>