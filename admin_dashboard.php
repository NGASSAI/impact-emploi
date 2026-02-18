<?php
require_once 'includes/header.php';
require_once 'includes/alerts.php';

// ===== PROTECTION : Accès réservé aux administrateurs =====
// Nouvelle logique : on autorise si
// - l'utilisateur est connecté ET a le rôle 'admin' (champ `role` en base)
// - OU l'email de l'utilisateur correspond à DEFAULT_ADMIN_EMAIL (constante dans config)
$allow_admin = false;
if (isset($_SESSION['user_id'])) {
    // Si le rôle est présent en session et vaut 'admin'
    if (!empty($_SESSION['role']) && $_SESSION['role'] === 'admin') {
        $allow_admin = true;
    } else {
        // Récupérer l'email/role depuis la base pour confirmer
        try {
            $check = $db->prepare('SELECT email, role FROM users WHERE id = ?');
            $check->execute([$_SESSION['user_id']]);
            $u = $check->fetch();
            if ($u) {
                if (isset($u['role']) && $u['role'] === 'admin') {
                    $allow_admin = true;
                }
                if (defined('DEFAULT_ADMIN_EMAIL') && $u['email'] === DEFAULT_ADMIN_EMAIL) {
                    $allow_admin = true;
                }
            }
        } catch (Exception $e) {
            error_log('Admin access check failed: ' . $e->getMessage());
        }
    }
}

if (!$allow_admin) {
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
