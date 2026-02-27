<?php 
// Vérifications de santé du site - Version dynamique
// Charger config.php d'abord pour avoir accès à $db
require_once 'includes/config.php';

$health_checks = [];
$all_healthy = true;

// 1. Vérification PHP
$php_version = phpversion();
$health_checks['php'] = [
    'name' => 'PHP',
    'status' => 'OK',
    'detail' => 'Version ' . $php_version,
    'icon' => '⚡'
];

// 2. Vérification Base de données
if (isset($db) && $db instanceof PDO) {
    try {
        $stmt = $db->query("SELECT VERSION() as version");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $mysql_version = isset($result['version']) ? $result['version'] : 'Inconnu';
        
        // Test de requête
        $db->query("SELECT 1");
        
        $health_checks['database'] = [
            'name' => 'Base de données',
            'status' => 'OK',
            'detail' => 'MySQL ' . substr($mysql_version, 0, 15),
            'icon' => '🗄️'
        ];
    } catch (PDOException $e) {
        $all_healthy = false;
        $health_checks['database'] = [
            'name' => 'Base de données',
            'status' => 'ERROR',
            'detail' => 'Erreur: ' . htmlspecialchars($e->getMessage()),
            'icon' => '🗄️'
        ];
    }
} else {
    $all_healthy = false;
    $health_checks['database'] = [
        'name' => 'Base de données',
        'status' => 'ERROR',
        'detail' => 'Connexion non disponible',
        'icon' => '🗄️'
    ];
}

// 3. Vérification Session
if (session_status() === PHP_SESSION_ACTIVE) {
    $health_checks['session'] = [
        'name' => 'Session PHP',
        'status' => 'OK',
        'detail' => 'Sessions actives',
        'icon' => '🔐'
    ];
} else {
    $all_healthy = false;
    $health_checks['session'] = [
        'name' => 'Session PHP',
        'status' => 'WARNING',
        'detail' => 'Sessions non actives',
        'icon' => '🔐'
    ];
}

// 4. Vérification Dossiers d'upload
$upload_dirs = [
    'assets/uploads/' => 'Dossier uploads',
    'assets/uploads/jobs/' => 'Images offres',
    'assets/uploads/cv/' => 'CV candidats'
];
$upload_status = [];
$upload_ok = true;

foreach ($upload_dirs as $dir => $name) {
    if (!is_dir($dir)) {
        $upload_ok = false;
        $upload_status[] = $name . ' (manquant)';
    } elseif (!is_writable($dir)) {
        $upload_ok = false;
        $upload_status[] = $name . ' (non accessible)';
    }
}

if ($upload_ok) {
    $health_checks['uploads'] = [
        'name' => 'Dossiers Upload',
        'status' => 'OK',
        'detail' => 'Tous les dossiers accessibles',
        'icon' => '📁'
    ];
} else {
    $all_healthy = false;
    $health_checks['uploads'] = [
        'name' => 'Dossiers Upload',
        'status' => 'WARNING',
        'detail' => implode(', ', $upload_status),
        'icon' => '📁'
    ];
}

// 5. Vérification Extensions PHP importantes
$required_extensions = ['pdo', 'pdo_mysql', 'mbstring', 'json', 'gd'];
$missing_ext = [];
foreach ($required_extensions as $ext) {
    if (!extension_loaded($ext)) {
        $missing_ext[] = $ext;
    }
}

if (empty($missing_ext)) {
    $health_checks['extensions'] = [
        'name' => 'Extensions PHP',
        'status' => 'OK',
        'detail' => 'Toutes les extensions requises',
        'icon' => '🔧'
    ];
} else {
    $all_healthy = false;
    $health_checks['extensions'] = [
        'name' => 'Extensions PHP',
        'status' => 'WARNING',
        'detail' => 'Manquantes: ' . implode(', ', $missing_ext),
        'icon' => '🔧'
    ];
}

// 6. Vérification Server
$server_info = $_SERVER['SERVER_SOFTWARE'] ?? 'Inconnu';
$health_checks['server'] = [
    'name' => 'Serveur Web',
    'status' => 'OK',
    'detail' => htmlspecialchars($server_info),
    'icon' => '🌐'
];

