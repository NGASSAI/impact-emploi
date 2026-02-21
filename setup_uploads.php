<?php
// Script pour vérifier et créer les répertoires uploads

$dirs_to_create = [
    __DIR__ . '/uploads',
    __DIR__ . '/uploads/jobs',
    __DIR__ . '/uploads/profiles',
    __DIR__ . '/uploads/cv'
];

echo "🔧 Vérification des répertoires uploads...\n\n";

foreach($dirs_to_create as $dir) {
    if(!is_dir($dir)) {
        echo "❌ Répertoire manquant: $dir\n";
        echo "   Création en cours...\n";
        if(mkdir($dir, 0777, true)) {
            echo "   ✅ Créé avec succès\n";
        } else {
            echo "   ❌ Erreur lors de la création\n";
        }
    } else {
        echo "✅ Répertoire existe: $dir\n";
        
        // Vérifier les permissions
        $perms = decoct(fileperms($dir) & 0777);
        echo "   Permissions: $perms\n";
        
        // Tenter de chmod à 777 si nécessaire
        if(!is_writable($dir)) {
            echo "   ⚠️  Répertoire non accessible en écriture\n";
            if(chmod($dir, 0777)) {
                echo "   ✅ Permissions mises à jour à 777\n";
            } else {
                echo "   ❌ Impossible de modifier les permissions\n";
            }
        } else {
            echo "   ✅ Accessible en écriture\n";
        }
    }
    echo "\n";
}

echo "✅ Vérification terminée!\n";
echo "\n⚠️  N'oubliez pas de supprimer ce fichier (setup_uploads.php) après utilisation.\n";
?>
