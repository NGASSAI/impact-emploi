<?php
/**
 * Test complet - Simule les requêtes qui causaient l'erreur
 */

require_once 'config.php';

echo "=== TEST COMPLET DES REQUÊTES ===\n\n";

$test_passed = true;

// Test 1: Simuler ajax_response.php
echo "Test 1: ajax_response.php - UPDATE candidate_notification_seen = 0\n";
echo "   Requête: UPDATE candidatures SET candidate_notification_seen = 0 WHERE id = ?\n";
try {
    $stmt = $pdo->prepare("UPDATE candidatures SET candidate_notification_seen = 0 WHERE id = ?");
    // Tester avec un ID bidon mais la requête doit être préparée correctement
    $stmt->execute([999]);
    echo "   ✓ Requête préparée et exécutée avec succès\n";
} catch (Exception $e) {
    echo "   ✗ ERREUR: " . $e->getMessage() . "\n";
    $test_passed = false;
}

// Test 2: Simuler chat.php
echo "\nTest 2: chat.php - UPDATE candidate_notification_seen = 0\n";
echo "   Requête: UPDATE candidatures SET candidate_notification_seen = 0 WHERE id = ?\n";
try {
    $stmt = $pdo->prepare("UPDATE candidatures SET candidate_notification_seen = 0 WHERE id = ?");
    $stmt->execute([999]);
    echo "   ✓ Requête préparée et exécutée avec succès\n";
} catch (Exception $e) {
    echo "   ✗ ERREUR: " . $e->getMessage() . "\n";
    $test_passed = false;
}

// Test 3: ajax_notifications.php - UPDATE candidate_notification_seen = 1
echo "\nTest 3: ajax_notifications.php - UPDATE candidate_notification_seen = 1\n";
echo "   Requête: UPDATE candidatures SET candidate_notification_seen = 1 WHERE id_utilisateur = ? AND candidate_notification_seen = 0\n";
try {
    $stmt = $pdo->prepare("UPDATE candidatures SET candidate_notification_seen = 1 WHERE id_utilisateur = ? AND candidate_notification_seen = 0");
    $stmt->execute([999]);
    echo "   ✓ Requête préparée et exécutée avec succès\n";
} catch (Exception $e) {
    echo "   ✗ ERREUR: " . $e->getMessage() . "\n";
    $test_passed = false;
}

// Test 4: SELECT avec candidate_notification_seen
echo "\nTest 4: SELECT avec candidate_notification_seen\n";
echo "   Requête: SELECT * FROM candidatures WHERE candidate_notification_seen = 0\n";
try {
    $stmt = $pdo->query("SELECT * FROM candidatures WHERE candidate_notification_seen = 0 LIMIT 1");
    $result = $stmt->fetch();
    echo "   ✓ Requête exécutée avec succès\n";
    if ($result) {
        echo "   ✓ Données récupérées (ID: " . $result['id'] . ")\n";
    }
} catch (Exception $e) {
    echo "   ✗ ERREUR: " . $e->getMessage() . "\n";
    $test_passed = false;
}

// Test 5: JOIN avec candidate_notification_seen
echo "\nTest 5: JOIN - ajax_notifications.php\n";
echo "   Requête: SELECT c.*, j.titre FROM candidatures c JOIN jobs j ON c.id_offre = j.id WHERE c.id_utilisateur = ? AND c.candidate_notification_seen = 0\n";
try {
    $stmt = $pdo->prepare("SELECT c.*, j.titre FROM candidatures c JOIN jobs j ON c.id_offre = j.id WHERE c.id_utilisateur = ? AND c.candidate_notification_seen = 0");
    $stmt->execute([999]);
    echo "   ✓ Requête JOIN exécutée avec succès\n";
} catch (Exception $e) {
    echo "   ✗ ERREUR: " . $e->getMessage() . "\n";
    $test_passed = false;
}

echo "\n" . str_repeat("=", 40) . "\n";
if ($test_passed) {
    echo "✓ TOUS LES TESTS ONT RÉUSSI!\n";
    echo "L'erreur 'Column not found: candidate_notification_seen' est corrigée.\n";
} else {
    echo "✗ CERTAINS TESTS ONT ÉCHOUÉ\n";
}
echo str_repeat("=", 40) . "\n";
?>

