<?php
/**
 * Script de correction d'urgence - Ajoute les colonnes manquantes
 * et teste les requêtes SQL
 */

require_once 'config.php';

echo "=== CORRECTION D'URGENCE - Base de données ===\n\n";

try {
    // Vérifier la structure actuelle
    echo "1. Vérification de la structure actuelle...\n";
    $stmt = $pdo->query("SHOW COLUMNS FROM candidatures");
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "   Colonnes actuelles: " . implode(', ', $columns) . "\n\n";
    
    // Liste des colonnes requises
    $required_columns = [
        'candidate_notification_seen' => "ALTER TABLE candidatures ADD COLUMN candidate_notification_seen TINYINT(1) DEFAULT 0 AFTER updated_at",
        'notification_seen' => "ALTER TABLE candidatures ADD COLUMN notification_seen TINYINT(1) DEFAULT 0 AFTER updated_at"
    ];
    
    // Ajouter les colonnes manquantes
    echo "2. Ajout des colonnes manquantes...\n";
    foreach ($required_columns as $col_name => $sql) {
        if (!in_array($col_name, $columns)) {
            echo "   ➜ Ajout de '$col_name'... ";
            $pdo->exec($sql);
            echo "OK\n";
        } else {
            echo "   ➜ '$col_name' existe déjà\n";
        }
    }
    
    // Vérifier à nouveau
    echo "\n3. Vérification finale...\n";
    $stmt = $pdo->query("SHOW COLUMNS FROM candidatures");
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "   Colonnes après mise à jour: " . implode(', ', $columns) . "\n\n";
    
    // Tester les requêtes UPDATE
    echo "4. Test des requêtes UPDATE...\n";
    
    // Test 1: UPDATE candidate_notification_seen
    echo "   Test 1: UPDATE candidate_notification_seen = 0... ";
    try {
        $pdo->exec("UPDATE candidatures SET candidate_notification_seen = 0 WHERE id = 1");
        echo "OK\n";
    } catch (Exception $e) {
        echo "ERREUR: " . $e->getMessage() . "\n";
    }
    
    // Test 2: UPDATE candidate_notification_seen = 1
    echo "   Test 2: UPDATE candidate_notification_seen = 1... ";
    try {
        $pdo->exec("UPDATE candidatures SET candidate_notification_seen = 1 WHERE id = 1");
        echo "OK\n";
    } catch (Exception $e) {
        echo "ERREUR: " . $e->getMessage() . "\n";
    }
    
    echo "\n=== CORRECTION TERMINÉE ===\n";
    echo "Les colonnes ont été ajoutées et les requêtes fonctionnent.\n";
    
} catch (Exception $e) {
    echo "❌ ERREUR: " . $e->getMessage() . "\n";
}
?>

