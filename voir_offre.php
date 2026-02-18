<?php
require_once 'includes/header.php';
require_once 'includes/config.php';

// On récupère l'ID de l'offre dans l'URL
$job_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($job_id <= 0) {
    echo "<div class='container'><div class='alert alert-error'>Identifiant d'offre invalide.</div></div>";
    require_once 'includes/footer.php';
    exit;
}

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
    echo "<div class='container'><div class='alert alert-error'>Offre introuvable.</div></div>";
    require_once 'includes/footer.php';
    exit;
}
?>

<div class="container">
    <a href="index.php" class="back-link">← Retour aux offres</a>

    <div class="offer-container">
        <div class="offer-header">
            <div>
                <?php if (!empty($job['image'])): ?>
                    <img src="assets/uploads/jobs/<?php echo htmlspecialchars($job['image']); ?>" alt="<?php echo htmlspecialchars($job['titre']); ?>" class="offer-image">
                <?php endif; ?>
                <div class="offer-type-section">
                    <span class="badge"><?php echo htmlspecialchars($job['type_contrat']); ?></span>
                </div>
            </div>
            <div>
                <h1 class="offer-title"><?php echo htmlspecialchars($job['titre']); ?></h1>
                <p class="offer-location">📍 <?php echo htmlspecialchars($job['lieu']); ?></p>
            </div>
            <div class="offer-salary">
                <?php echo htmlspecialchars($job['salaire'] ?: 'Salaire à débattre'); ?>
            </div>
        </div>

        <hr class="offer-separator">

        <h3>Description du poste</h3>
        <p class="offer-description">
            <?php echo htmlspecialchars($job['description']); ?>
        </p>

        <!-- Section Partage d'offre -->
        <div class="offer-share-section">
            <h3>📤 Partager cette offre</h3>
            <div class="share-buttons">
                <button class="share-btn share-whatsapp" onclick="shareWhatsApp('Excellent job !! 👀', '<?php echo htmlspecialchars($job['titre']); ?>\n' + window.location.href)">
                    💬 WhatsApp
                </button>
                <button class="share-btn share-email" onclick="shareEmail('Offre d\'emploi: <?php echo htmlspecialchars($job['titre']); ?>', 'Découvre cette offre d\'emploi intéressante', window.location.href)">
                    📧 Email
                </button>
                <button class="share-btn share-copy" onclick="copyToClipboard(window.location.href, 'Lien copié !')">
                    📋 Copier le lien
                </button>
                <button class="share-btn share-native" onclick="nativeShare('<?php echo htmlspecialchars($job['titre']); ?>', 'Découvre cette offre d\'emploi sur Impact Emploi', window.location.href)">
                    📤 Partager
                </button>
            </div>
        </div>

        <hr class="offer-separator">
            <h3 class="offer-contact-title">Candidaturer</h3>
            <?php if (isset($_SESSION['user_id'])): ?>
                <!-- Formulaire de candidature pour les utilisateurs connectés -->
                <form action="scripts/postuler.php" method="POST" enctype="multipart/form-data" class="offer-form">
                    <input type="hidden" name="id_offre" value="<?php echo htmlspecialchars($job['id']); ?>">
                    
                    <div class="form-group">
                        <label for="cv">📄 Télécharger votre CV (PDF uniquement, max 5MB) :</label>
                        <input type="file" name="cv_file" id="cv" accept=".pdf" required>
                    </div>
                    
                    <button type="submit" name="submit_postule" class="btn-submit">
                        ✉️ Envoyer ma candidature
                    </button>
                </form>

                <hr class="offer-contact-separator">

                <!-- Infos recruteur -->
                <h4 class="recruiter-title">Contact du recruteur</h4>
                <p><strong>👤 Recruteur :</strong> <?php echo htmlspecialchars($job['prenom'] . ' ' . $job['nom']); ?></p>
                <p><strong>📧 Email :</strong> <a href="mailto:<?php echo htmlspecialchars($job['email']); ?>"><?php echo htmlspecialchars($job['email']); ?></a></p>
                <p><strong>📞 Téléphone :</strong> <a href="tel:<?php echo htmlspecialchars($job['telephone']); ?>"><?php echo htmlspecialchars($job['telephone']); ?></a></p>
                
                <?php
                    // Normaliser le numéro pour wa.me en utilisant la fonction du config
                    $waPhone = '';
                    if (!empty($job['telephone'])) {
                        $waPhone = format_phone_for_wa($job['telephone']);
                    }

                    if (!empty($job['has_whatsapp']) && !empty($waPhone)):
                ?>
                    <p><strong>💬 WhatsApp :</strong> <a href="https://wa.me/<?php echo htmlspecialchars($waPhone); ?>" target="_blank" rel="noopener">Contacter sur WhatsApp</a></p>
                <?php else: ?>
                    <p class="recruiter-no-whatsapp">Le recruteur n'a pas indiqué WhatsApp.</p>
                <?php endif; ?>

                <p class="recruiter-note">💡 Conseil : Mentionnez "Impact Emploi" quand vous les appelez.</p>

            <?php else: ?>
                <!-- Non connecté -->
                <div class="not-logged-in-box">
                    <p class="lock-icon">🔐 Connectez-vous pour candidater</p>
                    <p>Vous devez être connecté pour envoyer une candidature et voir les coordonnées du recruteur.</p>
                    <a href="connexion.php" class="login-btn">
                        Se connecter →
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
