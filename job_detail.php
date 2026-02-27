<?php
require_once 'config.php';
track_visitor($pdo, 'job_detail.php');

$id = isset($_GET['id']) ? (int)$_GET['id'] : null;

if(!$id) {
    header('Location: ' . BASE_URL . '/index.php');
    exit();
}

// Récupérer l'offre avec les infos du recruteur
$sql = "SELECT j.*, u.nom as entreprise, u.telephone, u.email FROM jobs j 
        LEFT JOIN users u ON j.user_id = u.id 
        WHERE j.id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id]);
$job = $stmt->fetch();

if(!$job) {
    header('Location: ' . BASE_URL . '/index.php?error=Offre non trouvée');
    exit();
}

include 'includes/header.php';
?>

<style>
    /* Responsive design pour job_detail.php */
    @media (max-width: 768px) {
        .job-detail-grid {
            display: grid !important;
            grid-template-columns: 1fr !important;
            gap: 20px !important;
        }
        .job-detail-sidebar {
            position: static !important;
            top: auto !important;
        }
    }
    
    @media (max-width: 480px) {
        .job-detail-header {
            font-size: 1.5rem !important;
            margin-bottom: 10px !important;
        }
        .job-detail-grid-stats {
            grid-template-columns: 1fr !important;
            gap: 10px !important;
            padding: 15px !important;
        }
        .job-detail-grid-stats p {
            font-size: 0.9rem !important;
        }
    }
</style>

