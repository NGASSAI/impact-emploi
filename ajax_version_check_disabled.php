<?php
require_once 'config.php';

header('Content-Type: application/json');

// DÉSACTIVÉ TEMPORAIREMENT - TOUJOURS RETOURNER SUCCÈS
echo json_encode([
    'success' => true,
    'up_to_date' => true,
    'message' => 'Système de mise à jour désactivé temporairement'
]);
?>
