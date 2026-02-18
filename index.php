<?php
require_once 'includes/header.php';
require_once 'includes/config.php';

// --- LOGIQUE DE RECHERCHE ---
$search = isset($_GET['q']) ? htmlspecialchars($_GET['q']) : '';

if (!empty($search)) {
    // On cherche dans le titre, la description ou le lieu
    // Utiliser des paramètres nommés distincts pour PDO (évite HY093)
    $query = "SELECT * FROM jobs WHERE titre LIKE :s1 OR description LIKE :s2 OR lieu LIKE :s3 ORDER BY id DESC";
    $stmt = $db->prepare($query);
    $stmt->execute([
        ':s1' => "%$search%",
        ':s2' => "%$search%",
        ':s3' => "%$search%",
    ]);
} else {
    // Sinon on affiche tout (tri par `id` pour éviter erreur si `date_publication` manquante)
    $stmt = $db->query("SELECT * FROM jobs ORDER BY id DESC");
}
$jobs = $stmt->fetchAll();
?>

<div class="search-section">
    <h1>Trouvez un emploi près de chez vous</h1>
    <p>Plus besoin de marcher des heures, parcourez les offres ici.</p>
    
    <form action="index.php" method="GET" style="max-width: 600px; margin: 20px auto; display: flex; gap: 10px;">
        <input type="text" name="q" placeholder="Métier, quartier, ville..." value="<?php echo $search; ?>" style="flex: 2;">
        <button type="submit" style="flex: 1; background: var(--success);">Rechercher</button>
    </form>
</div>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <h2>Offres récentes (<?php echo count($jobs); ?>)</h2>
    
    <?php if(isset($_SESSION['role']) && $_SESSION['role'] === 'recruteur'): ?>
        <a href="poster_offre.php" class="btn-primary">
            + Publier une offre
        </a>
    <?php endif; ?>
</div>

<div class="job-grid">
    <?php if(count($jobs) > 0): ?>
        <?php foreach($jobs as $job): ?>
            <div class="job-card">
                <?php if (!empty($job['image'])): ?>
                    <img src="assets/uploads/jobs/<?php echo htmlspecialchars($job['image']); ?>" alt="<?php echo htmlspecialchars($job['titre']); ?>">
                <?php endif; ?>
                <span class="badge"><?php echo htmlspecialchars($job['type_contrat']); ?></span>
                <h3><?php echo htmlspecialchars($job['titre']); ?></h3>
                <p><?php echo substr(htmlspecialchars($job['description']), 0, 120); ?>...</p>
                
                <div class="meta">
                    <strong>📍 Lieu :</strong> <?php echo htmlspecialchars($job['lieu']); ?><br>
                    <strong>💰 Salaire :</strong> <?php echo htmlspecialchars($job['salaire'] ?: 'Non précisé'); ?><br>
                    <strong>📅 Publié le :</strong>
                    <?php
                        if (!empty($job['date_publication'])) {
                            echo date('d/m/Y', strtotime($job['date_publication']));
                        } elseif (!empty($job['created_at'])) {
                            echo date('d/m/Y', strtotime($job['created_at']));
                        } else {
                            echo '—';
                        }
                    ?>
                </div>
                
                <a href="voir_offre.php?id=<?php echo $job['id']; ?>" class="btn-primary" style="margin-top: 15px;">
                    Voir les détails →
                </a>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p style="text-align: center; grid-column: 1 / -1; padding: 50px;">
            Aucune offre trouvée pour "<strong><?php echo $search; ?></strong>".
        </p>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>