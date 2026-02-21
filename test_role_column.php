<?php
require_once 'config.php';

echo "<h1>Test de la colonne 'role'</h1>";

// Test 1: Vérifier que la colonne existe
try {
    $result = $pdo->query("DESCRIBE users")->fetchAll();
    echo "<h2>Structure de la table users:</h2>";
    echo "<pre>";
    foreach($result as $col) {
        echo $col['Field'] . " (" . $col['Type'] . ")" . ($col['Field'] === 'role' ? " <strong style='color: green;'>✓ TROUVÉE</strong>" : "") . "\n";
    }
    echo "</pre>";
} catch(Exception $e) {
    echo "Erreur: " . $e->getMessage();
}

// Test 2: Exécuter la requête statistiques
echo "<h2>Test requête statistiques:</h2>";
try {
    $stats = $pdo->query("SELECT 
        COUNT(*) as total,
        SUM(CASE WHEN `role`='admin' THEN 1 ELSE 0 END) as admins,
        SUM(CASE WHEN `role`='recruteur' THEN 1 ELSE 0 END) as recruiters,
        SUM(CASE WHEN `role`='candidat' THEN 1 ELSE 0 END) as candidates,
        SUM(CASE WHEN is_blocked=1 THEN 1 ELSE 0 END) as blocked
    ")->fetch();
    
    echo "<pre>";
    print_r($stats);
    echo "</pre>";
    echo "<p style='color: green;'><strong>✓ Requête réussie!</strong></p>";
} catch(PDOException $e) {
    echo "<p style='color: red;'><strong>✗ Erreur PDO:</strong> " . $e->getMessage() . "</p>";
}

// Test 3: Afficher quelques valeurs de role
echo "<h2>Valeurs existantes dans la colonne 'role':</h2>";
try {
    $roles = $pdo->query("SELECT DISTINCT role FROM users")->fetchAll();
    echo "<ul>";
    foreach($roles as $r) {
        echo "<li>" . htmlspecialchars($r['role']) . "</li>";
    }
    echo "</ul>";
} catch(PDOException $e) {
    echo "Erreur: " . $e->getMessage();
}
?>
