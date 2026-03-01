<?php
/**
 * Script de vérification et correction des colonnes
 */

require_once 'config.php';

echo "=== Vérification des colonnes de la table candidatures ===\n\n";

try {
    $stmt = $pdo->query("SHOW COLUMNS FROM candidatures");
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "Colonnes actuelles dans 'candidatures':\n";
    foreach ($columns as $col) {
        echo "  - $col\n";
    }
    
    echo "\n=== Vérification des colonnes requises ===\n";
    
    // Vérifier candidate_notification_seen
    if (!in_array('candidate_notification_seen', $columns)) {
        echo "❌ Colonne 'candidate_notification_seen' ABSENTE - Ajout en cours...\n";
        $pdo->exec("ALTER TABLE candidatures ADD COLUMN candidate_notification_seen TINYINT(1) DEFAULT 0 AFTER updated_at");
        echo "✓ Colonne 'candidate_notification_seen' ajoutée\n";
    } else {
        echo "✓ Colonne 'candidate_notification_seen' EXISTS\n";
    }
    
    // Vérifier notification_seen
    if (!in_array('notification_seen', $columns)) {
        echo "❌ Colonne 'notification_seen' ABSENTE - Ajout en cours...\n";
        $pdo->exec("ALTER TABLE candidatures ADD COLUMN notification_seen TINYINT(1) DEFAULT 0 AFTER updated_at");
        echo "✓ Colonne 'notification_seen' ajoutée\n";
    } else {
        echo "✓ Colonne 'notification_seen' EXISTS\n";
    }
    
    echo "\n=== Vérification finale ===\n";
    $stmt = $pdo->query("SHOW COLUMNS FROM candidatures");
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "Colonnes après mise à jour:\n";
    foreach ($columns as $col) {
        echo "  - $col\n";
    }
    
} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
}
?>

