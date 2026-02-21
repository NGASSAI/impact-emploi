<?php
// Test de connexion à la BD
session_start();
require_once 'config.php';

echo "<!DOCTYPE html>
<html>
<head>
    <title>Test Connexion BD</title>
    <style>
        body { font-family: Arial; margin: 20px; max-width: 800px; }
        .ok { color: green; background: #f0f0f0; padding: 10px; margin: 10px 0; border-left: 4px solid green; }
        .fail { color: red; background: #ffe0e0; padding: 10px; margin: 10px 0; border-left: 4px solid red; }
        code { background: #f5f5f5; padding: 2px 5px; border-radius: 3px; }
    </style>
</head>
<body>
<h1>🧪 Test Connexion BD</h1>";

// Détection environnement
$is_prod = (
    strpos($_SERVER['HTTP_HOST'] ?? '', 'infinityfree.com') !== false ||
    strpos($_SERVER['HTTP_HOST'] ?? '', '.tc') !== false
);

echo "<h2>Environnement</h2>";
if ($is_prod) {
    echo "<div class='ok'>✓ PRODUCTION détecté</div>";
    echo "Domain: <code>" . htmlspecialchars($_SERVER['HTTP_HOST'] ?? 'N/A') . "</code><br>";
} else {
    echo "<div class='ok'>✓ LOCAL détecté</div>";
    echo "Domain: <code>" . htmlspecialchars($_SERVER['HTTP_HOST'] ?? 'localhost') . "</code><br>";
}

echo "<h2>Connexion BD</h2>";
try {
    // Test simple
    $result = $pdo->query("SELECT 1")->fetchColumn();
    if ($result === 1) {
        echo "<div class='ok'>✓ Connexion réussie!</div>";
        
        // Afficher les infos de la BD
        $tables = $pdo->query("SELECT COUNT(*) FROM information_schema.TABLES WHERE TABLE_SCHEMA=database()")->fetchColumn();
        echo "<p>Tables dans la BD: <strong>$tables</strong></p>";
        
        // Test des tables critiques
        echo "<h2>Vérification tables</h2>";
        $critical_tables = ['users', 'jobs', 'candidatures', 'visitors', 'activity_logs'];
        foreach ($critical_tables as $table) {
            $exists = $pdo->query("SELECT 1 FROM information_schema.TABLES WHERE TABLE_SCHEMA=database() AND TABLE_NAME='$table'")->fetchColumn();
            if ($exists) {
                $count = $pdo->query("SELECT COUNT(*) FROM `$table`")->fetchColumn();
                echo "<div class='ok'>✓ <code>$table</code> ($count enregistrements)</div>";
            } else {
                echo "<div class='fail'>✗ <code>$table</code> manquante</div>";
            }
        }
    }
} catch (PDOException $e) {
    echo "<div class='fail'>✗ Erreur: " . htmlspecialchars($e->getMessage()) . "</div>";
    echo "<p>Cela signifie que le serveur MySQL est inaccessible depuis votre IP.</p>";
}

echo "</body></html>";
?>
