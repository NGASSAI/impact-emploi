<?php
require_once 'includes/header.php';
require_once 'includes/config.php';

// --- LOGIQUE DE RECHERCHE AVANCÉE ---
$search = isset($_GET['q']) ? htmlspecialchars($_GET['q']) : '';
$type_contrat = isset($_GET['type']) ? htmlspecialchars($_GET['type']) : '';
$salaire_min = isset($_GET['min_salary']) ? (int)$_GET['min_salary'] : 0;
$lieu = isset($_GET['lieu']) ? htmlspecialchars($_GET['lieu']) : '';

// Construire la requête dynamiquement
$where_clauses = [];
$params = [];

if (!empty($search)) {
    $where_clauses[] = "(titre LIKE ? OR description LIKE ? OR lieu LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if (!empty($type_contrat)) {
    $where_clauses[] = "type_contrat = ?";
    $params[] = $type_contrat;
}

if (!empty($lieu)) {
    $where_clauses[] = "lieu LIKE ?";
    $params[] = "%$lieu%";
}

if ($salaire_min > 0) {
    $where_clauses[] = "salaire >= ?";
    $params[] = $salaire_min;
}

$query = "SELECT * FROM jobs";
if (!empty($where_clauses)) {
    $query .= " WHERE " . implode(" AND ", $where_clauses);
}
$query .= " ORDER BY id DESC";

$stmt = $db->prepare($query);
$stmt->execute($params);
$jobs = $stmt->fetchAll();

// Récupérer les types de contrat disponibles
$types_stmt = $db->query("SELECT DISTINCT type_contrat FROM jobs WHERE type_contrat IS NOT NULL AND type_contrat != '' ORDER BY type_contrat");
$types_contrat = $types_stmt->fetchAll(PDO::FETCH_COLUMN);
?>

<div class="search-section">
    <h1>🔍 Trouvez votre emploi idéal</h1>
    <p>Explorez les meilleures offres d'emploi locales</p>
</div>

<!-- Filtres de recherche avancée -->
<div class="advanced-filters-container">
    <form id="search-form" action="index.php" method="GET" class="advanced-filters">
        <div class="filter-group">
            <input 
                type="text" 
                name="q" 
                placeholder="Métier, description..." 
                value="<?php echo $search; ?>"
                class="filter-input"
            >
        </div>

        <div class="filter-group">
            <input 
                type="text" 
                name="lieu" 
                placeholder="Localité/Ville" 
                value="<?php echo $lieu; ?>"
                class="filter-input"
            >
        </div>

        <div class="filter-group">
            <select name="type" class="filter-select">
                <option value="">Tous les types de contrats</option>
                <?php foreach($types_contrat as $type): ?>
                    <option value="<?php echo htmlspecialchars($type); ?>" <?php echo ($type_contrat === $type) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($type); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="filter-group">
            <input 
                type="number" 
                name="min_salary" 
                placeholder="Salaire min" 
                value="<?php echo $salaire_min; ?>"
                class="filter-input"
                min="0"
                step="1000"
            >
        </div>

        <div class="filter-group">
            <button type="submit" class="btn-primary">Chercher</button>
            <a href="index.php" class="btn-secondary">Réinitialiser</a>
        </div>
    </form>
</div>

<script>
    // Sauvegarder la recherche dans l'historique (localStorage)
    function saveSearchHistory() {
        const formData = new FormData(document.getElementById('search-form'));
        const search = formData.get('q') || '';
        
        if (search.length > 0) {
            let history = JSON.parse(localStorage.getItem('searchHistory') || '[]');
            history = [search, ...history.filter(s => s !== search)].slice(0, 10);
            localStorage.setItem('searchHistory', JSON.stringify(history));
            showNotification('Recherche sauvegardée', 'success', 1500);
        }
    }

    document.getElementById('search-form').addEventListener('submit', saveSearchHistory);
</script>

<div class="results-header">
    <h2>📋 Offres disponibles (<?php echo count($jobs); ?> résultats)</h2>
    
    <?php if(isset($_SESSION['role']) && $_SESSION['role'] === 'recruteur'): ?>
        <a href="poster_offre.php" class="btn-primary">+ Publier une offre</a>
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
        <div style="text-align: center; grid-column: 1 / -1; padding: 50px;">
            <p style="font-size: 1.1rem; color: var(--secondary); margin-bottom: 20px;">
                Aucune offre ne correspond à vos critères.
            </p>
            <a href="index.php" class="btn-primary">Voir toutes les offres</a>
        </div>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>