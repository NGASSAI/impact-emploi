<?php
require_once 'config.php';

// Créer la table de versions du site si elle n'existe pas
try {
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS site_versions (
            id INT AUTO_INCREMENT PRIMARY KEY,
            version VARCHAR(20) NOT NULL,
            description TEXT,
            is_mandatory TINYINT(1) DEFAULT 1,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_version (version),
            INDEX idx_mandatory (is_mandatory)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ");
    
    // Insérer la version actuelle si elle n'existe pas
    $current_version = SITE_VERSION;
    $stmt = $pdo->prepare("SELECT id FROM site_versions WHERE version = ?");
    $stmt->execute([$current_version]);
    
    if (!$stmt->fetch()) {
        $stmt = $pdo->prepare("
            INSERT INTO site_versions (version, description, is_mandatory) 
            VALUES (?, ?, 1)
        ");
        $stmt->execute([
            $current_version,
            "Système de notifications temps réel avec sonnerie puissante et modal de réponse moderne"
        ]);
    }
    
    echo "✅ Table des versions du site créée avec succès!\n";
    
} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
}
?>
