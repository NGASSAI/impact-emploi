<?php
require_once 'config.php';

echo "<h1>🔍 Diagnostic Erreur 403 - Production</h1>";

// 1. Vérifier l'environnement
echo "<h2>📊 Environnement</h2>";
echo "Production: " . ($is_production ? "OUI" : "NON") . "<br>";
echo "HTTP_HOST: " . ($_SERVER['HTTP_HOST'] ?? 'Non défini') . "<br>";
echo "DOCUMENT_ROOT: " . ($_SERVER['DOCUMENT_ROOT'] ?? 'Non défini') . "<br>";
echo "BASE_URL: " . BASE_URL . "<br>";

// 2. Vérifier les dossiers uploads
echo "<h2>📁 Structure des Dossiers</h2>";
$uploads_path = __DIR__ . '/uploads';
echo "Chemin uploads: " . $uploads_path . "<br>";
echo "Dossier uploads existe: " . (is_dir($uploads_path) ? "OUI" : "NON") . "<br>";

$cv_path = $uploads_path . '/cv';
echo "Chemin CV: " . $cv_path . "<br>";
echo "Dossier CV existe: " . (is_dir($cv_path) ? "OUI" : "NON") . "<br>";

// 3. Vérifier les permissions
echo "<h2>🔐 Permissions</h2>";
if (is_dir($cv_path)) {
    echo "Permissions CV: " . substr(sprintf('%o', fileperms($cv_path)), -4) . "<br>";
    echo "Lectible: " . (is_readable($cv_path) ? "OUI" : "NON") . "<br>";
    echo "Inscriptible: " . (is_writable($cv_path) ? "OUI" : "NON") . "<br>";
}

// 4. Vérifier les fichiers CV dans la BDD
echo "<h2>🗄️ Fichiers CV en BDD</h2>";
$stmt = $pdo->query("SELECT id, nom_cv FROM candidatures WHERE nom_cv IS NOT NULL AND nom_cv != '' LIMIT 5");
while($row = $stmt->fetch()) {
    $cv_file = $cv_path . '/' . $row['nom_cv'];
    echo "ID: {$row['id']} - CV: {$row['nom_cv']} - Fichier existe: " . (file_exists($cv_file) ? "OUI" : "NON") . "<br>";
}

// 5. Tester l'URL générée
echo "<h2>🌐 Test URLs</h2>";
$stmt = $pdo->query("SELECT nom_cv FROM candidatures WHERE nom_cv IS NOT NULL AND nom_cv != '' LIMIT 1");
$test_cv = $stmt->fetch();
if ($test_cv) {
    $cv_url = BASE_URL . '/uploads/cv/' . $test_cv['nom_cv'];
    echo "URL générée: " . $cv_url . "<br>";
    echo "URL complète: " . (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . $cv_url . "<br>";
}

// 6. Vérifier .htaccess
echo "<h2>⚙️ Configuration .htaccess</h2>";
$htaccess_path = __DIR__ . '/.htaccess';
echo "Fichier .htaccess existe: " . (file_exists($htaccess_path) ? "OUI" : "NON") . "<br>";
if (file_exists($htaccess_path)) {
    echo "Permissions .htaccess: " . substr(sprintf('%o', fileperms($htaccess_path)), -4) . "<br>";
}

echo "<h2>💡 Recommandations</h2>";
echo "<ul>";
echo "<li>Vérifier que le dossier uploads/cv a été uploadé sur le serveur</li>";
echo "<li>Vérifier les permissions CHMOD 755 pour les dossiers</li>";
echo "<li>Vérifier que le serveur autorise l'accès aux fichiers .pdf, .doc, .docx</li>";
echo "<li>Contacter l'hébergeur si les permissions sont bloquées</li>";
echo "</ul>";
?>
