<?php
require 'config.php';

try {
    $sql = "SELECT 
        COUNT(*) as total,
        SUM(CASE WHEN `role`='admin' THEN 1 ELSE 0 END) as admins,
        SUM(CASE WHEN `role`='recruteur' THEN 1 ELSE 0 END) as recruiters,
        SUM(CASE WHEN `role`='candidat' THEN 1 ELSE 0 END) as candidates,
        SUM(CASE WHEN is_blocked=1 THEN 1 ELSE 0 END) as blocked
    FROM users";
    
    $stats = $pdo->query($sql)->fetch();
    echo "✓ Requête réussie!\n";
    print_r($stats);
} catch(Exception $e) {
    echo "✗ Erreur: " . $e->getMessage();
}
?>
