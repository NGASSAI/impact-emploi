<?php
require_once 'config.php';
track_visitor($pdo, 'admin_visitors.php');
include 'includes/header.php';

if($_SESSION['auth_role'] !== 'admin') {
    header('Location: ' . BASE_URL . '/index.php');
    exit();
}

// Recherche par date
$search_date = isset($_GET['date']) ? clean($_GET['date']) : '';
$where = "WHERE is_bot = 0";
if($search_date) {
    $where .= " AND DATE(v.created_at) = ?";
}

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 30;
$offset = ($page - 1) * $per_page;

// Compter total des dates uniques
$count_sql = "SELECT COUNT(*) as count FROM (
    SELECT DATE(v.created_at), v.ip_address 
    FROM visitors v 
    $where
    GROUP BY DATE(v.created_at), v.ip_address
) as grouped";

$count_stmt = $pdo->prepare($count_sql);
if($search_date) {
    $count_stmt->execute([$search_date]);
} else {
    $count_stmt->execute();
}
$result = $count_stmt->fetch();
$total = $result['count'] ?? 0;
$pages = ceil($total / $per_page);

// Récupérer visiteurs groupés par date et IP avec compteur
$sql = "SELECT 
    DATE(v.created_at) as visit_date,
    v.ip_address,
    COUNT(*) as visit_count,
    MAX(v.user_id) as user_id,
    MAX(u.nom) as nom,
    MAX(u.prenom) as prenom,
    MAX(u.email) as email,
    MAX(v.created_at) as last_visit
FROM visitors v 
LEFT JOIN users u ON v.user_id = u.id
$where
GROUP BY DATE(v.created_at), v.ip_address
ORDER BY visit_date DESC, v.ip_address DESC
LIMIT $per_page OFFSET $offset";

$stmt = $pdo->prepare($sql);
if($search_date) {
    $stmt->execute([$search_date]);
} else {
    $stmt->execute();
}
$visitors = $stmt->fetchAll();
?>

<div class="container" style="max-width: 1200px; padding: 40px 0;">
    <h1 style="color: var(--primary); margin-bottom: 30px;">👁️ Suivi des Visiteurs</h1>
    <p style="color: var(--text-secondary); margin-bottom: 30px;">Affichage groupé par date et IP - compteur de visites par jour</p>
    
    <!-- Recherche par date -->
    <div class="card" style="margin-bottom: 25px;">
        <form method="GET" style="display: flex; gap: 15px; align-items: flex-end; flex-wrap: wrap;">
            <div class="form-group" style="margin-bottom: 0; flex: 1; min-width: 200px;">
                <label for="date">📅 Filtrer par date</label>
                <input type="date" id="date" name="date" value="<?php echo htmlspecialchars($search_date); ?>" style="padding: 12px; width: 100%;">
            </div>
            <button type="submit" class="btn btn-primary" style="padding: 12px 20px;">🔍 Rechercher</button>
            <?php if($search_date): ?>
                <a href="<?php echo BASE_URL; ?>/admin_visitors.php" class="btn btn-outline" style="padding: 12px 20px;">❌ Réinitialiser</a>
            <?php endif; ?>
        </form>
    </div>

    <!-- Statistiques -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-bottom: 25px;">
        <div class="card" style="text-align: center;">
            <div style="font-size: 2rem; color: var(--primary); font-weight: bold;">
                <?php
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM visitors WHERE is_bot = 0");
                $stmt->execute();
                echo $stmt->fetchColumn();
                ?>
            </div>
            <div style="color: var(--text-secondary); margin-top: 5px;">Visites totales</div>
        </div>
        <div class="card" style="text-align: center;">
            <div style="font-size: 2rem; color: var(--primary); font-weight: bold;">
                <?php
                $stmt = $pdo->prepare("SELECT COUNT(DISTINCT ip_address) FROM visitors WHERE is_bot = 0");
                $stmt->execute();
                echo $stmt->fetchColumn();
                ?>
            </div>
            <div style="color: var(--text-secondary); margin-top: 5px;">IPs uniques</div>
        </div>
        <div class="card" style="text-align: center;">
            <div style="font-size: 2rem; color: var(--primary); font-weight: bold;">
                <?php
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM visitors WHERE user_id IS NOT NULL AND is_bot = 0");
                $stmt->execute();
                echo $stmt->fetchColumn();
                ?>
            </div>
            <div style="color: var(--text-secondary); margin-top: 5px;">Visites inscrits</div>
        </div>
    </div>

    <!-- Tableau visiteurs -->
    <div class="card" style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; font-size: 0.95rem;">
            <thead>
                <tr style="background: var(--light); border-bottom: 2px solid var(--border-color);">
                    <th style="padding: 12px; text-align: left;">📅 Date</th>
                    <th style="padding: 12px; text-align: left;">🌐 IP</th>
                    <th style="padding: 12px; text-align: left;">👤 Utilisateur</th>
                    <th style="padding: 12px; text-align: center;">🔢 Visites</th>
                    <th style="padding: 12px; text-align: left;">⏰ Dernière visite</th>
                </tr>
            </thead>
            <tbody>
                <?php if(count($visitors) === 0): ?>
                <tr>
                    <td colspan="5" style="padding: 20px; text-align: center; color: var(--text-secondary);">
                        Aucun visiteur trouvé
                    </td>
                </tr>
                <?php else: ?>
                    <?php foreach($visitors as $v): ?>
                    <tr style="border-bottom: 1px solid var(--border-color);">
                        <td style="padding: 12px; white-space: nowrap;">
                            <strong><?php echo $v['visit_date']; ?></strong>
                        </td>
                        <td style="padding: 12px;">
                            <code style="background: var(--light); padding: 4px 8px; border-radius: 4px;">
                                <?php echo htmlspecialchars($v['ip_address']); ?>
                            </code>
                        </td>
                        <td style="padding: 12px;">
                            <?php if($v['user_id']): ?>
                                <strong><?php echo htmlspecialchars($v['prenom'] . ' ' . $v['nom']); ?></strong><br>
                                <small style="color: var(--text-secondary);"><?php echo htmlspecialchars($v['email']); ?></small>
                            <?php else: ?>
                                <span style="color: var(--text-secondary);">Non inscrit</span>
                            <?php endif; ?>
                        </td>
                        <td style="padding: 12px; text-align: center;">
                            <span style="background: var(--primary); color: white; padding: 4px 12px; border-radius: 20px; font-weight: bold; font-size: 0.9rem;">
                                <?php echo $v['visit_count']; ?>
                            </span>
                        </td>
                        <td style="padding: 12px; white-space: nowrap;">
                            <small><?php echo date('H:i', strtotime($v['last_visit'])); ?></small>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <?php if($pages > 1): ?>
    <div style="display: flex; justify-content: center; gap: 10px; margin-top: 25px; flex-wrap: wrap;">
        <?php for($i = 1; $i <= $pages; $i++): ?>
            <?php if($i === $page): ?>
                <button style="padding: 10px 15px; background: var(--primary); color: white; border: none; border-radius: 6px; cursor: default;">
                    <?php echo $i; ?>
                </button>
            <?php else: ?>
                <a href="?page=<?php echo $i; ?><?php echo $search_date ? '&date=' . urlencode($search_date) : ''; ?>" class="btn btn-outline" style="padding: 10px 15px;">
                    <?php echo $i; ?>
                </a>
            <?php endif; ?>
        <?php endfor; ?>
    </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
