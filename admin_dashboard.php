<?php
require_once 'config.php';
if(!is_admin()) { 
    header('Location: ' . BASE_URL . '/index.php'); 
    exit(); 
}
track_visitor($pdo, 'admin_dashboard.php');
include 'includes/header.php';

// Récupérer les statistiques (protection si certaines tables manquent)
try {
    $stats = $pdo->query("SELECT 
        (SELECT COUNT(*) FROM users) as total_users,
        (SELECT COUNT(*) FROM users WHERE role='candidat') as total_candidats,
        (SELECT COUNT(*) FROM users WHERE role='recruteur') as total_recruteurs,
        (SELECT COUNT(*) FROM users WHERE is_blocked=1) as users_blocked,
        (SELECT COUNT(*) FROM jobs) as total_jobs,
        (SELECT COUNT(*) FROM candidatures) as total_candidatures,
        (SELECT COUNT(*) FROM candidatures WHERE statut='En attente') as candidatures_pending,
        (SELECT COUNT(*) FROM candidatures WHERE statut='Accepté') as candidatures_accepted,
        (SELECT COUNT(*) FROM candidatures WHERE statut='Refusé') as candidatures_refused,
        (SELECT COUNT(*) FROM feedbacks) as total_feedbacks
    ")->fetch();
} catch (PDOException $e) {
    $stats = [
        'total_users' => 0,
        'total_candidats' => 0,
        'total_recruteurs' => 0,
        'users_blocked' => 0,
        'total_jobs' => 0,
        'total_candidatures' => 0,
        'candidatures_pending' => 0,
        'candidatures_accepted' => 0,
        'candidatures_refused' => 0,
        'total_feedbacks' => 0,
    ];
}

// Récupérer les utilisateurs récents (protection si table users manquante)
try {
    $recent_users = $pdo->query("SELECT id, nom, prenom, email, role, is_blocked FROM users ORDER BY id DESC LIMIT 10")->fetchAll();
} catch (PDOException $e) {
    $recent_users = [];
}

// Récupérer les activités récentes (protection si activity_logs manquante)
try {
    $recent_activities = $pdo->query("SELECT u.nom, u.prenom, a.action, a.description, a.created_at 
                                   FROM activity_logs a 
                                   LEFT JOIN users u ON a.user_id = u.id 
                                   ORDER BY a.created_at DESC LIMIT 15")->fetchAll();
} catch (PDOException $e) {
    $recent_activities = [];
}

// Récupérer les candidatures récentes (protection si tables manquent)
try {
    $recent_candidatures = $pdo->query("SELECT c.id, u.nom, u.prenom, j.titre, c.statut, c.date_postulation 
                                    FROM candidatures c 
                                    JOIN users u ON c.id_utilisateur = u.id 
                                    JOIN jobs j ON c.id_offre = j.id 
                                    ORDER BY c.date_postulation DESC LIMIT 8")->fetchAll();
} catch (PDOException $e) {
    $recent_candidatures = [];
}
?>

<div class="container">
    <!-- En-tête du tableau de bord -->
    <div style="margin-bottom: 40px;">
        <h1 style="font-size: 2.5rem; color: var(--primary); margin-bottom: 10px;">
            📊 Tableau de Bord Administrateur
        </h1>
        <p class="text-muted" style="font-size: 1.1rem;">Supervisez tous les paramètres du site Impact Emploi</p>
    </div>

    <!-- Cartes statistiques principales -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 40px;">
        
        <!-- Utilisateurs totaux -->
        <div class="card" style="border-left: 4px solid var(--primary);">
            <div style="display: flex; justify-content: space-between; align-items: start;">
                <div>
                    <p class="text-muted" style="margin-bottom: 8px;">Utilisateurs Totaux</p>
                    <p style="font-size: 2.5rem; color: var(--primary); font-weight: 900; margin-bottom: 10px;">
                        <?php echo number_format($stats['total_users']); ?>
                    </p>
                </div>
                <span style="font-size: 2.5rem;">👥</span>
            </div>
            <a href="#users" class="btn btn-primary btn-small">Gérer</a>
        </div>

        <!-- Candidats -->
        <div class="card" style="border-left: 4px solid var(--success);">
            <div style="display: flex; justify-content: space-between; align-items: start;">
                <div>
                    <p class="text-muted" style="margin-bottom: 8px;">Candidats Actifs</p>
                    <p style="font-size: 2.5rem; color: var(--success); font-weight: 900; margin-bottom: 10px;">
                        <?php echo number_format($stats['total_candidats']); ?>
                    </p>
                </div>
                <span style="font-size: 2.5rem;">🎓</span>
            </div>
        </div>

        <!-- Recruteurs -->
        <div class="card" style="border-left: 4px solid var(--info);">
            <div style="display: flex; justify-content: space-between; align-items: start;">
                <div>
                    <p class="text-muted" style="margin-bottom: 8px;">Recruteurs</p>
                    <p style="font-size: 2.5rem; color: var(--info); font-weight: 900; margin-bottom: 10px;">
                        <?php echo number_format($stats['total_recruteurs']); ?>
                    </p>
                </div>
                <span style="font-size: 2.5rem;">🏢</span>
            </div>
        </div>

        <!-- Offres d'emploi -->
        <div class="card" style="border-left: 4px solid var(--secondary);">
            <div style="display: flex; justify-content: space-between; align-items: start;">
                <div>
                    <p class="text-muted" style="margin-bottom: 8px;">Offres d'Emploi</p>
                    <p style="font-size: 2.5rem; color: var(--secondary); font-weight: 900; margin-bottom: 10px;">
                        <?php echo number_format($stats['total_jobs']); ?>
                    </p>
                </div>
                <span style="font-size: 2.5rem;">💼</span>
            </div>
        </div>

        <!-- Candidatures -->
        <div class="card" style="border-left: 4px solid var(--warning);">
            <div style="display: flex; justify-content: space-between; align-items: start;">
                <div>
                    <p class="text-muted" style="margin-bottom: 8px;">Candidatures</p>
                    <p style="font-size: 2.5rem; color: var(--warning); font-weight: 900; margin-bottom: 10px;">
                        <?php echo number_format($stats['total_candidatures']); ?>
                    </p>
                </div>
                <span style="font-size: 2.5rem;">📤</span>
            </div>
        </div>

        <!-- Comptes bloqués -->
        <div class="card" style="border-left: 4px solid var(--danger);">
            <div style="display: flex; justify-content: space-between; align-items: start;">
                <div>
                    <p class="text-muted" style="margin-bottom: 8px;">Comptes Bloqués</p>
                    <p style="font-size: 2.5rem; color: var(--danger); font-weight: 900; margin-bottom: 10px;">
                        <?php echo number_format($stats['users_blocked']); ?>
                    </p>
                </div>
                <span style="font-size: 2.5rem;">🔒</span>
            </div>
        </div>

        <!-- Feedbacks -->
        <div class="card" style="border-left: 4px solid var(--info);">
            <div style="display: flex; justify-content: space-between; align-items: start;">
                <div>
                    <p class="text-muted" style="margin-bottom: 8px;">Feedbacks Reçus</p>
                    <p style="font-size: 2.5rem; color: var(--info); font-weight: 900; margin-bottom: 10px;">
                        <?php echo number_format($stats['total_feedbacks']); ?>
                    </p>
                </div>
                <span style="font-size: 2.5rem;">💬</span>
            </div>
            <a href="<?php echo BASE_URL; ?>/admin_feedbacks.php" class="btn btn-primary btn-small">Consulter</a>
        </div>
    </div>

    <!-- Sections détaillées -->
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 25px; margin-bottom: 40px;">
        <!-- Statut des candidatures -->
        <div class="card">
            <h3 style="color: var(--primary); margin-bottom: 20px;">📊 Statut des Candidatures</h3>
            <div style="display: flex; flex-direction: column; gap: 15px;">
                <div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                        <span>⏳ En attente</span>
                        <strong style="color: var(--warning);"><?php echo $stats['candidatures_pending']; ?></strong>
                    </div>
                    <div style="width: 100%; height: 8px; background: var(--light); border-radius: 4px; overflow: hidden;">
                        <div style="height: 100%; width: <?php echo ($stats['candidatures_pending'] / max($stats['total_candidatures'], 1) * 100); ?>%; background: var(--warning);"></div>
                    </div>
                </div>
                <div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                        <span>✅ Acceptées</span>
                        <strong style="color: var(--success);"><?php echo $stats['candidatures_accepted']; ?></strong>
                    </div>
                    <div style="width: 100%; height: 8px; background: var(--light); border-radius: 4px; overflow: hidden;">
                        <div style="height: 100%; width: <?php echo ($stats['candidatures_accepted'] / max($stats['total_candidatures'], 1) * 100); ?>%; background: var(--success);"></div>
                    </div>
                </div>
                <div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                        <span>❌ Refusées</span>
                        <strong style="color: var(--danger);"><?php echo $stats['candidatures_refused']; ?></strong>
                    </div>
                    <div style="width: 100%; height: 8px; background: var(--light); border-radius: 4px; overflow: hidden;">
                        <div style="height: 100%; width: <?php echo ($stats['candidatures_refused'] / max($stats['total_candidatures'], 1) * 100); ?>%; background: var(--danger);"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Répartition des utilisateurs -->
        <div class="card">
            <h3 style="color: var(--primary); margin-bottom: 20px;">👥 Répartition des Utilisateurs</h3>
            <div style="display: flex; justify-content: space-around; align-items: center; padding: 20px 0;">
                <div style="text-align: center;">
                    <div style="font-size: 3rem; color: var(--success);"><?php echo $stats['total_candidats']; ?></div>
                    <div class="text-muted" style="margin-top: 8px;">Candidats</div>
                </div>
                <div style="width: 1px; height: 60px; background: var(--border-color);"></div>
                <div style="text-align: center;">
                    <div style="font-size: 3rem; color: var(--info);"><?php echo $stats['total_recruteurs']; ?></div>
                    <div class="text-muted" style="margin-top: 8px;">Recruteurs</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Candidatures récentes -->
    <div class="card" style="margin-bottom: 40px;">
        <h3 style="color: var(--primary); margin-bottom: 20px;">📋 Candidatures Récentes</h3>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Candidat</th>
                        <th>Poste</th>
                        <th>Statut</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(count($recent_candidatures) > 0): ?>
                        <?php foreach($recent_candidatures as $c): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($c['prenom'] . ' ' . $c['nom']); ?></td>
                                <td><?php echo htmlspecialchars($c['titre']); ?></td>
                                <td>
                                    <span class="badge <?php 
                                        echo $c['statut'] === 'Accepté' ? 'badge-success' : 
                                        ($c['statut'] === 'Refusé' ? 'badge-danger' : 'badge-warning');
                                    ?>">
                                        <?php echo htmlspecialchars($c['statut']); ?>
                                    </span>
                                </td>
                                <td><?php echo format_congo_date($c['date_postulation']); ?></td>
                                <td style="text-align: center;">
                                    <button onclick="supprimerCandidatureAdmin(<?php echo $c['id']; ?>, '<?php echo htmlspecialchars($c['prenom'] . ' ' . $c['nom']); ?>', '<?php echo htmlspecialchars($c['titre']); ?>')" 
                                            class="btn-danger" style="padding: 6px 12px; font-size: 0.85rem;">
                                        🗑️ Supprimer
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" style="text-align: center; color: var(--text-secondary);">Aucune candidature</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Utilisateurs récents -->
    <div class="card" style="margin-bottom: 40px;">
        <h3 style="color: var(--primary); margin-bottom: 20px;" id="users">👤 Utilisateurs</h3>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 15px; margin-bottom: 20px;">
            <div style="padding: 15px; border-left: 4px solid var(--primary); background: var(--light); border-radius: 4px; text-align: center;">
                <div style="font-size: 1.8rem; color: var(--primary); font-weight: bold;">
                    <?php
                    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users");
                    $stmt->execute();
                    echo $stmt->fetchColumn();
                    ?>
                </div>
                <small style="color: var(--text-secondary);">Total</small>
            </div>
            <div style="padding: 15px; border-left: 4px solid var(--info); background: var(--light); border-radius: 4px; text-align: center;">
                <div style="font-size: 1.8rem; color: var(--info); font-weight: bold;">
                    <?php
                    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE is_blocked=1");
                    $stmt->execute();
                    echo $stmt->fetchColumn();
                    ?>
                </div>
                <small style="color: var(--text-secondary);">Bloqués</small>
            </div>
            <div style="padding: 15px; border-left: 4px solid var(--success); background: var(--light); border-radius: 4px; text-align: center;">
                <div style="font-size: 1.8rem; color: var(--success); font-weight: bold;">
                    <?php
                    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE role='candidate'");
                    $stmt->execute();
                    echo $stmt->fetchColumn();
                    ?>
                </div>
                <small style="color: var(--text-secondary);">Candidats</small>
            </div>
        </div>
        <a href="<?php echo BASE_URL; ?>/admin_users.php" class="btn btn-primary btn-block" style="padding: 15px 20px; font-size: 1rem; font-weight: bold;">
            ⚙️ Gérer tous les utilisateurs →
        </a>
    </div>

    <!-- Activités récentes -->
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 25px; margin-bottom: 40px;">
        <!-- Activités -->
        <div class="card">
            <h3 style="color: var(--primary); margin-bottom: 20px;">📝 Activités Récentes</h3>
            <div style="display: flex; flex-direction: column; gap: 12px; max-height: 400px; overflow-y: auto;">
                <?php if(count($recent_activities) > 0): ?>
                    <?php $activity_count = 0; foreach($recent_activities as $activity): if($activity_count >= 5) break; $activity_count++; ?>
                        <div style="padding: 12px; border-left: 4px solid var(--primary); background: var(--light); border-radius: 4px; font-size: 0.85rem;">
                            <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 5px; gap: 10px;">
                                <strong style="font-size: 0.85rem;">
                                    <?php echo htmlspecialchars(substr($activity['prenom'] . ' ' . $activity['nom'] ?? 'Système', 0, 15)); ?>
                                </strong>
                                <small class="text-muted" style="white-space: nowrap; font-size: 0.8rem;"><?php echo isset($activity['created_at']) && $activity['created_at'] ? format_congo_date($activity['created_at']) : 'N/A'; ?></small>
                            </div>
                            <p style="margin: 0; color: var(--text-secondary); font-size: 0.8rem;">
                                <strong><?php echo htmlspecialchars($activity['action']); ?></strong>
                                <?php if($activity['description']): ?>
                                    <br><small><?php echo htmlspecialchars(substr($activity['description'], 0, 25)); ?></small>
                                <?php endif; ?>
                            </p>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-muted" style="text-align: center; padding: 20px 0; font-size: 0.9rem;">Aucune activité</p>
                <?php endif; ?>
            </div>
            <a href="<?php echo BASE_URL; ?>/admin_activities.php" class="btn btn-primary btn-block" style="margin-top: 12px; font-size: 0.9rem;">
                Voir toutes les activités →
            </a>
        </div>

        <!-- Visiteurs -->
        <div class="card">
            <h3 style="color: var(--primary); margin-bottom: 20px;">👁️ Visiteurs</h3>
            <div style="display: flex; flex-direction: column; gap: 15px; min-height: 300px;">
                <?php 
                try {
                    $visitor_count = $pdo->query("SELECT COUNT(*) FROM visitors WHERE is_bot = 0")->fetchColumn();
                    $unique_ips = $pdo->query("SELECT COUNT(DISTINCT ip_address) FROM visitors WHERE is_bot = 0")->fetchColumn();
                    $registered_visitors = $pdo->query("SELECT COUNT(*) FROM visitors WHERE user_id IS NOT NULL AND is_bot = 0")->fetchColumn();
                } catch (PDOException $e) {
                    $visitor_count = 0;
                    $unique_ips = 0;
                    $registered_visitors = 0;
                }
                ?>
                <div style="padding: 15px; border-left: 4px solid var(--info); background: var(--light); border-radius: 4px;">
                    <div style="display: flex; justify-content: space-between; align-items: start;">
                        <div>
                            <small class="text-muted">Total visiteurs</small>
                            <div style="font-size: 1.5rem; color: var(--info); font-weight: bold;"><?php echo number_format($visitor_count); ?></div>
                        </div>
                    </div>
                </div>
                <div style="padding: 15px; border-left: 4px solid var(--secondary); background: var(--light); border-radius: 4px;">
                    <div style="display: flex; justify-content: space-between; align-items: start;">
                        <div>
                            <small class="text-muted">IPs uniques</small>
                            <div style="font-size: 1.5rem; color: var(--secondary); font-weight: bold;"><?php echo number_format($unique_ips); ?></div>
                        </div>
                    </div>
                </div>
                <div style="padding: 15px; border-left: 4px solid var(--success); background: var(--light); border-radius: 4px;">
                    <div style="display: flex; justify-content: space-between; align-items: start;">
                        <div>
                            <small class="text-muted">Visiteurs inscrits</small>
                            <div style="font-size: 1.5rem; color: var(--success); font-weight: bold;"><?php echo number_format($registered_visitors); ?></div>
                        </div>
                    </div>
                </div>
            </div>
            <a href="<?php echo BASE_URL; ?>/admin_visitors.php" class="btn btn-primary btn-block" style="margin-top: 15px;">
                Voir tous les visiteurs →
            </a>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

<script>
function supprimerCandidatureAdmin(candidatureId, nomCandidat, titreOffre) {
    if (confirm('⚠️ Voulez-vous vraiment supprimer la candidature de "' + nomCandidat + '" pour le poste : "' + titreOffre + '" ?\n\nCette action est irréversible et supprimera définitivement la candidature et le CV du candidat.')) {
        // Désactiver le bouton pendant la suppression
        event.target.disabled = true;
        event.target.innerHTML = '⏳ Suppression...';
        
        // Envoyer la requête AJAX
        fetch('ajax_supprimer_candidature_admin.php', {
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
                window.location.href = 'admin_dashboard.php?success=' + encodeURIComponent(data.message);
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