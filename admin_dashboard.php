<?php
require_once 'includes/header.php';
require_once 'includes/alerts.php';

// ===== PROTECTION : Seul l'admin (ID 1) peut accéder ====
if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] != 1) {
    http_response_code(403);
    echo "<div class='container'><div class='alert alert-error'>❌ Accès refusé. Réservé à l'administrateur.</div></div>";
    require_once 'includes/footer.php';
    exit();
}

// ===== RÉCUPÉRER LES CANDIDATURES (avec requête préparée) =====
try {
    $sql = "SELECT c.id, c.nom_cv, c.date_postulation, u.nom, u.prenom, j.titre
            FROM candidatures c
            JOIN users u ON c.id_utilisateur = u.id
            JOIN jobs j ON c.id_offre = j.id
            ORDER BY c.date_postulation DESC";
    
    $stmt = $db->prepare($sql);
    $stmt->execute();
    $candidatures = $stmt->fetchAll();
    
} catch (PDOException $e) {
    error_log("Erreur Admin: " . $e->getMessage());
    $candidatures = [];
}
?>

<div class="container">
    <!-- Afficher les alertes -->
    <?php echo displayAlerts(); ?>

    <div class="admin-header">
        <h1>📊 Tableau de Bord Admin</h1>
        <p>Gestion des candidatures reçues sur la plateforme Impact Emploi</p>
    </div>

    <div class="admin-stats">
        <div class="stat-card">
            <div class="stat-number"><?php echo count($candidatures); ?></div>
            <div class="stat-label">Candidatures</div>
        </div>
    </div>

    <div class="admin-section">
        <h2>📋 Liste des Candidatures</h2>
        
        <?php if (count($candidatures) > 0): ?>
            <div class="table-responsive">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Candidat</th>
                            <th>Poste visé</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($candidatures as $cand): ?>
                        <tr>
                            <td data-label="Date">
                                <?php echo date('d/m/Y H:i', strtotime($cand['date_postulation'])); ?>
                            </td>
                            <td data-label="Candidat">
                                <?php echo htmlspecialchars($cand['prenom'] . ' ' . $cand['nom']); ?>
                            </td>
                            <td data-label="Poste">
                                <?php echo htmlspecialchars($cand['titre']); ?>
                            </td>
                            <td data-label="Actions" class="actions-cell">
                                <a href="assets/uploads/cv/<?php echo htmlspecialchars($cand['nom_cv']); ?>" 
                                   target="_blank" 
                                   rel="noopener"
                                   class="btn-action btn-view"
                                   title="Voir le CV">
                                    📄 CV
                                </a>
                                <a href="scripts/delete_action.php?delete_cand=<?php echo $cand['id']; ?>" 
                                   onclick="return confirm('Supprimer cette candidature définitivement ? ⚠️');" 
                                   class="btn-action btn-delete"
                                   title="Supprimer">
                                    🗑️ Supprimer
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="no-data">
                <p>✓ Aucune candidature pour le moment.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
