<?php
// SCRIPT À EXÉCUTER SUR INFINITYFREE
// 1. Créer un fichier create_admin_infinityfree.php
// 2. L'uploader via FTP dans le répertoire racine du site
// 3. Visiter: https://ton-domaine.com/create_admin_infinityfree.php
// 4. Supprimer le file via FTP après succès

require_once 'config.php';

$email = 'nathanzouma@gmail.com';
$password = 'nathan1234';
$nom = 'Zouma';
$prenom = 'Nathan';

echo "<h1>🔐 CRÉATION ADMIN sur InfinityFREE</h1>";

try {
    // Vérifier si l'utilisateur existe
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if($user) {
        // Mettre à jour
        $hashed = password_hash($password, PASSWORD_ARGON2ID, [
            'memory_cost' => 65536,
            'time_cost' => 4,
            'threads' => 3
        ]);
        $stmt = $pdo->prepare("UPDATE users SET password = ?, role = 'admin', updated_at = NOW() WHERE email = ?");
        $stmt->execute([$hashed, $email]);
        echo "<p>✓ Admin existant mis à jour</p>";
    } else {
        // Créer nouveau
        $hashed = password_hash($password, PASSWORD_ARGON2ID, [
            'memory_cost' => 65536,
            'time_cost' => 4,
            'threads' => 3
        ]);
        $stmt = $pdo->prepare("INSERT INTO users (nom, prenom, email, password, role, created_at) VALUES (?, ?, ?, ?, 'admin', NOW())");
        $stmt->execute([$nom, $prenom, $email, $hashed]);
        echo "<p>✓ Nouvel admin créé</p>";
    }
    
    echo "<h2>✅ Admin créé avec succès !</h2>";
    echo "<p><strong>Email:</strong> $email</p>";
    echo "<p><strong>Mot de passe:</strong> $password</p>";
    echo "<p style='color:red;'><strong>⚠️ IMPORTANT: Supprime ce fichier via FTP après utilisation !</strong></p>";
    
} catch(Exception $e) {
    echo "<h2>✗ Erreur: " . htmlspecialchars($e->getMessage()) . "</h2>";
}
?>
