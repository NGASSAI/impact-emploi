<?php
// Vérifier la structure avec les identifiants LOCAUX uniquement pour tester
$host = 'localhost';
$db   = 'impact_emploi';
$user = 'root'; 
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    
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
} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage();
}
?>
