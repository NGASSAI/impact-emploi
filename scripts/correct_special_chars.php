<?php
/**
 * Script de correction des caractères spéciaux dans les messages
 * Ce script corrige le double encodage des messages dans la base de données
 * Exécuter ce fichier une seule fois puis le supprimer
 */

require_once 'config.php';

echo "=== Correction des caractères spéciaux ===\n\n";

try {
    // 1. Corriger les messages dans la table candidatures
    $stmt = $pdo->query("SELECT id, recruteur_message FROM candidatures WHERE recruteur_message IS NOT NULL AND recruteur_message != ''");
    $messages = $stmt->fetchAll();

    $msgUpdated = 0;
    foreach ($messages as $msg) {
        if (!empty($msg['recruteur_message'])) {
            // Vérifier si le message contient des entités HTML (signe de double encodage)
            if (strpos($msg['recruteur_message'], '&') !== false || strpos($msg['recruteur_message'], '&#') !== false) {
                // Décoder le double encodage
                $decoded = html_entity_decode($msg['recruteur_message'], ENT_QUOTES, 'UTF-8');
                // Nettoyer
                $cleaned = strip_tags(trim($decoded));

                if ($cleaned !== $msg['recruteur_message']) {
                    $update = $pdo->prepare("UPDATE candidatures SET recruteur_message = ? WHERE id = ?");
                    $update->execute([$cleaned, $msg['id']]);
                    $msgUpdated++;
                    echo "Corrigé: Candidature #{$msg['id']}\n";
                }
            }
        }
    }
    echo "Messages corrigés dans candidatures: $msgUpdated\n";

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
                    echo "Corrigé: Notification #{$notif['id']}\n";
                }
            }
        }
    }
    echo "Messages corrigés dans notifications: $notifUpdated\n";

    echo "\n=== Correction terminée avec succès! ===\n";
    echo "\nRecommandations:\n";
    echo "- La fonction display_message() a été mise à jour dans config.php\n";
    echo "- Les nouveaux messages seront correctement affichés\n";
    echo "- Vous pouvez supprimer ce fichier après exécution\n";

} catch (PDOException $e) {
    echo "Erreur: " . $e->getMessage() . "\n";
}
?>

