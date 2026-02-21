<?php
// Script pour créer la table visitors
require_once 'config.php';

echo "🔧 Création table visitors...\n\n";

try {
    $sql = "CREATE TABLE IF NOT EXISTS visitors (
        id INT AUTO_INCREMENT PRIMARY KEY,
        ip_address VARCHAR(45) NOT NULL,
        user_id INT NULL,
        page_visited VARCHAR(255) NOT NULL,
        user_agent VARCHAR(255),
        referer VARCHAR(255),
        is_bot TINYINT(1) DEFAULT 0,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
        INDEX (ip_address),
        INDEX (created_at),
        INDEX (user_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    
    $pdo->exec($sql);
    echo "✅ Table visitors créée avec succès\n\n";
    
    // Ajouter fonction de tracking au config.php
    echo "📝 Instructions: Ajouter ceci au début de chaque page (après config.php):\n\n";
    echo "<?php\n";
    echo "track_visitor(\$pdo, \$_SERVER['REQUEST_URI']);\n";
    echo "?>\n\n";
    
} catch(Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
}
?>
