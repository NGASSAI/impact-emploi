<?php
require_once 'config.php';

echo "<pre>";
try {
    $result = $pdo->query("DESCRIBE users");
    $columns = $result->fetchAll();
    echo "Colonnes de la table 'users':\n";
    foreach($columns as $col) {
        echo "- " . $col['Field'] . " (" . $col['Type'] . ")\n";
    }
} catch(Exception $e) {
    echo "Erreur: " . $e->getMessage();
}
echo "</pre>";
?>
