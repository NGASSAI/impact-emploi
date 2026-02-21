<?php
require_once 'config.php';
// Affichage HTML stylé pour le contrôle de santé
try {
    $db_ok = false;
    $pdo->query('SELECT 1');
    $db_ok = true;
} catch (Exception $e) {
    $db_ok = false;
    $db_error = $e->getMessage();
}

$tables = ['users','jobs','candidatures'];
$table_status = [];
foreach ($tables as $t) {
    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = ?");
        $stmt->execute([$t]);
        $count = (int) $stmt->fetchColumn();
        $table_status[$t] = $count > 0 ? 'Présente' : 'Manquante';
    } catch (Exception $e) {
        $table_status[$t] = 'Inconnue';
    }
}

// Format de la date en timezone configurée (config.php définit Africa/Brazzaville)
$now = date('d/m/Y H:i:s');

include 'includes/header.php';
?>

<div class="container" style="padding: 30px 0;">
    <div class="card" style="max-width:900px;margin:0 auto;">
        <h2 style="color:var(--primary); margin-bottom:8px;">✅ Santé du site</h2>
        <p class="text-muted">Vérification rapide des composants essentiels</p>

        <div style="display:flex; gap:20px; flex-wrap:wrap; margin-top:20px;">
            <div style="flex:1; min-width:220px;">
                <h4>Statut</h4>
                <p style="font-size:1.1rem;">Serveur: <strong>OK</strong></p>
                <p style="font-size:1.1rem;">Base de données: <strong style="color: <?php echo $db_ok ? 'var(--success)' : 'var(--danger)'; ?>"><?php echo $db_ok ? 'Connectée' : 'Non connectée'; ?></strong></p>
                <?php if(empty($db_ok) && !empty($db_error)): ?>
                    <div class="alert alert-error">Erreur DB: <?php echo htmlspecialchars($db_error); ?></div>
                <?php endif; ?>
            </div>

            <div style="flex:1; min-width:220px;">
                <h4>Horloge</h4>
                <p style="font-size:1.1rem;"><strong><?php echo $now; ?></strong></p>
                <p class="text-muted">Fuseau: Africa/Brazzaville</p>
            </div>

            <div style="flex:1; min-width:220px;">
                <h4>Tables essentielles</h4>
                <ul style="list-style:none; padding-left:0;">
                    <?php foreach($table_status as $name => $st): ?>
                        <li style="margin-bottom:6px;"><strong><?php echo htmlspecialchars($name); ?></strong>: <?php echo htmlspecialchars($st); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>

        <div style="margin-top:20px;">
            <a href="<?php echo BASE_URL; ?>/admin_dashboard.php" class="btn btn-outline">Retour au tableau de bord</a>
        </div>
    </div>
</div>

<?php include 'includes/footer.php';
