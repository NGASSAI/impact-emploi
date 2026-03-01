<?php
require_once 'config.php';

header('Content-Type: application/json');

// Système SIMPLE de vérification de version
try {
    // Récupérer la dernière version
    $stmt = $pdo->prepare("
        SELECT version FROM site_versions 
        ORDER BY created_at DESC 
        LIMIT 1
    ");
    $stmt->execute();
    $result = $stmt->fetch();
    
    if ($result) {
        $latest_version = $result['version'];
        $current_version = $_GET['version'] ?? '1.4.0';
        
        $needs_update = version_compare($current_version, $latest_version, '<');
        
        echo json_encode([
            'success' => true,
            'up_to_date' => !$needs_update,
            'latest_version' => $latest_version,
            'message' => $needs_update ? 'Mise à jour disponible' : 'Version à jour'
        ]);
    } else {
        echo json_encode([
            'success' => true,
            'up_to_date' => true,
            'message' => 'Aucune mise à jour requise'
        ]);
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Erreur: ' . $e->getMessage()
    ]);
}
?>
