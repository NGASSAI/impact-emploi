<?php
require_once 'config.php';
track_visitor($pdo, 'admin_users.php');
include 'includes/header.php';

if($_SESSION['auth_role'] !== 'admin') {
    header('Location: ' . BASE_URL . '/index.php');
    exit();
}

// Recherche et filtrage
$search = isset($_GET['search']) ? clean($_GET['search']) : '';
$role_filter = isset($_GET['role']) ? clean($_GET['role']) : '';
$status_filter = isset($_GET['status']) ? clean($_GET['status']) : '';
$where = "WHERE 1=1";
$params = [];

if($search) {
    $where .= " AND (u.nom LIKE ? OR u.prenom LIKE ? OR u.email LIKE ?)";
    $params[] = '%' . $search . '%';
    $params[] = '%' . $search . '%';
    $params[] = '%' . $search . '%';
}

if($role_filter) {
    $where .= " AND u.role = ?";
    $params[] = $role_filter;
}

if($status_filter === 'blocked') {
    $where .= " AND u.is_blocked = 1";
} elseif($status_filter === 'active') {
    $where .= " AND u.is_blocked = 0";
}

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 25;
$offset = ($page - 1) * $per_page;

// Compter total
$count_sql = "SELECT COUNT(*) FROM users u $where";
$count_stmt = $pdo->prepare($count_sql);
$count_stmt->execute($params);
$total = $count_stmt->fetchColumn();
$pages = ceil($total / $per_page);

// Récupérer utilisateurs
$sql = "SELECT u.* FROM users u $where ORDER BY u.date_inscription DESC LIMIT $per_page OFFSET $offset";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$users = $stmt->fetchAll();

