<?php
require_once 'config.php';

// Créer les tables de notifications si elles n'existent pas
try {
    // Ajouter les colonnes de notification à la table candidatures
    $pdo->exec("
        ALTER TABLE candidatures 
        ADD COLUMN IF NOT EXISTS notification_seen TINYINT(1) DEFAULT 0,
        ADD COLUMN IF NOT EXISTS candidate_notification_seen TINYINT(1) DEFAULT 0
    ");
    
    // Table de notifications système
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS notifications (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            type ENUM('new_candidature', 'recruiter_response', 'system') NOT NULL,
            title VARCHAR(255) NOT NULL,
            message TEXT NOT NULL,
            data JSON NULL,
            is_read TINYINT(1) DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            INDEX idx_user_unread (user_id, is_read),
            INDEX idx_created (created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ");
    
    echo "✅ Tables de notifications créées avec succès!\n";
    
} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
}
?>
