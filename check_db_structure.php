<?php
require_once 'config.php';

echo "📊 STRUCTURE DE LA BASE DE DONNÉES LOCALE\n\n";

$tables = ['users', 'jobs', 'candidatures', 'feedbacks', 'activity_logs'];

foreach($tables as $table) {
    $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
    if($stmt->rowCount() > 0) {
        $count = $pdo->query("SELECT COUNT(*) FROM $table")->fetchColumn();
        echo "✅ $table: $count lignes\n";
        
        // Afficher les colonnes
        $cols = $pdo->query("DESCRIBE $table")->fetchAll();
        echo "   Colonnes: " . implode(', ', array_column($cols, 'Field')) . "\n\n";
    } else {
        echo "❌ $table: N'existe pas\n";
    }
}
?>
