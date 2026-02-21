<?php
// Test rapide de connexion InfinityFREE

// Mettez vos vrais identifiants ici TEMPORAIREMENT
$host = 'localhost';
$db   = 'VOTRE_NOM_BD_ICI';  // Ex: ab123456_impact_emploi
$user = 'VOTRE_USERNAME_ICI'; // Ex: ab123456_emploi
$pass = 'VOTRE_PASSWORD_ICI'; // Le mot de passe

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
    
    echo "✅ Connexion BD réussie !<br><br>";
    
    // Vérifier les tables
    $tables = ['users', 'jobs', 'candidatures', 'feedbacks', 'activity_logs'];
    foreach($tables as $table) {
        $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
        $exists = $stmt->rowCount() > 0 ? '✅' : '❌';
        echo "$exists Table: $table<br>";
    }
    
    // Compter les utilisateurs
    $count = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
    echo "<br>📊 Utilisateurs dans la BD: $count<br>";
    
} catch (PDOException $e) {
    echo "❌ Erreur connexion: " . $e->getMessage();
}
?>
