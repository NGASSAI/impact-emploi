<?php
// Test de connexion InfinityFREE
echo "🔧 Test de connexion à InfinityFREE...\n\n";

$host = 'sql111.infinityfree.com';
$db   = 'if0_41179665_impact_db';
$user = 'if0_41179665'; 
$pass = '12345nAthaNdDs';

try {
    echo "Host: $host\n";
    echo "Database: $db\n";
    echo "User: $user\n";
    echo "\n⏳ Tentative de connexion...\n\n";
    
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    
    echo "✅ CONNEXION RÉUSSIE !\n\n";
    
    // Vérifier les tables
    echo "📊 Vérification des tables:\n";
    $tables = ['users', 'jobs', 'candidatures', 'feedbacks', 'activity_logs'];
    foreach($tables as $table) {
        $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
        $exists = $stmt->rowCount() > 0 ? '✅' : '❌';
        echo "  $exists $table\n";
    }
    
    // Compter les utilisateurs
    echo "\n📋 Données:\n";
    $count_users = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
    $count_jobs = $pdo->query("SELECT COUNT(*) FROM jobs")->fetchColumn();
    $count_candidatures = $pdo->query("SELECT COUNT(*) FROM candidatures")->fetchColumn();
    
    echo "  👥 Utilisateurs: $count_users\n";
    echo "  💼 Offres d'emploi: $count_jobs\n";
    echo "  📝 Candidatures: $count_candidatures\n";
    
    echo "\n✅ Tout est bon ! La BD InfinityFREE fonctionne.\n";
    
} catch (PDOException $e) {
    echo "❌ ERREUR DE CONNEXION !\n\n";
    echo "Erreur: " . $e->getMessage() . "\n\n";
    echo "Vérifiez:\n";
    echo "  - Les identifiants sont corrects\n";
    echo "  - Le hostname est exact\n";
    echo "  - Le serveur InfinityFREE MySQL est actif\n";
} catch (Exception $e) {
    echo "❌ ERREUR: " . $e->getMessage();
}
?>