// 7. Vérification Configuration
$config_writable = is_writable('config.php');
$health_checks['config'] = [
    'name' => 'Fichier Config',
    'status' => $config_writable ? 'WARNING' : 'OK',
    'detail' => $config_writable ? 'Accessible en écriture (attention!)' : 'Protégé',
    'icon' => '⚙️'
];

// Timestamp
$check_time = date('d/m/Y à H:i:s');
?>

<?php include 'includes/header.php'; ?>

<main class="health-page">
    <div class="health-header">
        <div class="health-title">
            <span class="health-icon">💚</span>
            <h1>Santé du Site</h1>
        </div>
        <p class="health-subtitle">Vérification du <?php echo $check_time; ?></p>
    </div>

    <!-- Résumé global -->
    <div class="health-summary <?php echo $all_healthy ? 'healthy' : 'warning'; ?>">
        <div class="summary-icon">
            <?php echo $all_healthy ? '✅' : '⚠️'; ?>
        </div>
        <div class="summary-text">
            <h2><?php echo $all_healthy ? 'Tous les systèmes opérationnels' : 'Beberapa composants nécessitent attention'; ?></h2>
            <p><?php echo $all_healthy ? 'Le site fonctionne correctement. Tous les services sont disponibles.' : 'Vérifiez les éléments en orange ou rouge ci-dessous.'; ?></p>
        </div>
        <button class="btn-refresh" onclick="location.reload()">
            <span>🔄</span> Actualiser
        </button>
    </div>

    <!-- Grille des vérifications -->
    <div class="health-grid">
        <?php foreach ($health_checks as $check): ?>
            <div class="health-card status-<?php echo strtolower($check['status']); ?>">
                <div class="card-header">
                    <span class="card-icon"><?php echo $check['icon']; ?></span>
                    <span class="card-name"><?php echo $check['name']; ?></span>
                </div>
                <div class="card-status">
                    <?php 
                    $status_class = '';
                    $status_text = '';
                    switch($check['status']) {
                        case 'OK':
                            $status_class = 'success';
                            $status_text = 'Opérationnel';
                            break;
                        case 'WARNING':
                            $status_class = 'warning';
                            $status_text = 'Attention';
                            break;
                        case 'ERROR':
                            $status_class = 'error';
                            $status_text = 'Erreur';
                            break;
                    }
                    ?>
                    <span class="badge badge-<?php echo $status_class; ?>">
                        <?php if($check['status'] === 'OK'): ?>✓<?php elseif($check['status'] === 'WARNING'): ?>⚠<?php else: ?>✗<?php endif; ?>
                        <?php echo $status_text; ?>
                    </span>
                </div>
                <div class="card-detail"><?php echo $check['detail']; ?></div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Informations système -->
    <div class="system-info">
        <h3>📊 Informations Système</h3>
        <div class="info-grid">
            <div class="info-item">
                <span class="info-label">Heure serveur:</span>
                <span class="info-value"><?php echo date('d/m/Y H:i:s'); ?></span>
            </div>
            <div class="info-item">
                <span class="info-label">Adresse IP:</span>
                <span class="info-value"><?php echo $_SERVER['REMOTE_ADDR'] ?? 'N/A'; ?></span>
            </div>
            <div class="info-item">
                <span class="info-label">Memory Limit:</span>
                <span class="info-value"><?php echo ini_get('memory_limit'); ?></span>
            </div>
            <div class="info-item">
                <span class="info-label">Max Upload:</span>
                <span class="info-value"><?php echo ini_get('upload_max_filesize'); ?></span>
            </div>
            <div class="info-item">
                <span class="info-label">Max Post:</span>
                <span class="info-value"><?php echo ini_get('post_max_size'); ?></span>
            </div>
            <div class="info-item">
                <span class="info-label">Timezone:</span>
                <span class="info-value"><?php echo date_default_timezone_get(); ?></span>
            </div>
        </div>
    </div>

    <!-- Message d'aide -->
    <div class="health-help">
        <p>💡 <strong>Note:</strong> Si un composant affiche une erreur, contactez l'administrateur du site.</p>
    </div>
