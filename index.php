<?php 
require_once 'config.php';
track_visitor($pdo, 'index.php');
include 'includes/header.php';

// Récupérer les offres d'emploi
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 9;
$offset = ($page - 1) * $per_page;

$jobs = $pdo->query("SELECT j.*, u.nom as entreprise FROM jobs j JOIN users u ON j.user_id = u.id ORDER BY j.date_publication DESC LIMIT $per_page OFFSET $offset")->fetchAll();
$total = $pdo->query("SELECT COUNT(*) FROM jobs")->fetchColumn();
$pages = ceil($total / $per_page);

// Statistiques
$stats = $pdo->query("SELECT 
    (SELECT COUNT(*) FROM jobs) as total_jobs,
    (SELECT COUNT(*) FROM users WHERE role='candidat') as candidats,
    (SELECT COUNT(*) FROM users WHERE role='recruteur') as recruteurs,
    (SELECT COUNT(*) FROM candidatures WHERE statut='Accepté') as acceptes
")->fetch();
?>

<!-- Hero Section -->
<section class="hero">
    <div class="container">
        <h1>Trouvez l'emploi de vos rêves</h1>
        <p>Reliez-vous avec les meilleures opportunités d'emploi</p>
        <?php if(!isset($_SESSION['auth_id'])): ?>
            <div style="margin-top: 30px;">
                <a href="<?php echo BASE_URL; ?>/register.php" class="btn btn-primary" style="background: white; color: var(--primary); font-weight: 700;">Commencer maintenant</a>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Statistiques -->
<section class="container mb-large">
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px;">
        <div class="card" style="text-align: center; border-left: 4px solid var(--primary);">
            <div style="font-size: 2.5rem; color: var(--primary); font-weight: 900;"><?php echo number_format($stats['total_jobs']); ?></div>
            <div style="color: var(--text-secondary); margin-top: 10px;">Offres d'emploi</div>
        </div>
        <div class="card" style="text-align: center; border-left: 4px solid var(--secondary);">
            <div style="font-size: 2.5rem; color: var(--secondary); font-weight: 900;"><?php echo number_format($stats['candidats']); ?></div>
            <div style="color: var(--text-secondary); margin-top: 10px;">Candidats</div>
        </div>
        <div class="card" style="text-align: center; border-left: 4px solid var(--success);">
            <div style="font-size: 2.5rem; color: var(--success); font-weight: 900;"><?php echo number_format($stats['recruteurs']); ?></div>
            <div style="color: var(--text-secondary); margin-top: 10px;">Recruteurs</div>
        </div>
        <div class="card" style="text-align: center; border-left: 4px solid var(--info);">
            <div style="font-size: 2.5rem; color: var(--info); font-weight: 900;"><?php echo number_format($stats['acceptes']); ?></div>
            <div style="color: var(--text-secondary); margin-top: 10px;">Placements</div>
        </div>
    </div>
</section>

<!-- Messages d'alerte -->
<div class="container">
    <?php if(isset($_GET['success'])): ?>
        <div class="alert alert-success">✓ <?php echo htmlspecialchars($_GET['success']); ?></div>
    <?php endif; ?>
    <?php if(isset($_GET['error'])): ?>
        <div class="alert alert-error">✕ <?php echo htmlspecialchars($_GET['error']); ?></div>
    <?php endif; ?>
</div>

<!-- Offres d'emploi -->
<section class="container">
    <h2 style="margin-bottom: 30px; font-size: 2rem; color: var(--text-primary);">
        🎯 Offres d'emploi en vedette
    </h2>
    
    <?php if(count($jobs) > 0): ?>
        <div class="grid-jobs">
            <?php foreach($jobs as $job): ?>
                <a href="<?php echo BASE_URL; ?>/job_detail.php?id=<?php echo $job['id']; ?>" style="text-decoration: none; color: inherit;">
                    <div class="card card-job" style="display: flex; flex-direction: column; overflow: hidden; cursor: pointer; transition: transform 0.3s, box-shadow 0.3s;" onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='0 10px 25px rgba(0,0,0,0.15)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow=''">
                        <!-- Image de l'offre -->
                        <?php if(!empty($job['image_offre'])): ?>
                            <div style="width: 100%; height: 250px; overflow: hidden; margin: -16px -16px 15px -16px; border-radius: 8px 8px 0 0; background: #f8f9fa;">
                                <img src="<?php echo BASE_URL; ?>/uploads/jobs/<?php echo htmlspecialchars($job['image_offre']); ?>" 
                                     alt="Image offre" class="job-photo" data-lightbox loading="lazy"
                                     style="width: 100%; height: 100%; object-fit: contain; object-position: center; transition: transform 0.3s ease;"
                                     onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
                            </div>
                        <?php endif; ?>
                        
                        <div style="flex: 1;">
                            <h3 style="margin-bottom: 10px; color: var(--primary); font-size: 1.2rem;">
                                <?php echo htmlspecialchars($job['titre']); ?>
                            </h3>
                            <p style="margin: 5px 0; color: var(--secondary); font-weight: 600;">
                                🏢 <?php echo htmlspecialchars($job['entreprise']); ?>
                            </p>
                            <p style="margin: 5px 0; color: var(--text-secondary);">
                                📍 <?php echo htmlspecialchars($job['lieu']); ?>
                            </p>
                            <p style="margin: 10px 0; color: var(--primary); font-weight: 700; font-size: 1.1rem;">
                                <?php echo number_format($job['salaire'], 0, ',', ' '); ?> FCFA
                            </p>
                            <p style="color: var(--text-secondary); font-size: 0.9rem; margin: 10px 0;">
                                <?php echo substr(htmlspecialchars($job['description']), 0, 100); ?>...
                            </p>
                            <div style="padding-top: 10px; border-top: 1px solid var(--border-color); margin-top: 10px;">
                                <small style="color: #9CA3AF;">
                                    📅 <?php echo date('d M Y', strtotime($job['date_publication'])); ?>
                                </small>
                            </div>
                        </div>
                        
                        <div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid var(--border-color);">
                            <span class="btn btn-primary btn-block" style="padding: 10px; display: inline-block; width: 100%; text-align: center;">
                                👁️ Voir l'offre complète
                            </span>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="card text-center" style="padding: 60px 20px;">
            <p style="font-size: 1.2rem; color: var(--text-secondary);">
                Aucune offre d'emploi disponible pour le moment. Revenez bientôt! 🔍
            </p>
        </div>
    <?php endif; ?>
</section>

<!-- Pagination -->
<?php if($pages > 1): ?>
    <section class="container" style="margin-top: 40px; text-align: center;">
        <div style="display: flex; justify-content: center; gap: 10px; flex-wrap: wrap;">
            <?php for($i = 1; $i <= $pages; $i++): ?>
                <a href="?page=<?php echo $i; ?>" class="btn <?php echo $page === $i ? 'btn-primary' : 'btn-outline'; ?> btn-small">
                    <?php echo $i; ?>
                </a>
            <?php endfor; ?>
        </div>
    </section>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>