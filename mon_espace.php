<?php
require_once 'config.php';

// ===== PROTECTION =====
if (!is_logged_in()) {
    header('Location: login.php?error=connexion');
    exit();
}

try {
    $id_user = intval($_SESSION['auth_id']);

    // Récupérer les candidatures envoyées
    $sql_cand = "SELECT c.*, j.titre, j.lieu 
                 FROM candidatures c 
                 JOIN jobs j ON c.id_offre = j.id 
                 WHERE c.id_utilisateur = ? 
                 ORDER BY c.date_postulation DESC";
    $stmt_cand = $pdo->prepare($sql_cand);
    $stmt_cand->execute([$id_user]);
    $mes_candidatures = $stmt_cand->fetchAll();

} catch (PDOException $e) {
    error_log("Erreur Mon Espace: " . $e->getMessage());
    $mes_candidatures = [];
}
?>

<div class="container">
    <?php include 'includes/header.php'; ?>
    
    <!-- Afficher les alertes -->
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

    <h1 class="dashboard-title">📌 Mon Espace</h1>

    <!-- SECTION CANDIDATURES -->
    <div class="dashboard-section">
        <h2>✉️ Mes Candidatures Envoyées</h2>
        
        <?php if (count($mes_candidatures) > 0): ?>
            <div class="job-grid">
                <?php foreach ($mes_candidatures as $cand): ?>
                    <div class="job-card">
                        <h3><?php echo htmlspecialchars($cand['titre']); ?></h3>
                        <p class="meta">
                            <strong>📍 Lieu :</strong> <?php echo htmlspecialchars($cand['lieu']); ?>
                        </p>
                        <p class="meta">
                            <strong>📅 Candidature du :</strong> 
                            <?php echo format_congo_date($cand['date_postulation']); ?>
                        </p>
                        <p class="meta">
                            <strong>📄 CV :</strong> 
                            <a href="<?php echo BASE_URL; ?>/uploads/cv/<?php echo htmlspecialchars($cand['nom_cv']); ?>" 
                               target="_blank" rel="noopener" class="btn-secondary"
                               style="font-size:0.9rem; padding:0.5rem 1rem;">
                                📄 Voir mon CV
                            </a>
                        </p>
                        <div style="margin-top: 15px; display: flex; gap: 10px;">
                            <a href="voir_offre.php?id=<?php echo $cand['id_offre']; ?>" class="btn-primary" style="flex: 1; text-align: center;">
                                Voir l'offre →
                            </a>
                            <button onclick="supprimerCandidature(<?php echo $cand['id']; ?>, '<?php echo htmlspecialchars($cand['titre']); ?>')" 
                                    class="btn-danger" style="flex: 1; text-align: center;">
                                🗑️ Supprimer
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="no-data">
                <p>📝 Vous n'avez pas encore envoyé de candidature.</p>
                <a href="index.php" class="btn-primary">
                    Parcourir les offres →
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>

<script>
function supprimerCandidature(candidatureId, titreOffre) {
    if (confirm('⚠️ Voulez-vous vraiment supprimer votre candidature pour le poste : "' + titreOffre + '" ?\n\nCette action est irréversible et supprimera définitivement votre candidature et votre CV.')) {
        // Désactiver le bouton pendant la suppression
        event.target.disabled = true;
        event.target.innerHTML = '⏳ Suppression...';
        
        // Envoyer la requête AJAX
        fetch('ajax_supprimer_candidature.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: 'candidature_id=' + candidatureId + '&csrf_token=<?php echo $_SESSION["csrf_token"]; ?>'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Succès: recharger la page
                alert('✅ ' + data.message);
                window.location.href = 'mon_espace.php?success=' + encodeURIComponent(data.message);
            } else {
                // Erreur: afficher le message
                alert('❌ ' + data.message);
                // Réactiver le bouton
                event.target.disabled = false;
                event.target.innerHTML = '🗑️ Supprimer';
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            alert('❌ Erreur lors de la suppression de la candidature');
            // Réactiver le bouton
            event.target.disabled = false;
            event.target.innerHTML = '🗑️ Supprimer';
        });
    }
}
</script>
