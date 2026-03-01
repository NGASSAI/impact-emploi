<?php
require_once 'config.php';

// DÉSACTIVER TEMPORAIREMENT LE SYSTÈME DE MISE À JOUR
// Pour débloquer l'accès au site

try {
    // Supprimer toutes les versions obligatoires pour débloquer
    $pdo->exec("DELETE FROM site_versions WHERE is_mandatory = 1");
    
    // Insérer une version non obligatoire
    $stmt = $pdo->prepare("
        INSERT INTO site_versions (version, description, is_mandatory) 
        VALUES (?, ?, 0)
    ");
    $stmt->execute([
        '1.4.0',
        'Version stable - Système désactivé temporairement'
    ]);
    
    echo "✅ Système de mise à jour désactivé temporairement!\n";
    echo "✅ Le site est maintenant accessible!\n";
    
} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
}
?>