// Statistiques
$stats = $pdo->query("SELECT 
    COUNT(*) as total,
    SUM(CASE WHEN `role`='admin' THEN 1 ELSE 0 END) as admins,
    SUM(CASE WHEN `role`='recruteur' THEN 1 ELSE 0 END) as recruiters,
    SUM(CASE WHEN `role`='candidat' THEN 1 ELSE 0 END) as candidates,
    SUM(CASE WHEN is_blocked=1 THEN 1 ELSE 0 END) as blocked
FROM users")->fetch();
?>

<div class="container" style="max-width: 1400px; padding: 40px 0;">
    <h1 style="color: var(--primary); margin-bottom: 10px;">👥 Gestion des Utilisateurs</h1>
    <p style="color: var(--text-secondary); margin-bottom: 30px;">Gérez tous les utilisateurs de la plateforme</p>
    
    <!-- Statistiques -->
    <style>
        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: 1fr 1fr !important;
            }
        }
        @media (max-width: 480px) {
            .stats-grid {
                grid-template-columns: 1fr !important;
            }
        }
    </style>
    <div class="stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 15px; margin-bottom: 30px;">
        <div class="card" style="text-align: center;">
            <div style="font-size: 2rem; color: var(--primary); font-weight: bold;"><?php echo $stats['total']; ?></div>
            <small style="color: var(--text-secondary);">Total</small>
        </div>
        <div class="card" style="text-align: center;">
            <div style="font-size: 2rem; color: var(--primary); font-weight: bold;"><?php echo $stats['admins']; ?></div>
            <small style="color: var(--text-secondary);">Admins</small>
        </div>
        <div class="card" style="text-align: center;">
            <div style="font-size: 2rem; color: var(--info); font-weight: bold;"><?php echo $stats['recruiters']; ?></div>
            <small style="color: var(--text-secondary);">Recruteurs</small>
        </div>
        <div class="card" style="text-align: center;">
            <div style="font-size: 2rem; color: var(--success); font-weight: bold;"><?php echo $stats['candidates']; ?></div>
            <small style="color: var(--text-secondary);">Candidats</small>
        </div>
        <div class="card" style="text-align: center;">
            <div style="font-size: 2rem; color: var(--danger); font-weight: bold;"><?php echo $stats['blocked']; ?></div>
            <small style="color: var(--text-secondary);">Bloqués</small>
        </div>
    </div>

    <!-- Recherche et Filtrage -->
    <div class="card" style="margin-bottom: 25px;">
        <form method="GET" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; align-items: flex-end;">
            <div class="form-group" style="margin-bottom: 0;">
                <label for="search">🔍 Nom/Email</label>
                <input type="text" id="search" name="search" value="<?php echo htmlspecialchars($search); ?>" 
                       placeholder="nathan, nathan@email.com..." style="padding: 12px; width: 100%; box-sizing: border-box;">
            </div>
            <div class="form-group" style="margin-bottom: 0;">
                <label for="role">👔 Rôle</label>
                <select id="role" name="role" style="padding: 12px; width: 100%; box-sizing: border-box;">
                    <option value="">Tous</option>
                    <option value="admin" <?php echo $role_filter === 'admin' ? 'selected' : ''; ?>>Admin</option>
                    <option value="recruteur" <?php echo $role_filter === 'recruteur' ? 'selected' : ''; ?>>Recruteur</option>
                    <option value="candidat" <?php echo $role_filter === 'candidat' ? 'selected' : ''; ?>>Candidat</option>
                </select>
            </div>
            <div class="form-group" style="margin-bottom: 0;">
                <label for="status">🔒 Statut</label>
                <select id="status" name="status" style="padding: 12px; width: 100%; box-sizing: border-box;">
                    <option value="">Tous</option>
                    <option value="active" <?php echo $status_filter === 'active' ? 'selected' : ''; ?>>Actif</option>
                    <option value="blocked" <?php echo $status_filter === 'blocked' ? 'selected' : ''; ?>>Bloqué</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary" style="padding: 12px 20px; width: 100%; box-sizing: border-box;">🔍 Rechercher</button>
            <a href="<?php echo BASE_URL; ?>/admin_users.php" class="btn btn-outline" style="padding: 12px 20px; width: 100%; box-sizing: border-box; text-align: center; display: block;">❌ Réinit</a>
        </form>
    </div>

    <!-- Tableau Utilisateurs -->
    <div class="card" style="overflow-x: auto;">
        <style>
            @media (max-width: 768px) {
                .users-table {
                    display: grid;
                    grid-template-columns: 1fr;
                    gap: 15px;
                }
                .users-table thead {
                    display: none;
                }
                .users-table tbody {
                    display: grid;
                    grid-template-columns: 1fr;
                    gap: 15px;
                }
                .users-table tr {
                    display: grid;
                    grid-template-columns: 1fr;
                    gap: 10px;
                    border: 1px solid var(--border-color);
                    border-radius: 8px;
                    padding: 15px;
                    background: var(--light);
                }
                .users-table td {
                    display: flex;
                    align-items: center;
                    gap: 10px;
                }
                .users-table td::before {
                    content: attr(data-label);
                    font-weight: bold;
                    min-width: 80px;
                    color: var(--primary);
                }
                .users-table td:nth-child(1)::before { content: "Utilisateur"; }
                .users-table td:nth-child(2)::before { content: "Email"; }
                .users-table td:nth-child(3)::before { content: "Rôle"; }
                .users-table td:nth-child(4)::before { content: "Statut"; }
                .users-table td:nth-child(5)::before { content: "Inscription"; }
                .users-table td:nth-child(6)::before { content: "Actions"; }
                
                .users-table td:nth-child(1) {
                    flex-direction: column;
                    align-items: flex-start;
                }
                .users-table td:nth-child(6) {
                    flex-direction: column;
                    align-items: stretch;
                }
                .users-table .btn {
                    width: 100% !important;
                    text-align: center;
                    margin-bottom: 5px;
                }
            }
        </style>
        <table class="users-table" style="width: 100%; border-collapse: collapse; font-size: 0.9rem;">
            <thead>
                <tr style="background: var(--light); border-bottom: 2px solid var(--border-color);">
                    <th style="padding: 12px; text-align: left;">👤 Utilisateur</th>
                    <th style="padding: 12px; text-align: left;">📧 Email</th>
                    <th style="padding: 12px; text-align: center;">👔 Rôle</th>
                    <th style="padding: 12px; text-align: center;">🔒 Statut</th>
                    <th style="padding: 12px; text-align: left;">📅 Inscription</th>
                    <th style="padding: 12px; text-align: center;">⚙️ Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if(count($users) === 0): ?>
                <tr>
                    <td colspan="6" style="padding: 20px; text-align: center; color: var(--text-secondary);">
                        Aucun utilisateur trouvé
                    </td>
                </tr>
                <?php else: ?>
                    <?php foreach($users as $u): ?>
                    <tr style="border-bottom: 1px solid var(--border-color);">
                        <td style="padding: 12px;">
                            <strong><?php echo htmlspecialchars($u['prenom'] . ' ' . $u['nom']); ?></strong>
                            <?php if(!empty($u['photo_profil'])): ?>
                                <br><small style="color: var(--text-secondary);">📸 Photo</small>
                            <?php endif; ?>
                        </td>
                        <td style="padding: 12px;">
                            <code style="background: var(--light); padding: 4px 8px; border-radius: 4px; font-size: 0.85rem;">
                                <?php echo htmlspecialchars($u['email']); ?>
                            </code>
                        </td>
                        <td style="padding: 12px; text-align: center;">
                            <?php 
                            $role_colors = ['admin' => 'badge-primary', 'recruteur' => 'badge-info', 'candidat' => 'badge-success'];
                            $role_labels = ['admin' => 'Admin', 'recruteur' => 'Recruteur', 'candidat' => 'Candidat'];
                            ?>
                            <span class="badge <?php echo $role_colors[$u['role']] ?? 'badge-secondary'; ?>">
                                <?php echo $role_labels[$u['role']] ?? ucfirst($u['role']); ?>
                            </span>
                        </td>
                        <td style="padding: 12px; text-align: center;">
                            <?php if($u['is_blocked']): ?>
                                <span class="badge badge-danger">🔒 Bloqué</span>
                            <?php else: ?>
                                <span class="badge badge-success">✓ Actif</span>
                            <?php endif; ?>
                        </td>
                        <td style="padding: 12px;">
                            <small><?php echo date('d/m/Y', strtotime($u['date_inscription'])); ?></small>
                        </td>
                        <td style="padding: 12px; text-align: center;">
                            <?php if($u['is_blocked']): ?>
                                <a href="<?php echo BASE_URL; ?>/admin_actions.php?toggle_block=<?php echo $u['id']; ?>&redirect=admin_users" 
                                   class="btn btn-small btn-success" title="Débloquer">Débloquer</a>
                            <?php else: ?>
                                <a href="<?php echo BASE_URL; ?>/admin_actions.php?toggle_block=<?php echo $u['id']; ?>&redirect=admin_users" 
                                   class="btn btn-small btn-outline" title="Bloquer">Bloquer</a>
                            <?php endif; ?>
                            <a href="<?php echo BASE_URL; ?>/admin_actions.php?del_user=<?php echo $u['id']; ?>&redirect=admin_users" 
                               class="btn btn-small btn-danger" 
                               onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur?');">Supprimer</a>
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
        <?php 
        $query_params = http_build_query(array_filter([
            'search' => $search ?: null,
            'role' => $role_filter ?: null,
            'status' => $status_filter ?: null
        ]));
        $separator = $query_params ? '&' : '?';
        ?>
        <?php for($i = 1; $i <= $pages; $i++): ?>
            <?php if($i === $page): ?>
                <button style="padding: 10px 15px; background: var(--primary); color: white; border: none; border-radius: 6px; cursor: default;">
                    <?php echo $i; ?>
                </button>
            <?php else: ?>
                <a href="?page=<?php echo $i; ?><?php echo $query_params ? '&' . $query_params : ''; ?>" 
                   class="btn btn-outline" style="padding: 10px 15px;">
                    <?php echo $i; ?>
                </a>
            <?php endif; ?>
        <?php endfor; ?>
    </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
