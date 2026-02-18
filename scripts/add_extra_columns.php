<?php
/**
 * Script d'Administration : Ajouter les colonnes supplémentaires
 * ==============================================================
 * 
 * Ce script ajoute les colonnes de fonctionnalités extras à la base de
 * données si elles n'existent pas déjà. Utile lors de mises à jour
 * de version ou d'activation de nouvelles fonctionnalités.
 * 
 * Colonnes traitées :
 *   - users.has_whatsapp (TINYINT) : User a autorisé le contact WhatsApp
 *   - jobs.image (VARCHAR) : Chemin de l'image de l'offre d'emploi
 * 
 * Exécution : php scripts/add_extra_columns.php
 */

require_once __DIR__ . '/../includes/config.php';

try {
    // ===== 1. Ajouter la colonne "has_whatsapp" à la table "users" =====
    // Cette colonne indique si un utilisateur accepte d'être contacté via WhatsApp
    $res = $db->query("SHOW COLUMNS FROM `users` LIKE 'has_whatsapp'");
    $exists = $res ? $res->fetch() : false;
    
    if ($exists) {
        echo "✓ La colonne 'has_whatsapp' existe déjà sur la table 'users'\n";
    } else {
        // Ajouter la colonne avec valeur par défaut 0 (désactivé)
        $db->exec("ALTER TABLE `users` ADD COLUMN `has_whatsapp` TINYINT(1) NOT NULL DEFAULT 0");
        echo "✓ Colonne 'has_whatsapp' ajoutée à la table 'users'\n";
    }

    // ===== 2. Ajouter la colonne "image" à la table "jobs" =====
    // Cette colonne stocke le nom de fichier image pour chaque offre d'emploi
    $res2 = $db->query("SHOW COLUMNS FROM `jobs` LIKE 'image'");
    $exists2 = $res2 ? $res2->fetch() : false;
    
    if ($exists2) {
        echo "✓ La colonne 'image' existe déjà sur la table 'jobs'\n";
    } else {
        // Ajouter la colonne avec valeur NULL par défaut (pas d'image)
        $db->exec("ALTER TABLE `jobs` ADD COLUMN `image` VARCHAR(255) DEFAULT NULL");
        echo "✓ Colonne 'image' ajoutée à la table 'jobs'\n";
    }

    echo "\n✓ Opération terminée avec succès.\n";

} catch (PDOException $e) {
    // En cas d'erreur PDO, afficher le message et arrêter
    echo "❌ Erreur PDO : " . $e->getMessage() . "\n";
    exit(1);
}

?>