<div class="container" style="padding: 20px 0;">
    <!-- Retour -->
    <a href="<?php echo BASE_URL; ?>/index.php" style="color: var(--primary); text-decoration: none; margin-bottom: 15px; display: inline-block; font-size: 0.95rem;">
        ← Retour aux offres
    </a>

    <div class="job-detail-grid" style="display: grid; grid-template-columns: 2fr 1fr; gap: 30px; margin-bottom: 40px;">
        <!-- Contenu principal -->
        <div>
            <!-- Image -->
            <?php if(!empty($job['image_offre'])): ?>
                <img src="<?php echo BASE_URL; ?>/uploads/jobs/<?php echo htmlspecialchars($job['image_offre']); ?>"
                     alt="Image offre" class="job-photo" data-lightbox loading="lazy" style="width: 100%; height: 250px; object-fit: cover; border-radius: 8px; margin-bottom: 25px; cursor: pointer;">
            <?php endif; ?>

            <!-- Titre et infos principales -->
            <h1 class="job-detail-header" style="font-size: 2.2rem; color: var(--primary); margin-bottom: 15px;">
                <?php echo htmlspecialchars($job['titre']); ?>
            </h1>

            <div class="job-detail-grid-stats" style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; margin-bottom: 30px; padding: 20px; background: #f5f5f5; border-radius: 8px;">
                <div>
                    <p style="color: #666; font-size: 0.9rem; margin: 0;">Entreprise</p>
                    <p style="font-size: 1.1rem; color: var(--primary); font-weight: 600; margin: 5px 0;">🏢 <?php echo htmlspecialchars($job['entreprise'] ?? 'Entreprise'); ?></p>
                </div>
                <div>
                    <p style="color: #666; font-size: 0.9rem; margin: 0;">Localité</p>
                    <p style="font-size: 1.1rem; color: var(--primary); font-weight: 600; margin: 5px 0;">📍 <?php echo htmlspecialchars($job['lieu']); ?></p>
                </div>
                <div>
                    <p style="color: #666; font-size: 0.9rem; margin: 0;">Salaire</p>
                    <p style="font-size: 1.1rem; color: var(--primary); font-weight: 600; margin: 5px 0;">💰 <?php echo number_format($job['salaire'], 0, ',', ' '); ?> FCFA</p>
                </div>
                <div>
                    <p style="color: #666; font-size: 0.9rem; margin: 0;">Type de contrat</p>
                    <p style="font-size: 1.1rem; color: var(--primary); font-weight: 600; margin: 5px 0;">📋 <?php echo htmlspecialchars($job['type_contrat']); ?></p>
                </div>
            </div>

            <!-- Description -->
            <div style="margin-bottom: 30px;">
                <h2 style="font-size: 1.5rem; color: var(--primary); margin-bottom: 15px;">📝 Description du Poste</h2>
                <p style="color: var(--text-secondary); line-height: 1.8;">
                    <?php echo nl2br(htmlspecialchars($job['description'])); ?>
                </p>
            </div>

            <!-- Compétences -->
            <?php if(!empty($job['competences'])): ?>
                <div style="margin-bottom: 30px;">
                    <h2 style="font-size: 1.5rem; color: var(--primary); margin-bottom: 15px;">🎯 Compétences Requises</h2>
                    <div style="background: #f0f7ff; padding: 20px; border-radius: 8px; border-left: 4px solid var(--primary);">
                        <p style="color: var(--text-secondary); line-height: 1.8; margin: 0;">
                            <?php echo nl2br(htmlspecialchars($job['competences'])); ?>
                        </p>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Date de publication -->
            <p style="color: #999; font-size: 0.9rem;">
                📅 Publié le <?php echo date('d F Y', strtotime($job['date_publication'])); ?>
            </p>
        </div>

        <!-- Barre latérale: formulaire de candidature -->
        <div class="job-detail-sidebar" style="position: sticky; top: 20px;">
            <div class="card" style="margin-bottom: 20px;">
                <h3 style="color: var(--primary); margin-top: 0;">📤 Postuler pour cette offre</h3>
                
                <?php if(isset($_SESSION['auth_role']) && $_SESSION['auth_role'] === 'candidat'): ?>
                    <form action="<?php echo BASE_URL; ?>/postuler.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="id_offre" value="<?php echo $job['id']; ?>">
                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                        
                        <div class="form-group">
                            <label for="cv">📄 Télécharger votre CV</label>
                            <input type="file" id="cv" name="cv" accept=".pdf" required style="padding: 10px; border: 2px dashed var(--primary); border-radius: 6px;">
                            <small style="color: #666;">Format: PDF uniquement (max 5 MB)</small>
                        </div>

                        <button type="submit" class="btn btn-primary btn-block" style="padding: 14px; margin-top: 20px;">
                            ✉️ Envoyer ma candidature
                        </button>
                    </form>

                        <div style="margin-top: 20px; padding: 15px; background: #f0f7ff; border-radius: 6px;">
                            <p style="margin: 0; color: #666; font-size: 0.9rem;">
                                💡 Votre CV sera transmis à l'entreprise pour examen.
                            </p>
                        </div>
                <?php elseif(!isset($_SESSION['auth_id'])): ?>
                    <p style="color: var(--text-secondary); margin-bottom: 15px;">
                        Vous devez être connecté pour postuler à cette offre.
                    </p>
                    <a href="<?php echo BASE_URL; ?>/login.php" class="btn btn-primary btn-block" style="padding: 14px;">
                        🔐 Se connecter
                    </a>
                    <p style="color: #999; font-size: 0.9rem; margin-top: 10px; text-align: center;">
                        Pas encore de compte ? <a href="<?php echo BASE_URL; ?>/register.php" style="color: var(--primary); text-decoration: none;">S'inscrire</a>
                    </p>
                <?php else: ?>
                    <div style="padding: 20px; background: #fff3cd; border-radius: 6px; border-left: 4px solid #ff9800;">
                        <p style="margin: 0; color: #856404;">
                            ⚠️ Seuls les candidats peuvent postuler à des offres d'emploi.
                        </p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Infos du recruteur -->
            <div class="card" style="margin-top: 20px;">
                <h3 style="color: var(--primary); margin-top: 0;">🏢 À propos du recruteur</h3>
                <p style="margin: 10px 0; color: var(--text-secondary);">
                    <strong><?php echo htmlspecialchars($job['entreprise'] ?? 'Entreprise'); ?></strong>
                </p>
                <?php if($job['email']): ?>
                    <p style="margin: 10px 0;">
                        <a href="mailto:<?php echo htmlspecialchars($job['email']); ?>" style="color: var(--primary); text-decoration: none;">
                            ✉️ <?php echo htmlspecialchars($job['email']); ?>
                        </a>
                    </p>
                <?php endif; ?>
                <?php if($job['telephone']): ?>
                    <p style="margin: 10px 0;">
                        <a href="tel:<?php echo htmlspecialchars($job['telephone']); ?>" style="color: var(--primary); text-decoration: none;">
                            📞 <?php echo htmlspecialchars($job['telephone']); ?>
                        </a>
                    </p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
