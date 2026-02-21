<?php
require_once 'includes/header.php';
require_once 'includes/alerts.php';

// ===== PROTECTION =====
if (!isset($_SESSION['user_id'])) {
    header('Location: connexion.php?error=connexion');
    exit();
}

try {
    $id_user = intval($_SESSION['user_id']);

    // Récupérer les candidatures envoyées
    $sql_cand = "SELECT c.*, j.titre, j.lieu 
                 FROM candidatures c 
                 JOIN jobs j ON c.id_offre = j.id 
                 WHERE c.id_utilisateur = ? 
                 ORDER BY c.date_postulation DESC";
    $stmt_cand = $db->prepare($sql_cand);
    $stmt_cand->execute([$id_user]);
    $mes_candidatures = $stmt_cand->fetchAll();

} catch (PDOException $e) {
    error_log("Erreur Mon Espace: " . $e->getMessage());
    $mes_candidatures = [];
}
?>

<div class="container">
    <!-- Afficher les alertes -->
    <?php echo displayAlerts(); ?>

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
                            <?php echo date('d/m/Y H:i', strtotime($cand['date_postulation'])); ?>
                        </p>
                        <p class="meta">
                            <strong>📄 CV :</strong> 
                            <a href="assets/uploads/cv/<?php echo htmlspecialchars($cand['nom_cv']); ?>" 
                               target="_blank" rel="noopener" class="btn-secondary"
                               style="font-size:0.9rem; padding:0.5rem 1rem;">
                                📄 Voir mon CV
                            </a>
                        </p>
                        <a href="voir_offre.php?id=<?php echo $cand['id_offre']; ?>" class="btn-primary">
                            Voir l'offre →
                        </a>
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
