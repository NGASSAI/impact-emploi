<?php
require_once 'includes/header.php';
require_once 'includes/config.php';
require_once 'includes/csrf.php';

// Sécurité : Si pas connecté, direction connexion
if (!isset($_SESSION['user_id'])) {
    header('Location: connexion.php');
    exit;
}

// Mise à jour rapide du profil (téléphone / whatsapp)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_contact'])) {
    // Validation CSRF
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = "⚠️ Erreur de sécurité : requête invalide.";
    } else {
        $tel = htmlspecialchars(trim($_POST['telephone']));
        $has_whatsapp = isset($_POST['has_whatsapp']) ? 1 : 0;
        $upd = $db->prepare("UPDATE users SET telephone = ?, has_whatsapp = ? WHERE id = ?");
        $upd->execute([$tel, $has_whatsapp, $_SESSION['user_id']]);
        // Recharger la page pour voir les changements
        header('Location: profil.php');
        exit;
    }
}

// On récupère les infos fraîches de l'utilisateur
$stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();
?>

<div class="container">
    <div class="form-card" style="max-width: 800px;">
        <h2>Mon Profil</h2>
        <div style="display: flex; gap: 20px; align-items: center; border-bottom: 1px solid #eee; padding-bottom: 20px; margin-bottom: 20px;">
            <div style="width: 80px; height: 80px; background: var(--primary); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 2rem; font-weight: bold;">
                <?php echo strtoupper(substr($user['prenom'], 0, 1)); ?>
            </div>
            <div>
                <p style="font-size: 1.2rem; font-weight: bold;"><?php echo htmlspecialchars($user['prenom'] . ' ' . $user['nom']); ?></p>
                <p style="color: var(--secondary);"><?php echo htmlspecialchars($user['email']); ?></p>
                <span class="badge" style="background: #dcfce7; color: #166534;">Compte <?php echo ucfirst($user['role']); ?></span>
            </div>
        </div>

        <div style="margin-bottom: 30px;">
            <p><strong>📞 Téléphone :</strong> <?php echo htmlspecialchars($user['telephone'] ?: 'Non renseigné'); ?>
                <a href="#update-contact" class="inline-button" title="Modifier vos coordonnées">Modifier mes coordonnées</a>
            </p>
            <p style="color:#6b7280; font-size:0.95rem; margin-top:6px;">Indiquez votre numéro pour être contacté; cochez WhatsApp si vous acceptez d'être joint par message.</p>
            <p><strong>📅 Membre depuis le :</strong>
                <?php
                    $inscrDate = null;
                    if (!empty($user['date_inscription'])) {
                        $inscrDate = $user['date_inscription'];
                    } elseif (!empty($user['created_at'])) {
                        $inscrDate = $user['created_at'];
                    }

                    if (!empty($inscrDate) && strtotime($inscrDate) !== false && strtotime($inscrDate) > 0) {
                        echo date('d/m/Y', strtotime($inscrDate));
                    } else {
                        echo '—';
                    }
                ?>
            </p>
        </div>

        <?php if ($user['role'] === 'recruteur'): ?>
            <hr>
            <h3 style="margin: 20px 0;">Mes offres publiées</h3>
            <?php
            // Certains projets n'ont pas la colonne `date_publication` — trier par `id` est plus sûr
            $stmtJobs = $db->prepare("SELECT * FROM jobs WHERE user_id = ? ORDER BY id DESC");
            $stmtJobs->execute([$user['id']]);
            $myJobs = $stmtJobs->fetchAll();
            ?>
            
            <?php if (count($myJobs) > 0): ?>
                <table style="width: 100%; border-collapse: collapse; margin-top: 10px;">
                    <tr style="background: #f1f5f9; text-align: left;">
                        <th style="padding: 10px;">Titre</th>
                        <th style="padding: 10px;">Date</th>
                        <th style="padding: 10px;">Actions</th>
                    </tr>
                    <?php foreach ($myJobs as $job): ?>
                        <tr style="border-bottom: 1px solid #eee;">
                            <td style="padding: 10px;"><?php echo htmlspecialchars($job['titre']); ?></td>
                            <td style="padding: 10px;">
                                <?php
                                    $dateJob = null;
                                    if (!empty($job['date_publication'])) {
                                        $dateJob = $job['date_publication'];
                                    } elseif (!empty($job['created_at'])) {
                                        $dateJob = $job['created_at'];
                                    }

                                    if (!empty($dateJob) && strtotime($dateJob) !== false && strtotime($dateJob) > 0) {
                                        echo date('d/m/Y', strtotime($dateJob));
                                    } else {
                                        echo '—';
                                    }
                                ?>
                            </td>
                            <td style="padding: 10px;">
                                <a href="voir_offre.php?id=<?php echo $job['id']; ?>" class="btn-secondary">Voir</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            <?php else: ?>
                <p>Vous n'avez pas encore publié d'offres.</p>
                <a href="poster_offre.php" class="btn-primary">Publier ma première offre →</a>
            <?php endif; ?>
        <?php endif; ?>

            <hr style="margin-top: 30px;">
        <h3>Mise à jour rapide des coordonnées</h3>
        <p style="color:#6b7280; font-size:0.95rem;">Modifiez votre numéro ou indiquez si vous avez WhatsApp — cela permet aux candidats ou recruteurs de vous contacter facilement.</p>
        <form id="update-contact" action="profil.php" method="POST" style="max-width: 500px;">
            <?php csrfField(); ?>
            <div class="form-group">
                <label>Téléphone</label>
                <input type="tel" name="telephone" value="<?php echo htmlspecialchars($user['telephone']); ?>">
            </div>
            <div class="form-group">
                <label><input type="checkbox" name="has_whatsapp" <?php echo !empty($user['has_whatsapp']) ? 'checked' : ''; ?>> J'ai WhatsApp <span style="color:#6b7280; font-size:0.9rem;">(Permet le contact via WhatsApp)</span></label>
            </div>
            <input type="hidden" name="update_contact" value="1">
            <button type="submit" title="Enregistrer votre numéro et préférence WhatsApp">Enregistrer mes coordonnées</button>
            <p style="margin-top:8px; color:#6b7280; font-size:0.9rem;">Après enregistrement, vos coordonnées seront affichées sur vos offres (si vous êtes recruteur).</p>
        </form>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>