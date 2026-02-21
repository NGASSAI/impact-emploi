<?php
require_once 'config.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : null;

if(!$id) {
    header('Location: ' . BASE_URL . '/index.php');
    exit();
}

// Récupérer les informations de la candidature
$stmt = $pdo->prepare("SELECT c.*, u.nom, u.prenom, u.email, j.titre FROM candidatures c 
                       JOIN users u ON c.id_utilisateur = u.id 
                       JOIN jobs j ON c.id_offre = j.id 
                       WHERE c.id = ?");
$stmt->execute([$id]);
$data = $stmt->fetch();

if(!$data) {
    header('Location: ' . BASE_URL . '/index.php?error=Candidature non trouvée');
    exit();
}

// Vérifier les permissions
if($_SESSION['auth_role'] !== 'admin' && $_SESSION['auth_id'] !== $data['recruteur_id']) {
    header('Location: ' . BASE_URL . '/index.php?error=Accès non autorisé');
    exit();
}

// Traiter la mise à jour
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    
    $message = clean($_POST['message'] ?? '');
    $statut = $_POST['statut'] ?? '';

    // Validation
    if(!in_array($statut, ['En attente', 'Accepté', 'Refusé'])) {
        header('Location: ' . BASE_URL . '/chat.php?id=' . $id . '&error=Statut invalide');
        exit();
    }

    try {
        $pdo->prepare("UPDATE candidatures SET recruteur_message = ?, statut = ?, updated_at = NOW() WHERE id = ?")
           ->execute([$message, $statut, $id]);

        log_activity($_SESSION['auth_id'], 'update_candidature', "Candidature $id mise à jour: $statut");
        
        header('Location: ' . BASE_URL . '/chat.php?id=' . $id . '&success=Candidature mise à jour');
        exit();
    } catch(Exception $e) {
        header('Location: ' . BASE_URL . '/chat.php?id=' . $id . '&error=Erreur lors de la mise à jour');
        exit();
    }
}

include 'includes/header.php';
?>

<div class="container" style="max-width: 900px; padding: 40px 0;">
    <!-- En-tête -->
    <div style="margin-bottom: 30px;">
        <h1 style="font-size: 2rem; color: var(--primary); margin-bottom: 10px;">
            💬 Répondre à la candidature
        </h1>
    </div>

    <!-- Messages -->
    <?php if(isset($_GET['success'])): ?>
        <div class="alert alert-success" style="margin-bottom: 20px;">
            ✓ <?php echo htmlspecialchars($_GET['success']); ?>
        </div>
    <?php endif; ?>
    
    <?php if(isset($_GET['error'])): ?>
        <div class="alert alert-error" style="margin-bottom: 20px;">
            ✕ <?php echo htmlspecialchars($_GET['error']); ?>
        </div>
    <?php endif; ?>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 25px;">
        <!-- Infos du candidat -->
        <div class="card">
            <h3 style="color: var(--primary); margin-bottom: 20px;">👤 Informations du Candidat</h3>
            
            <div style="margin-bottom: 15px;">
                <label class="text-muted" style="display: block; margin-bottom: 5px;">Nom Complet</label>
                <p style="font-size: 1.2rem; font-weight: 700; color: var(--text-primary);">
                    <?php echo htmlspecialchars($data['prenom'] . ' ' . $data['nom']); ?>
                </p>
            </div>

            <div style="margin-bottom: 15px;">
                <label class="text-muted" style="display: block; margin-bottom: 5px;">📧 Email</label>
                <a href="mailto:<?php echo htmlspecialchars($data['email']); ?>" style="color: var(--primary); text-decoration: none;">
                    <?php echo htmlspecialchars($data['email']); ?>
                </a>
            </div>

            <div style="margin-bottom: 15px;">
                <label class="text-muted" style="display: block; margin-bottom: 5px;">💼 Poste Demandé</label>
                <p style="font-weight: 600; color: var(--secondary);">
                    <?php echo htmlspecialchars($data['titre']); ?>
                </p>
            </div>

            <div style="margin-bottom: 15px;">
                <label class="text-muted" style="display: block; margin-bottom: 5px;">📅 Date de Candidature</label>
                <p><?php echo date('d/m/Y à H:i', strtotime($data['date_postulation'])); ?></p>
            </div>

            <div>
                <label class="text-muted" style="display: block; margin-bottom: 5px;">📄 Curriculum Vitae</label>
                <a href="<?php echo BASE_URL; ?>/uploads/cv/<?php echo htmlspecialchars($data['nom_cv']); ?>" target="_blank" class="btn btn-primary btn-block">
                    📥 Télécharger le CV
                </a>
            </div>
        </div>

        <!-- Formulaire de réponse -->
        <div class="card">
            <h3 style="color: var(--primary); margin-bottom: 20px;">✍️ Votre Réponse</h3>

            <form method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

                <div class="form-group">
                    <label for="statut">🎯 Décision</label>
                    <select id="statut" name="statut" required style="padding: 12px;">
                        <option value="En attente" <?php echo $data['statut'] === 'En attente' ? 'selected' : ''; ?>>
                            ⏳ En évaluation
                        </option>
                        <option value="Accepté" <?php echo $data['statut'] === 'Accepté' ? 'selected' : ''; ?>>
                            ✅ Accepté
                        </option>
                        <option value="Refusé" <?php echo $data['statut'] === 'Refusé' ? 'selected' : ''; ?>>
                            ❌ Refusé
                        </option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="message">📝 Message au Candidat</label>
                    <textarea id="message" name="message" placeholder="Écrivez votre message (feedback, prochaines étapes, etc.)" style="min-height: 150px; padding: 12px;"><?php echo htmlspecialchars($data['recruteur_message'] ?? ''); ?></textarea>
                    <small class="text-muted">Soyez professionnel et bienveillant dans votre message</small>
                </div>

                <div style="display: flex; gap: 10px;">
                    <button type="submit" class="btn btn-primary btn-block" style="padding: 14px;">
                        💾 Mettre à Jour
                    </button>
                    <a href="<?php echo BASE_URL; ?>/recruteur_dashboard.php" class="btn btn-outline btn-block" style="padding: 14px;">
                        Retour
                    </a>
                </div>
            </form>

            <!-- Statut actuel -->
            <div style="margin-top: 20px; padding: 15px; background: var(--light); border-radius: 8px;">
                <p style="margin: 0; font-size: 0.9rem; color: var(--text-secondary);">
                    <strong>Statut actuel:</strong> 
                    <span class="badge <?php 
                        echo $data['statut'] === 'Accepté' ? 'badge-success' : 
                        ($data['statut'] === 'Refusé' ? 'badge-danger' : 'badge-warning');
                    ?>">
                        <?php echo htmlspecialchars($data['statut']); ?>
                    </span>
                </p>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>