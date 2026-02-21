<?php
require_once 'config.php';

try {
    // Créer la table visitors
    $pdo->exec("
    CREATE TABLE IF NOT EXISTS visitors (
        id INT PRIMARY KEY AUTO_INCREMENT,
        ip_address VARCHAR(45) NOT NULL,
        user_id INT NULL,
        page_visited VARCHAR(255) NOT NULL,
        user_agent VARCHAR(255),
        referer VARCHAR(255),
        is_bot BOOLEAN DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
        INDEX idx_ip (ip_address),
        INDEX idx_user_id (user_id),
        INDEX idx_created_at (created_at),
        INDEX idx_is_bot (is_bot)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    
    echo "✅ Table 'visitors' créée avec succès!<br>";
    
    // Vérifier que la table existe
    $result = $pdo->query("SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='visitors'")->fetchColumn();
    if($result > 0) {
        echo "✅ Vérification: Table 'visitors' existe<br>";
    }
    
} catch(Exception $e) {
    echo "❌ Erreur: " . htmlspecialchars($e->getMessage());
}
?>
