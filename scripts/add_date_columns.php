<?php
/**
 * Script d'Administration : Ajouter les colonnes de dates manquantes
 * ====================================================================
 * 
 * Ce script ajoute les colonnes de dates aux tables existantes si elles
 * n'existent pas déjà. Il est notamment utile lors de la première
 * installation ou après une mise à jour de la base de données.
 * 
 * Colonnes traitées :
 *   - users.date_inscription : date/heure d'inscription de l'utilisateur
 *   - jobs.date_publication : date/heure de publication de l'offre
 * 
 * Exécution : php scripts/add_date_columns.php
 */

require_once __DIR__ . '/../includes/config.php';

try {
    $changes = [];

    // Liste des tables et colonnes de dates à vérifier/ajouter
    $pairs = [
        ['table' => 'users', 'col' => 'date_inscription'],
        ['table' => 'jobs', 'col' => 'date_publication'],
    ];

    // Parcourir chaque table et colonne
    foreach ($pairs as $p) {
        $table = $p['table'];
        $col = $p['col'];

        // Vérifier si la colonne existe déjà
        $res = $db->query("SHOW COLUMNS FROM `$table` LIKE '$col'");
        $exists = $res ? $res->fetch() : false;

        if ($exists) {
            echo "✓ La colonne $col existe déjà sur la table $table\n";
            $changes[] = "exists:$table.$col";
            continue;
        }

        // Ajouter la colonne avec valeur par défaut : date/heure actuelle
        $sql = "ALTER TABLE `$table` ADD COLUMN `$col` DATETIME DEFAULT CURRENT_TIMESTAMP";
        $db->exec($sql);
        echo "✓ Colonne $col ajoutée à la table $table\n";

        // Mettre à jour les enregistrements existants (NULL → CURRENT_TIMESTAMP)
        $db->exec("UPDATE `$table` SET `$col` = CURRENT_TIMESTAMP WHERE `$col` IS NULL");
        echo "✓ Enregistrements existants mis à jour sur $table (colonne $col)\n";

        $changes[] = "added:$table.$col";
    }

    echo "\n✓ Opération terminée. Résumé :\n" . implode("\n", $changes) . "\n";

} catch (PDOException $e) {
    echo "❌ Erreur PDO : " . $e->getMessage() . "\n";
    exit(1);
}

?>
