<?php
require_once 'config.php';

echo "===========================================\n";
echo "CRÉATION D'UN NOUVEL COMPTE ADMINISTRATEUR\n";
echo "===========================================\n\n";

// Données du nouvel admin
$email = 'admin@impactemploi.com';  // À modifier si besoin
$password = 'Admin@2026!';           // Mot de passe initial
$nom = 'Administrateur';
$prenom = 'Impact Emploi';
$telephone = '+242 06 000 0000';

// Hash le mot de passe avec Argon2id
$password_hash = password_hash($password, PASSWORD_ARGON2ID, [
    'memory_cost' => 65536,
    'time_cost' => 4,
    'threads' => 3
]);

// Vérifier si l'email existe déjà
$stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
$stmt->execute([$email]);
$existing = $stmt->fetch();

if($existing) {
    echo "⚠️  Un utilisateur avec cet email existe déjà.\n";
    echo "   Email: $email\n";
    echo "\n   Veuillez supprimer l'utilisateur existant ou utiliser un autre email.\n";
    exit(1);
}

// Créer le nouvel admin
try {
    $sql = "INSERT INTO users (nom, prenom, email, telephone, role, password, is_blocked, created_at, updated_at) 
            VALUES (?, ?, ?, ?, ?, ?, 0, NOW(), NOW())";
    
    $pdo->prepare($sql)->execute([
        $nom,
        $prenom,
        $email,
        $telephone,
        'admin',
        $password_hash
    ]);

    echo "✅ NOUVEL ADMINISTRATEUR CRÉÉ AVEC SUCCÈS !\n\n";
    echo "════════════════════════════════════════\n";
    echo "📧 EMAIL: " . $email . "\n";
    echo "🔐 MOT DE PASSE: " . $password . "\n";
    echo "════════════════════════════════════════\n\n";
    
    echo "⚠️  IMPORTANT:\n";
    echo "1. Notez soigneusement ces identifiants\n";
    echo "2. Connectez-vous et changez le mot de passe\n";
    echo "3. Supprimez ce fichier après création\n\n";
    
    // Obtenir l'ID du nouvel utilisateur
    $new_user = $pdo->query("SELECT id FROM users WHERE email = '$email'")->fetch();
    echo "Nouvel utilisateur ID: " . $new_user['id'] . "\n";
    
} catch(Exception $e) {
    echo "✗ Erreur lors de la création:\n";
    echo $e->getMessage() . "\n";
    exit(1);
}

echo "\n";
log_activity($new_user['id'], 'account_created', 'Compte administrateur créé automatiquement');
echo "✅ Opération terminée avec succès !\n";
?>
