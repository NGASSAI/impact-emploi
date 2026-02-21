<?php
require_once 'config.php';
track_visitor($pdo, 'recruteur_dashboard.php');

if($_SESSION['auth_role'] !== 'recruteur') { 
    header('Location: ' . BASE_URL . '/index.php'); 
    exit(); 
}
include 'includes/header.php';

$rec_id = $_SESSION['auth_id'];

// Récupérer les statistiques du recruteur
$stats = $pdo->query("SELECT 
    (SELECT COUNT(*) FROM jobs WHERE user_id = $rec_id) as total_offres,
    (SELECT COUNT(*) FROM candidatures WHERE recruteur_id = $rec_id) as total_candidatures,
    (SELECT COUNT(*) FROM candidatures WHERE recruteur_id = $rec_id AND statut = 'En attente') as candidatures_pending,
    (SELECT COUNT(*) FROM candidatures WHERE recruteur_id = $rec_id AND statut = 'Accepté') as candidatures_accepted
")->fetch();

// Récupérer les candidatures
$stmt = $pdo->prepare("SELECT c.id, u.nom, u.prenom, u.email, u.telephone, j.titre, c.nom_cv, c.statut, c.date_postulation, c.recruteur_message
                       FROM candidatures c 
                       JOIN users u ON c.id_utilisateur = u.id 
                       JOIN jobs j ON c.id_offre = j.id 
                       WHERE c.recruteur_id = ?
                       ORDER BY c.date_postulation DESC");
$stmt->execute([$rec_id]);
$candidatures = $stmt->fetchAll();

// Récupérer les offres du recruteur
$jobs_stmt = $pdo->prepare("SELECT * FROM jobs WHERE user_id = ? ORDER BY date_publication DESC");
$jobs_stmt->execute([$rec_id]);
$jobs = $jobs_stmt->fetchAll();
?>

<div class="container">
    <!-- En-tête -->
    <div style="margin-bottom: 40px;">
        <h1 style="font-size: 2.5rem; color: var(--primary); margin-bottom: 10px;">
            👥 Tableau de Bord Recruteur
        </h1>
        <p class="text-muted" style="font-size: 1.1rem;">Gérez vos offres d'emploi et vos candidatures</p>
    </div>

    <!-- Statistiques -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; margin-bottom: 40px;">
        <div class="card" style="border-left: 4px solid var(--primary);">
            <p class="text-muted" style="margin-bottom: 8px;">Offres d'Emploi</p>
            <p style="font-size: 2.5rem; color: var(--primary); font-weight: 900;">
                <?php echo $stats['total_offres']; ?>
            </p>
        </div>
        <div class="card" style="border-left: 4px solid var(--secondary);">
            <p class="text-muted" style="margin-bottom: 8px;">Candidatures Total</p>
            <p style="font-size: 2.5rem; color: var(--secondary); font-weight: 900;">
                <?php echo $stats['total_candidatures']; ?>
            </p>
        </div>
        <div class="card" style="border-left: 4px solid var(--warning);">
            <p class="text-muted" style="margin-bottom: 8px;">En Attente</p>
            <p style="font-size: 2.5rem; color: var(--warning); font-weight: 900;">
                <?php echo $stats['candidatures_pending']; ?>
            </p>
        </div>
        <div class="card" style="border-left: 4px solid var(--success);">
            <p class="text-muted" style="margin-bottom: 8px;">Acceptées</p>
            <p style="font-size: 2.5rem; color: var(--success); font-weight: 900;">
                <?php echo $stats['candidatures_accepted']; ?>
            </p>
        </div>
    </div>

    <!-- Candidatures -->
    <div class="card" style="margin-bottom: 40px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h3 style="color: var(--primary);">📊 Candidatures Reçues</h3>
            <span class="badge badge-primary"><?php echo count($candidatures); ?> candidat(s)</span>
        </div>

        <?php if(count($candidatures) > 0): ?>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Candidat</th>
                            <th>Contact</th>
                            <th>Poste</th>
                            <th>Statut</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($candidatures as $c): ?>
                            <tr>
                                <td>
                                    <strong><?php echo htmlspecialchars($c['prenom'] . ' ' . $c['nom']); ?></strong>
                                </td>
                                <td>
                                    <div style="font-size: 0.9rem;">
                                        📧 <?php echo htmlspecialchars($c['email']); ?><br>
                                        📱 <?php echo htmlspecialchars($c['telephone']); ?>
                                    </div>
                                </td>
                                <td><?php echo htmlspecialchars($c['titre']); ?></td>
                                <td>
                                    <span class="badge <?php 
                                        echo $c['statut'] === 'Accepté' ? 'badge-success' : 
                                        ($c['statut'] === 'Refusé' ? 'badge-danger' : 'badge-warning');
                                    ?>">
                                        <?php echo htmlspecialchars($c['statut']); ?>
                                    </span>
                                </td>
                                <td><?php echo date('d/m/Y', strtotime($c['date_postulation'])); ?></td>
                                <td>
                                    <a href="<?php echo BASE_URL; ?>/uploads/cv/<?php echo htmlspecialchars($c['nom_cv']); ?>" target="_blank" class="btn btn-primary btn-small">📄 CV</a>
                                    <a href="<?php echo BASE_URL; ?>/chat.php?id=<?php echo $c['id']; ?>" class="btn btn-secondary btn-small">💬 Répondre</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div style="text-align: center; padding: 40px; color: var(--text-secondary);">
                <p style="font-size: 1.2rem;">Aucune candidature reçue pour le moment 📭</p>
            </div>
        <?php endif; ?>
    </div>

    <!-- Offres d'emploi -->
    <div class="card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h3 style="color: var(--primary);">💼 Mes Offres d'Emploi</h3>
            <a href="<?php echo BASE_URL; ?>/create_job.php" class="btn btn-primary btn-small">➕ Créer une offre</a>
        </div>

        <?php if(count($jobs) > 0): ?>
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px;">
                <?php foreach($jobs as $job): ?>
                    <div class="card" style="border-left: 4px solid var(--secondary); display: flex; flex-direction: column;">
                        <?php if(!empty($job['image_offre'])): ?>
                            <img src="<?php echo BASE_URL; ?>/uploads/jobs/<?php echo htmlspecialchars($job['image_offre']); ?>" alt="Image offre" data-lightbox loading="lazy" style="width: 100%; height: 150px; object-fit: cover; border-radius: 8px; margin-bottom: 15px; cursor: pointer;">
                        <?php endif; ?>
                        <h4 style="color: var(--primary); margin-bottom: 10px;">
                            <?php echo htmlspecialchars($job['titre']); ?>
                        </h4>
                        <p class="text-muted" style="margin: 8px 0;">
                            📍 <?php echo htmlspecialchars($job['lieu']); ?>
                        </p>
                        <p style="color: var(--secondary); font-weight: 700; margin: 8px 0;">
                            <?php echo $job['salaire'] > 0 ? number_format($job['salaire'], 0, ',', ' ') . ' FCFA' : 'Salaire non spécifié'; ?>
                        </p>
                        <p class="text-muted" style="font-size: 0.85rem; line-height: 1.4; margin: 10px 0; flex: 1;">
                            <?php echo !empty($job['description']) ? substr(htmlspecialchars($job['description']), 0, 80) . '...' : 'Pas de description'; ?>
                        </p>
                        <div style="padding-top: 10px; border-top: 1px solid var(--border-color); margin-top: 10px;">
                            <small class="text-muted">
                                📅 <?php echo date('d M Y', strtotime($job['date_publication'])); ?>
                            </small>
                        </div>
                        <div style="margin-top: 10px; display: flex; gap: 8px;">
                            <a href="<?php echo BASE_URL; ?>/edit_job.php?id=<?php echo $job['id']; ?>" class="btn btn-small btn-outline" style="flex: 1; text-align: center;">✏️ Éditer</a>
                            <a href="<?php echo BASE_URL; ?>/delete_job.php?id=<?php echo $job['id']; ?>" class="btn btn-small btn-danger" style="flex: 1; text-align: center;" onclick="return confirm('Confirmer?');">🗑️ Supprimer</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div style="text-align: center; padding: 40px; color: var(--text-secondary);">
                <p style="font-size: 1.2rem;">Vous n'avez pas encore créé d'offre d'emploi 📋</p>
                <a href="<?php echo BASE_URL; ?>/create_job.php" class="btn btn-primary" style="margin-top: 20px;">Créer votre première offre</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>