</main>

<!-- CSS pour la page de santé -->
<style>
    .health-page {
        padding: 2rem;
        max-width: 1000px;
        margin: 0 auto;
    }

    .health-header {
        text-align: center;
        margin-bottom: 2rem;
    }

    .health-title {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 12px;
    }

    .health-icon {
        font-size: 2.5rem;
    }

    .health-title h1 {
        font-size: 2rem;
        color: var(--primary);
        margin: 0;
    }

    .health-subtitle {
        color: var(--text-secondary);
        margin-top: 8px;
    }

    /* Summary Card */
    .health-summary {
        display: flex;
        align-items: center;
        gap: 20px;
        padding: 20px 25px;
        border-radius: 12px;
        margin-bottom: 2rem;
        background: white;
        box-shadow: var(--shadow-md);
    }

    .health-summary.healthy {
        border-left: 5px solid var(--success);
    }

    .health-summary.warning {
        border-left: 5px solid var(--warning);
    }

    .summary-icon {
        font-size: 2.5rem;
    }

    .summary-text {
        flex: 1;
    }

    .summary-text h2 {
        margin: 0 0 5px 0;
        font-size: 1.2rem;
        color: var(--text-primary);
    }

    .summary-text p {
        margin: 0;
        color: var(--text-secondary);
        font-size: 0.95rem;
    }

    .btn-refresh {
        background: var(--primary);
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 8px;
        cursor: pointer;
        font-size: 0.95rem;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: var(--transition);
    }

    .btn-refresh:hover {
        background: #003d7a;
        transform: translateY(-2px);
    }

    /* Health Grid */
    .health-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 20px;
        margin-bottom: 2rem;
    }

    .health-card {
        background: white;
        border-radius: 10px;
        padding: 20px;
        box-shadow: var(--shadow-md);
        border-left: 4px solid var(--success);
    }

    .health-card.status-warning {
        border-left-color: var(--warning);
    }

    .health-card.status-error {
        border-left-color: var(--danger);
    }

    .card-header {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 12px;
    }

    .card-icon {
        font-size: 1.5rem;
    }

    .card-name {
        font-weight: 600;
        color: var(--text-primary);
    }

    .card-status {
        margin-bottom: 8px;
    }

    .badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 600;
    }

    .badge-success {
        background: #d1fae5;
        color: #065f46;
    }

    .badge-warning {
        background: #fef3c7;
        color: #92400e;
    }

    .badge-error {
        background: #fee2e2;
        color: #991b1b;
    }

    .card-detail {
        color: var(--text-secondary);
        font-size: 0.9rem;
    }

    /* System Info */
    .system-info {
        background: white;
        border-radius: 10px;
        padding: 20px;
        box-shadow: var(--shadow-md);
        margin-bottom: 2rem;
    }

    .system-info h3 {
        margin: 0 0 15px 0;
        color: var(--text-primary);
        font-size: 1.1rem;
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 15px;
    }

    .info-item {
        display: flex;
        justify-content: space-between;
        padding: 10px;
        background: var(--bg-secondary);
        border-radius: 6px;
    }

    .info-label {
        color: var(--text-secondary);
        font-size: 0.9rem;
    }

    .info-value {
        color: var(--text-primary);
        font-weight: 600;
        font-size: 0.9rem;
    }

    /* Help Message */
    .health-help {
        text-align: center;
        padding: 15px;
        background: #f0f9ff;
        border-radius: 8px;
        border: 1px solid #bae6fd;
    }

    .health-help p {
        margin: 0;
        color: var(--text-secondary);
    }

    /* Responsive */
    @media (max-width: 768px) {
        .health-page {
            padding: 1rem;
        }

        .health-title h1 {
            font-size: 1.5rem;
        }

        .health-summary {
            flex-direction: column;
            text-align: center;
        }

        .btn-refresh {
            width: 100%;
            justify-content: center;
        }
    }
</style>

<?php include 'includes/footer.php'; ?>

