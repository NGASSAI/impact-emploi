<?php
// Script de test pour valider les changements
session_start();
require_once 'config.php';

echo "<!DOCTYPE html>
<html>
<head>
    <title>Test des changements</title>
    <style>
        body { font-family: Arial; margin: 20px; }
        .success { color: green; background: #f0f0f0; padding: 10px; margin: 10px 0; }
        .error { color: red; background: #ffe0e0; padding: 10px; margin: 10px 0; }
        .info { color: blue; background: #e0e0ff; padding: 10px; margin: 10px 0; }
        h2 { color: #333; }
    </style>
</head>
<body>
<h1>🧪 Test des Changements</h1>";

// Test 1: Vérification des fonctions
echo "<h2>✅ Test 1: Fonctions dans config.php</h2>";
if(function_exists('sanitize')) {
    echo "<div class='success'>✓ Fonction sanitize() existe</div>";
} else {
    echo "<div class='error'>✗ Fonction sanitize() manquante</div>";
}

if(function_exists('track_visitor')) {
    echo "<div class='success'>✓ Fonction track_visitor() existe</div>";
} else {
    echo "<div class='error'>✗ Fonction track_visitor() manquante</div>";
}

// Test 2: Test sanitize function
echo "<h2>✅ Test 2: Fonction sanitize()</h2>";
$test_bio = "Prénom: Jean-Claude, Résumé: ça c'est très bien! É à è";
$sanitized = sanitize($test_bio);
echo "<div class='info'>Input: " . htmlspecialchars($test_bio) . "</div>";
echo "<div class='info'>Output: " . htmlspecialchars($sanitized) . "</div>";
if($sanitized === $test_bio) {
    echo "<div class='success'>✓ sanitize() fonctionne correctement (pas de double escaping)</div>";
} else {
    echo "<div class='info'>ℹ sanitize() a modifié: " . htmlspecialchars($sanitized) . "</div>";
}

// Test 3: Fichiers créés
echo "<h2>✅ Test 3: Fichiers créés</h2>";
$files_to_check = [
    'admin_activities.php',
    'admin_visitors.php',
];
foreach($files_to_check as $file) {
    if(file_exists($file)) {
        $size = filesize($file);
        echo "<div class='success'>✓ {$file} existe ({$size} bytes)</div>";
    } else {
        echo "<div class='error'>✗ {$file} manquant</div>";
    }
}

// Test 4: Vérifier la table visitors
echo "<h2>✅ Test 4: Table visitors</h2>";
try {
    $result = $pdo->query("SELECT COUNT(*) as count FROM information_schema.TABLES WHERE TABLE_SCHEMA='" . getenv('DB_NAME') . "' AND TABLE_NAME='visitors'")->fetch();
    if($result['count'] > 0) {
        echo "<div class='success'>✓ Table 'visitors' existe déjà</div>";
        $count = $pdo->query("SELECT COUNT(*) as count FROM visitors")->fetch();
        echo "<div class='info'> Contient " . $count['count'] . " lignes</div>";
    } else {
        echo "<div class='error'>✗ Table 'visitors' n'existe pas encore</div>";
    }
} catch(Exception $e) {
    echo "<div class='error'>✗ Erreur: " . htmlspecialchars($e->getMessage()) . "</div>";
}

// Test 5: Vérifier les fichiers modifiés
echo "<h2>✅ Test 5: Fichiers modifiés</h2>";
$modified_files = [
    'config.php' => ['sanitize', 'track_visitor'],
    'admin_dashboard.php' => ['admin_activities.php', 'admin_visitors.php'],
    'profil.php' => ['sanitize'],
    'includes/header.php' => ['file_exists'],
];

foreach($modified_files as $file => $keywords) {
    if(!file_exists($file)) {
        echo "<div class='error'>✗ {$file} n'existe pas</div>";
        continue;
    }
    $content = file_get_contents($file);
    $all_found = true;
    $found_keywords = [];
    foreach($keywords as $keyword) {
        if(strpos($content, $keyword) !== false) {
            $found_keywords[] = $keyword;
        } else {
            $all_found = false;
        }
    }
    if($all_found) {
        echo "<div class='success'>✓ {$file} contient tous les keywords: " . implode(', ', $keywords) . "</div>";
    } else {
        echo "<div class='error'>✗ {$file} manque: " . implode(', ', array_diff($keywords, $found_keywords)) . "</div>";
    }
}

// Test 6: Vérifier les attributs lazy loading
echo "<h2>✅ Test 6: Lazy loading trong les fichiers</h2>";
$lazy_load_files = ['index.php', 'job_detail.php', 'recruteur_dashboard.php', 'profil.php', 'edit_job.php'];
foreach($lazy_load_files as $file) {
    if(!file_exists($file)) {
        echo "<div class='error'>✗ {$file} n'existe pas</div>";
        continue;
    }
    $content = file_get_contents($file);
    $count = substr_count($content, 'loading="lazy"');
    if($count > 0) {
        echo "<div class='success'>✓ {$file} a {$count} images avec lazy loading</div>";
    } else {
        echo "<div class='error'>✗ {$file} n'a pas de lazy loading</div>";
    }
}

echo "</body></html>";
?>
