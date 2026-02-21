<?php
require_once 'config.php';

// Vérifier que c'est un candidat
if($_SESSION['auth_role'] !== 'candidat') {
    header('Location: ' . BASE_URL . '/index.php');
    exit();
}

// Récupérer les statistiques des candidatures du candidat
$user_id = $_SESSION['auth_id'];
$sql = "SELECT 
    (SELECT COUNT(*) FROM candidatures WHERE id_utilisateur = ?) as total,
    (SELECT COUNT(*) FROM candidatures WHERE id_utilisateur = ? AND statut = 'En attente') as pending,
    (SELECT COUNT(*) FROM candidatures WHERE id_utilisateur = ? AND statut = 'Accepté') as accepted,
    (SELECT COUNT(*) FROM candidatures WHERE id_utilisateur = ? AND statut = 'Refusé') as refused";
$stmt = $pdo->prepare($sql);
$stmt->execute([$user_id, $user_id, $user_id, $user_id]);
$stats = $stmt->fetch(PDO::FETCH_ASSOC);

// Récupérer les candidatures du candidat
$sql = "SELECT c.id, c.statut, c.date_postulation, c.recruteur_message, j.titre, j.lieu, j.salaire, u.nom as entreprise
        FROM candidatures c 
        JOIN jobs j ON c.id_offre = j.id 
        LEFT JOIN users u ON j.user_id = u.id 
        WHERE c.id_utilisateur = ? 
        ORDER BY c.date_postulation DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute([$user_id]);
$candidatures = $stmt->fetchAll(PDO::FETCH_ASSOC);

include 'includes/header.php';
?>

<div class="container" style="padding: 40px 0;">
    <!-- En-tête du tableau de bord -->
    <div style="margin-bottom: 40px;">
        <h1 style="font-size: 2.5rem; color: var(--primary); margin-bottom: 10px;">
            📋 Mes Candidatures
        </h1>
        <p class="text-muted" style="font-size: 1.1rem;">Suivi de vos candidatures et réponses des recruteurs</p>
    </div>

    <!-- Statistiques -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 40px;">
        
        <!-- Total -->
        <div class="card" style="border-left: 4px solid var(--primary);">
            <p class="text-muted" style="margin-bottom: 8px;">Total Candidatures</p>
            <p style="font-size: 2.5rem; color: var(--primary); font-weight: 900; margin: 0;">
                <?php echo $stats['total']; ?>
            </p>
        </div>

        <!-- En attente -->
        <div class="card" style="border-left: 4px solid var(--warning);">
            <p class="text-muted" style="margin-bottom: 8px;">En Attente</p>
            <p style="font-size: 2.5rem; color: var(--warning); font-weight: 900; margin: 0;">
                <?php echo $stats['pending']; ?>
            </p>
        </div>

        <!-- Acceptées -->
        <div class="card" style="border-left: 4px solid var(--success);">
            <p class="text-muted" style="margin-bottom: 8px;">Acceptées</p>
            <p style="font-size: 2.5rem; color: var(--success); font-weight: 900; margin: 0;">
                <?php echo $stats['accepted']; ?>
            </p>
        </div>

        <!-- Refusées -->
        <div class="card" style="border-left: 4px solid var(--danger);">
            <p class="text-muted" style="margin-bottom: 8px;">Refusées</p>
            <p style="font-size: 2.5rem; color: var(--danger); font-weight: 900; margin: 0;">
                <?php echo $stats['refused']; ?>
            </p>
        </div>
    </div>

    <!-- Candidatures -->
    <div class="card">
        <h2 style="color: var(--primary); margin-top: 0;">📊 Vos Candidatures</h2>

        <?php if(count($candidatures) > 0): ?>
            <div style="display: grid; gap: 20px;">
                <?php foreach($candidatures as $c): ?>
                    <div style="padding: 20px; background: #f9f9f9; border-radius: 8px; border-left: 4px solid 
                        <?php 
                        if($c['statut'] === 'Accepté') echo 'var(--success)';
                        elseif($c['statut'] === 'Refusé') echo 'var(--danger)';
                        else echo 'var(--warning)';
                        ?>">
                        
                        <div style="display: grid; grid-template-columns: 1fr auto; gap: 15px; margin-bottom: 15px;">
                            <div>
                                <h3 style="margin: 0 0 5px 0; color: var(--primary); font-size: 1.2rem;">
                                    <?php echo htmlspecialchars($c['titre']); ?>
                                </h3>
                                <p style="margin: 5px 0; color: #666;">
                                    🏢 <?php echo htmlspecialchars($c['entreprise']); ?>
                                </p>
                                <p style="margin: 5px 0; color: #666;">
                                    📍 <?php echo htmlspecialchars($c['lieu']); ?> | 💰 <?php echo number_format($c['salaire'], 0, ',', ' '); ?> FCFA
                                </p>
                            </div>
                            <div style="text-align: right;">
                                <span style="display: inline-block; padding: 8px 12px; border-radius: 20px; font-weight: 600; font-size: 0.9rem;
                                    <?php 
                                    if($c['statut'] === 'Accepté') echo 'background: #d4edda; color: #155724;';
                                    elseif($c['statut'] === 'Refusé') echo 'background: #f8d7da; color: #721c24;';
                                    else echo 'background: #fff3cd; color: #856404;';
                                    ?>">
                                    <?php echo $c['statut']; ?>
                                </span>
                            </div>
                        </div>

                        <p style="margin: 10px 0; color: #999; font-size: 0.9rem;">
                            📅 Candidature du <?php echo date('d/m/Y à H:i', strtotime($c['date_postulation'])); ?>
                        </p>

                        <!-- Message du recruteur -->
                        <?php if(!empty($c['recruteur_message'])): ?>
                            <div style="margin-top: 15px; padding: 15px; background: white; border-radius: 6px; border-left: 4px solid var(--primary);">
                                <p style="margin: 0 0 10px 0; color: var(--primary); font-weight: 600;">💬 Message du recruteur</p>
                                <p style="margin: 0; color: var(--text-secondary); line-height: 1.6;">
                                    <?php echo nl2br(htmlspecialchars($c['recruteur_message'])); ?>
                                </p>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div style="padding: 60px 20px; text-align: center; background: #f5f5f5; border-radius: 8px;">
                <p style="font-size: 1.2rem; color: var(--text-secondary); margin: 0;">
                    Vous n'avez pas encore candidaté 📝
                </p>
                <a href="<?php echo BASE_URL; ?>/index.php" class="btn btn-primary" style="margin-top: 15px;">
                    Parcourir les offres d'emploi
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
