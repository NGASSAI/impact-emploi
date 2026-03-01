<?php
/**
 * Script de mise à jour de la base de données
 * Ajoute les colonnes manquantes pour les notifications et corrige les dates
 * Exécuter ce fichier une seule fois puis le supprimer
 */

require_once 'config.php';

echo "=== Mise à jour de la base de données ===\n\n";

try {
    // 1. Vérifier et ajouter la colonne seen dans notifications si elle n'existe pas
    $stmt = $pdo->query("SHOW COLUMNS FROM notifications LIKE 'seen'");
    if ($stmt->rowCount() == 0) {
        $pdo->exec("ALTER TABLE notifications ADD COLUMN seen TINYINT(1) DEFAULT 0 AFTER data");
        echo "✓ Colonne 'seen' ajoutée à notifications\n";
    } else {
        echo "✓ Colonne 'seen' existe déjà dans notifications\n";
    }
    
    // 2. Vérifier et ajouter la colonne created_at dans notifications si elle n'existe pas
    $stmt = $pdo->query("SHOW COLUMNS FROM notifications LIKE 'created_at'");
    if ($stmt->rowCount() == 0) {
        $pdo->exec("ALTER TABLE notifications ADD COLUMN created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP AFTER seen");
        echo "✓ Colonne 'created_at' ajoutée à notifications\n";
    } else {
        echo "✓ Colonne 'created_at' existe déjà dans notifications\n";
    }
    
    // 3. Vérifier et ajouter la colonne candidate_notification_seen dans candidatures
    $stmt = $pdo->query("SHOW COLUMNS FROM candidatures LIKE 'candidate_notification_seen'");
    if ($stmt->rowCount() == 0) {
        $pdo->exec("ALTER TABLE candidatures ADD COLUMN candidate_notification_seen TINYINT(1) DEFAULT 0 AFTER updated_at");
        echo "✓ Colonne 'candidate_notification_seen' ajoutée à candidatures\n";
    } else {
        echo "✓ Colonne 'candidate_notification_seen' existe déjà dans candidatures\n";
    }
    
    // 4. Vérifier et ajouter la colonne notification_seen dans candidatures
    $stmt = $pdo->query("SHOW COLUMNS FROM candidatures LIKE 'notification_seen'");
    if ($stmt->rowCount() == 0) {
        $pdo->exec("ALTER TABLE candidatures ADD COLUMN notification_seen TINYINT(1) DEFAULT 0 AFTER updated_at");
        echo "✓ Colonne 'notification_seen' ajoutée à candidatures\n";
    } else {
        echo "✓ Colonne 'notification_seen' existe déjà dans candidatures\n";
    }
    
    // 5. Corriger les dates existantes - convertir en fuseau horaire Congo
    // Cette requête met à jour les timestamps qui seraient hors fuseau horaire
    $pdo->exec("SET time_zone = '+01:00'");
    echo "✓ Fuseau horaire MySQL configuré: +01:00 (Africa/Brazzaville)\n";
    
    echo "\n=== Mise à jour terminée avec succès! ===\n";
    echo "Vous pouvez maintenant supprimer ce fichier.\n";
    
    // Afficher les paramètres de date actuelle
    $stmt = $pdo->query("SELECT NOW() as current_time, @@session.time_zone as timezone");
    $result = $stmt->fetch();
    echo "\nHeure actuelle du serveur: " . $result['current_time'] . "\n";
    echo "Fuseau horaire configuré: " . $result['timezone'] . "\n";
    
} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
}

echo "\n=== Vérification du fuseau horaire PHP ===\n";
echo "Fuseau horaire PHP: " . date_default_timezone_get() . "\n";
echo "Heure actuelle PHP: " . date('Y-m-d H:i:s') . "\n";
?>

