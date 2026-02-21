<?php
// Diagnostic de connexion à la BD
echo "<!DOCTYPE html><html><head><title>DB Diagnostic</title>
<style>body{font-family:Arial;margin:20px}.ok{color:green}.fail{color:red}</style>
</head><body><h1>🔍 Diagnostic Connexion BD</h1>";

// Vérifier quel host est configuré
$config_file = __FILE__;
$config_content = file_get_contents('config.php');

preg_match('/\$host\s*=\s*[\'"]([^\'"]+)/', $config_content, $host_match);
preg_match('/\$user\s*=\s*[\'"]([^\'"]+)/', $config_content, $user_match);
preg_match('/\$db\s*=\s*[\'"]([^\'"]+)/', $config_content, $db_match);

echo "<h2>Configuration actuelle:</h2>";
echo "Host: <code>" . ($host_match[1] ?? 'NON TROUVÉ') . "</code><br>";
echo "User: <code>" . ($user_match[1] ?? 'NON TROUVÉ') . "</code><br>";
echo "Database: <code>" . ($db_match[1] ?? 'NON TROUVÉ') . "</code><br>";

echo "<h2>Serveur actuel:</h2>";
echo "Domain: <code>" . ($_SERVER['HTTP_HOST'] ?? 'LOCAL') . "</code><br>";
echo "IP Client: <code>" . ($_SERVER['REMOTE_ADDR'] ?? 'N/A') . "</code><br>";

echo "<h2>Vérification:</h2>";

// Test connexion locale
if(($host_match[1] ?? null) === 'localhost') {
    echo "<span class='fail'>✗ PROBLÈME: config.php utilise 'localhost'</span><br>";
    echo "Sur production, doit être: <code>sql111.infinityfree.com</code><br>";
} elseif(strpos($_SERVER['HTTP_HOST'] ?? '', 'infinityfree') !== false || strpos($_SERVER['HTTP_HOST'] ?? '', '.tc') !== false) {
    echo "<span class='ok'>✓ Production détectée, host correct</span><br>";
} else {
    echo "Local ou développement<br>";
}

echo "</body></html>";
?>
