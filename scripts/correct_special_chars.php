<?php
/**
 * Script de correction des caractères spéciaux dans les messages
 * Ce script corrige le double encodage des messages dans la base de données
 * Exécuter ce fichier une seule fois puis le supprimer
 */

require_once 'config.php';

echo "=== Correction des caractères spéciaux ===\n\n";

$updated = 0;
$errors = 0;

try {
    // 1. Corriger les messages dans la table candidatures
    $stmt = $pdo->query("SELECT id, recruteur_message FROM candidatures WHERE recruteur_message IS NOT NULL AND recruteur_message != ''");
    $messages = $stmt->fetchAll();

    foreach ($messages as $msg) {
        if (!empty($msg['recruteur_message'])) {
            // Décoder le double encodage
            $decoded = html_entity_decode($msg['recruteur_message'], ENT_QUOTES, 'UTF-8');
            // Nettoyer
            $cleaned = strip_tags(trim($decoded));
            
            if ($cleaned !== $msg['recruteur_message']) {
                $update = $pdo->prepare("UPDATE candidatures SET recruteur_message = ? WHERE id = ?");
                $update->execute([$cleaned, $msg['id']]);
                $updated++;
                echo "✓ Candidature #{$msg['id']} corrigée\n";
            }
        }
    }
    
    echo "\n--- Résultats ---\n";
    echo "Messages corrigés dans candidatures: $updated\n";
    
    // 2. Corriger les messages dans la table notifications
    $stmt = $pdo->query("SELECT id, message FROM notifications WHERE message IS NOT NULL AND message != ''");
    $notifications = $stmt->fetchAll();
    
    $notifUpdated = 0;
    foreach ($notifications as $notif) {
        if (!empty($notif['message'])) {
            // Vérifier si le message contient des entités HTML
            if (strpos($notif['message'], '&') !== false || strpos($notif['message'], '&#') !== false) {
                $decoded = html_entity_decode($notif['message'], ENT_QUOTES, 'UTF-8');
                $cleaned = strip_tags(trim($decoded));
                
                if ($cleaned !== $notif['message']) {
                    $update = $pdo->prepare("UPDATE notifications SET message = ? WHERE id = ?");
                    $update->execute([$cleaned, $notif['id']]);
                    $notifUpdated++;
                }
            }
        }
    }
    
    echo "Messages corrigés dans notifications: $notifUpdated\n";
    echo "\n=== Correction terminée avec succès! ===\n";
    echo "Vous pouvez maintenant supprimer ce fichier.\n";
    
} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
    $errors++;
}

// Suggestions pour améliorer l'encodage à l'avenir
echo "\n=== Recommandations ===\n";
echo "- Utiliser sanitize() pour enregistrer les messages\n";
echo "- Utiliser display_message() pour afficher les messages\n";
echo "- Le fuseau horaire Africa/Brazzaville est déjà configuré dans config.php\n";
?>

