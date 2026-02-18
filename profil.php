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
    <!-- Bouton retour à l'accueil -->
    <div style="margin-bottom: 20px;">
        <a href="index.php" class="back-link">← Retour aux offres disponibles</a>
    </div>

    <!-- Card d'en-tête professionnel -->
    <div class="profile-header-card">
        <div class="profile-avatar">
            <?php echo strtoupper(substr($user['prenom'], 0, 1)); ?>
        </div>
        <div class="profile-info">
            <h1 class="profile-name"><?php echo htmlspecialchars($user['prenom'] . ' ' . $user['nom']); ?></h1>
            <p class="profile-email"><?php echo htmlspecialchars($user['email']); ?></p>
            <div class="profile-badges">
                <span class="badge-role"><?php echo $user['role'] === 'recruteur' ? '👔 Recruteur' : '💼 Chercheur d\'emploi'; ?></span>
                <span class="badge-member">
                    Membre depuis 
                    <?php
                        $inscrDate = $user['date_inscription'] ?? $user['created_at'];
                        if (!empty($inscrDate) && strtotime($inscrDate) !== false) {
                            echo date('d/m/Y', strtotime($inscrDate));
                        } else {
                            echo 'récemment';
                        }
                    ?>
                </span>
            </div>
        </div>
    </div>

    <!-- Section Contact -->
    <div class="profile-section">
        <h2>📞 Informations de contact</h2>
        <div class="contact-info">
            <p><strong>Téléphone:</strong> <?php echo htmlspecialchars($user['telephone'] ?: '—'); ?></p>
            <p><strong>WhatsApp:</strong> <?php echo $user['has_whatsapp'] ? '✅ Disponible' : '❌ Non'; ?></p>
            <p class="contact-hint">Ces informations aident recruteurs et candidats à vous contacter facilement.</p>
        </div>
    </div>

    <!-- Section Recruteur - Offres publiées -->
    <?php if ($user['role'] === 'recruteur'): ?>
        <div class="profile-section">
            <h2>📋 Mes offres publiées</h2>
            <?php
                $stmtJobs = $db->prepare("SELECT * FROM jobs WHERE user_id = ? ORDER BY id DESC");
                $stmtJobs->execute([$user['id']]);
                $myJobs = $stmtJobs->fetchAll();
            ?>
            
            <?php if (count($myJobs) > 0): ?>
                <div class="jobs-table-wrapper">
                    <table class="jobs-table">
                        <thead>
                            <tr>
                                <th>Titre de l'offre</th>
                                <th>Date de publication</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($myJobs as $job): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($job['titre']); ?></td>
                                    <td>
                                        <?php
                                            $dateJob = $job['date_publication'] ?? $job['created_at'];
                                            if (!empty($dateJob) && strtotime($dateJob) !== false) {
                                                echo date('d/m/Y', strtotime($dateJob));
                                            } else {
                                                echo '—';
                                            }
                                        ?>
                                    </td>
                                    <td>
                                        <a href="voir_offre.php?id=<?php echo $job['id']; ?>" class="btn-secondary" style="font-size: 0.9rem; padding: 0.5rem 1rem;">Voir l'offre</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="no-data-box">
                    <p>Vous n'avez pas encore publié d'offres. Commencez maintenant!</p>
                    <a href="poster_offre.php" class="btn-primary">+ Publier une offre</a>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <!-- Section Mise à jour des coordonnées -->
    <div class="profile-section">
        <h2>✏️ Mettre à jour mes coordonnées</h2>
        <form id="update-contact" action="profil.php" method="POST" class="profile-form">
            <?php csrfField(); ?>
            
            <div class="form-group">
                <label for="telephone">Téléphone</label>
                <input type="tel" id="telephone" name="telephone" value="<?php echo htmlspecialchars($user['telephone']); ?>" placeholder="Ex: 0600000000">
            </div>

            <div class="form-group-checkbox">
                <label>
                    <input type="checkbox" name="has_whatsapp" <?php echo !empty($user['has_whatsapp']) ? 'checked' : ''; ?>>
                    <span>J'utilise WhatsApp et j'accepte les messages</span>
                </label>
            </div>

            <div class="form-actions">
                <input type="hidden" name="update_contact" value="1">
                <button type="submit" class="btn-primary">Enregistrer les modifications</button>
                <a href="mon_espace.php" class="btn-secondary">Voir mes candidatures</a>
            </div>

            <p class="form-hint">Vos coordonnées seront visibles sur vos offres publiées et permettront aux autres utilisateurs de vous contacter.</p>
        </form>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>