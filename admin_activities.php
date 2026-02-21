<?php
require_once 'config.php';
track_visitor($pdo, 'admin_activities.php');
include 'includes/header.php';

if($_SESSION['auth_role'] !== 'admin') {
    header('Location: ' . BASE_URL . '/index.php');
    exit();
}

// Recherche par action/description et par date
$search = isset($_GET['search']) ? clean($_GET['search']) : '';
$search_date = isset($_GET['date']) ? clean($_GET['date']) : '';
$where = "WHERE 1=1";
$params = [];

if($search) {
    $where .= " AND (a.action LIKE ? OR a.description LIKE ?)";
    $params[] = '%' . $search . '%';
    $params[] = '%' . $search . '%';
}

if($search_date) {
    $where .= " AND DATE(a.created_at) = ?";
    $params[] = $search_date;
}

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 30;
$offset = ($page - 1) * $per_page;

// Compter total
$count_sql = "SELECT COUNT(*) FROM activity_logs a LEFT JOIN users u ON a.user_id = u.id $where";
$count_stmt = $pdo->prepare($count_sql);
$count_stmt->execute($params);
$total = $count_stmt->fetchColumn();
$pages = ceil($total / $per_page);

// Récupérer activités
$sql = "SELECT a.*, u.nom, u.prenom, u.email, u.role FROM activity_logs a 
        LEFT JOIN users u ON a.user_id = u.id
        $where 
        ORDER BY a.created_at DESC 
        LIMIT $per_page OFFSET $offset";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$activities = $stmt->fetchAll();

// Icônes pour les actions
$action_icons = [
    'login' => '🔐',
    'logout' => '🔓',
    'create_job' => '➕',
    'update_job' => '✏️',
    'delete_job' => '🗑️',
    'apply_job' => '📝',
    'update_profile' => '👤',
    'update_password' => '🔑',
    'create_feedback' => '💬',
];
?>

<div class="container" style="max-width: 1200px; padding: 40px 0;">
    <h1 style="color: var(--primary); margin-bottom: 30px;">📋 Activités des utilisateurs</h1>
    
    <!-- Recherche -->
    <div class="card" style="margin-bottom: 25px;">
        <form method="GET" style="display: grid; grid-template-columns: 1fr 1fr 200px auto; gap: 15px; align-items: flex-end;">
            <div class="form-group" style="margin-bottom: 0;">
                <label for="search">🔍 Rechercher (action/description)</label>
                <input type="text" id="search" name="search" value="<?php echo htmlspecialchars($search); ?>" 
                       placeholder="login, create_job, update_profile..." style="padding: 12px; width: 100%;">
            </div>
            <div class="form-group" style="margin-bottom: 0;">
                <label for="date">📅 Filtrer par date</label>
                <input type="date" id="date" name="date" value="<?php echo htmlspecialchars($search_date); ?>" style="padding: 12px; width: 100%;">
            </div>
            <button type="submit" class="btn btn-primary" style="padding: 12px 20px;">🔍 Rechercher</button>
            <?php if($search || $search_date): ?>
                <a href="<?php echo BASE_URL; ?>/admin_activities.php" class="btn btn-outline" style="padding: 12px 20px;">❌ Réinit</a>
            <?php endif; ?>
        </form>
    </div>

    <!-- Statistiques -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-bottom: 25px;">
        <div class="card" style="text-align: center;">
            <div style="font-size: 2rem; color: var(--primary); font-weight: bold;">
                <?php
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM activity_logs");
                $stmt->execute();
                echo $stmt->fetchColumn();
                ?>
            </div>
            <div style="color: var(--text-secondary); margin-top: 5px;">Total activités</div>
        </div>
        <div class="card" style="text-align: center;">
            <div style="font-size: 2rem; color: var(--primary); font-weight: bold;">
                <?php
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM activity_logs WHERE DATE(created_at) = DATE(NOW())");
                $stmt->execute();
                echo $stmt->fetchColumn();
                ?>
            </div>
            <div style="color: var(--text-secondary); margin-top: 5px;">Aujourd'hui</div>
        </div>
        <div class="card" style="text-align: center;">
            <div style="font-size: 2rem; color: var(--primary); font-weight: bold;">
                <?php
                $stmt = $pdo->prepare("SELECT action, COUNT(*) as count FROM activity_logs GROUP BY action ORDER BY count DESC LIMIT 1");
                $stmt->execute();
                $top = $stmt->fetch();
                echo isset($top['action']) ? htmlspecialchars($top['action']) : 'N/A';
                ?>
            </div>
            <div style="color: var(--text-secondary); margin-top: 5px;">Action la plus fréquente</div>
        </div>
    </div>

    <!-- Tableau activités -->
    <div class="card" style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; font-size: 0.9rem;">
            <thead>
                <tr style="background: var(--light); border-bottom: 2px solid var(--border-color);">
                    <th style="padding: 12px; text-align: left;">Action</th>
                    <th style="padding: 12px; text-align: left;">👤 Utilisateur</th>
                    <th style="padding: 12px; text-align: left;">📝 Description</th>
                    <th style="padding: 12px; text-align: left;">🌐 IP</th>
                    <th style="padding: 12px; text-align: left;">📅 Date</th>
                </tr>
            </thead>
            <tbody>
                <?php if(count($activities) === 0): ?>
                <tr>
                    <td colspan="5" style="padding: 20px; text-align: center; color: var(--text-secondary);">
                        Aucune activité trouvée
                    </td>
                </tr>
                <?php else: ?>
                    <?php foreach($activities as $a): ?>
                    <tr style="border-bottom: 1px solid var(--border-color);">
                        <td style="padding: 12px;">
                            <div style="font-size: 1.2rem; margin-right: 8px;">
                                <?php echo isset($action_icons[$a['action']]) ? $action_icons[$a['action']] : '•'; ?>
                            </div>
                            <code style="background: var(--light); padding: 4px 8px; border-radius: 4px; font-size: 0.85rem;">
                                <?php echo htmlspecialchars($a['action']); ?>
                            </code>
                        </td>
                        <td style="padding: 12px;">
                            <?php if($a['user_id']): ?>
                                <strong><?php echo htmlspecialchars($a['prenom'] . ' ' . $a['nom']); ?></strong><br>
                                <small style="color: var(--text-secondary);">
                                    <?php 
                                    $role_labels = ['admin' => 'Admin', 'recruiter' => 'Recruteur', 'candidate' => 'Candidat'];
                                    echo isset($role_labels[$a['role']]) ? $role_labels[$a['role']] : $a['role'];
                                    ?>
                                </small>
                            <?php else: ?>
                                <span style="color: var(--text-secondary);">Utilisateur supprimé</span>
                            <?php endif; ?>
                        </td>
                        <td style="padding: 12px;">
                            <small><?php echo htmlspecialchars(substr($a['description'], 0, 50)); ?></small>
                        </td>
                        <td style="padding: 12px;">
                            <code style="font-size: 0.85rem; background: var(--light); padding: 4px 8px; border-radius: 4px;">
                                <?php echo htmlspecialchars($a['ip_address']); ?>
                            </code>
                        </td>
                        <td style="padding: 12px; white-space: nowrap;">
                            <?php echo date('d/m/Y H:i', strtotime($a['created_at'])); ?>
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
                <a href="?page=<?php echo $i; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>" class="btn btn-outline" style="padding: 10px 15px;">
                    <?php echo $i; ?>
                </a>
            <?php endif; ?>
        <?php endfor; ?>
    </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
