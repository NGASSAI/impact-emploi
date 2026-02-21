<?php
// CHECKLIST DE PRODUCTION - Vérification rapide
require_once 'config.php';

echo "<!DOCTYPE html>
<html>
<head>
    <title>Production Checklist</title>
    <style>
        body { font-family: Arial; margin: 20px; }
        .ok { color: green; }
        .fail { color: red; }
        code { background: #f0f0f0; padding: 2px 5px; }
    </style>
</head>
<body>
<h1>✅ Production Checklist</h1>

<h2>Fichiers créés</h2>";

$files = ['admin_activities.php', 'admin_visitors.php', 'setup_visitors_table.php'];
foreach($files as $f) {
    $exists = file_exists($f) ? '<span class="ok">✓</span>' : '<span class="fail">✗</span>';
    echo "$exists $f<br>";
}

echo "<h2>Fonctions dans config.php</h2>";
$funcs = ['sanitize', 'track_visitor'];
foreach($funcs as $f) {
    $ok = function_exists($f) ? '<span class="ok">✓</span>' : '<span class="fail">✗</span>';
    echo "$ok <code>$f()</code><br>";
}

echo "<h2>Table 'visitors'</h2>";
try {
    $result = $pdo->query("SELECT COUNT(*) FROM visitors")->fetchColumn();
    echo '<span class="ok">✓</span> Table existe (' . $result . ' enregistrements)<br>';
} catch(Exception $e) {
    echo '<span class="fail">✗</span> Table n\'existe pas<br>';
}

echo "<h2>Modifications aux fichiers</h2>";
$checks = [
    'index.php' => 'track_visitor',
    'profil.php' => 'sanitize',
    'admin_dashboard.php' => 'admin_activities.php',
    'recruteur_dashboard.php' => 'track_visitor',
    'job_detail.php' => 'track_visitor'
];

foreach($checks as $file => $keyword) {
    if(file_exists($file)) {
        $content = file_get_contents($file);
        $ok = strpos($content, $keyword) !== false ? '<span class="ok">✓</span>' : '<span class="fail">✗</span>';
        echo "$ok <code>$file</code> contient '$keyword'<br>";
    }
}

echo "</body></html>";
?>
