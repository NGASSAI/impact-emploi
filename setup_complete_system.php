<?php
require_once 'config.php';

// Vérifier si les tables nécessaires existent
try {
    // Vérifier table notifications
    $stmt = $pdo->query("SHOW TABLES LIKE 'notifications'");
    if ($stmt->rowCount() == 0) {
        // Créer la table notifications
        $pdo->exec("
            CREATE TABLE notifications (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                type VARCHAR(50) NOT NULL,
                title VARCHAR(255) NOT NULL,
                message TEXT,
                data JSON,
                seen TINYINT(1) DEFAULT 0,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_user_id (user_id),
                INDEX idx_seen (seen),
                INDEX idx_type (type),
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");
        echo "✅ Table 'notifications' créée avec succès<br>";
    } else {
        echo "✅ Table 'notifications' existe déjà<br>";
    }
    
    // Vérifier table activity_logs
    $stmt = $pdo->query("SHOW TABLES LIKE 'activity_logs'");
    if ($stmt->rowCount() == 0) {
        // Créer la table activity_logs
        $pdo->exec("
            CREATE TABLE activity_logs (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT,
                action VARCHAR(100) NOT NULL,
                description TEXT,
                ip_address VARCHAR(45),
                user_agent TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_user_id (user_id),
                INDEX idx_action (action),
                INDEX idx_created_at (created_at),
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");
        echo "✅ Table 'activity_logs' créée avec succès<br>";
    } else {
        echo "✅ Table 'activity_logs' existe déjà<br>";
    }
    
    // Vérifier table site_versions
    $stmt = $pdo->query("SHOW TABLES LIKE 'site_versions'");
    if ($stmt->rowCount() == 0) {
        // Créer la table site_versions
        $pdo->exec("
            CREATE TABLE site_versions (
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
        echo "✅ Table 'site_versions' créée avec succès<br>";
    } else {
        echo "✅ Table 'site_versions' existe déjà<br>";
    }
    
    // Insérer la version actuelle si elle n'existe pas
    $stmt = $pdo->prepare("SELECT id FROM site_versions WHERE version = ?");
    $stmt->execute(['1.4.1']);
    if ($stmt->rowCount() == 0) {
        $stmt = $pdo->prepare("INSERT INTO site_versions (version, description, is_mandatory) VALUES (?, ?, ?)");
        $stmt->execute(['1.4.1', 'Version stable avec notifications temps réel et suppression admin', 1]);
        echo "✅ Version 1.4.1 insérée dans site_versions<br>";
    } else {
        echo "✅ Version 1.4.1 existe déjà<br>";
    }
    
    // Vérifier si la colonne candidate_notification_seen existe dans candidatures
    $stmt = $pdo->query("SHOW COLUMNS FROM candidatures LIKE 'candidate_notification_seen'");
    if ($stmt->rowCount() == 0) {
        $pdo->exec("ALTER TABLE candidatures ADD COLUMN candidate_notification_seen TINYINT(1) DEFAULT 0 AFTER notification_seen");
        echo "✅ Colonne 'candidate_notification_seen' ajoutée à candidatures<br>";
    } else {
        echo "✅ Colonne 'candidate_notification_seen' existe déjà<br>";
    }
    
    // Vérifier si la colonne notification_seen existe dans candidatures
    $stmt = $pdo->query("SHOW COLUMNS FROM candidatures LIKE 'notification_seen'");
    if ($stmt->rowCount() == 0) {
        $pdo->exec("ALTER TABLE candidatures ADD COLUMN notification_seen TINYINT(1) DEFAULT 0 AFTER recruteur_message");
        echo "✅ Colonne 'notification_seen' ajoutée à candidatures<br>";
    } else {
        echo "✅ Colonne 'notification_seen' existe déjà<br>";
    }
    
    echo "<br><strong>🎉 Toutes les tables sont prêtes pour le système complet!</strong>";
    
} catch (Exception $e) {
    echo "<br><strong>❌ Erreur:</strong> " . $e->getMessage();
}
?>
