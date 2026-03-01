<?php
require_once 'config.php';

header('Content-Type: application/json');

if (!is_logged_in()) {
    echo json_encode(['success' => false, 'message' => 'Non autorisé']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
    exit();
}

verify_csrf();

$candidature_id = (int)($_POST['candidature_id'] ?? 0);
$user_id = $_SESSION['auth_id'];

if ($candidature_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'ID de candidature invalide']);
    exit();
}

try {
    // Vérifier que la candidature appartient bien à l'utilisateur
    $stmt = $pdo->prepare("SELECT id, nom_cv FROM candidatures WHERE id = ? AND id_utilisateur = ?");
    $stmt->execute([$candidature_id, $user_id]);
    $candidature = $stmt->fetch();
    
    if (!$candidature) {
        echo json_encode(['success' => false, 'message' => 'Candidature non trouvée']);
        exit();
    }
    
    // Récupérer le nom du fichier CV à supprimer
    $cv_file = $candidature['nom_cv'];
    
    // Supprimer le fichier CV du serveur
    $cv_path = __DIR__ . '/uploads/cv/' . $cv_file;
    if (file_exists($cv_path)) {
        unlink($cv_path);
    }
    
    // Supprimer la candidature de la base de données
    $stmt = $pdo->prepare("DELETE FROM candidatures WHERE id = ? AND id_utilisateur = ?");
    $stmt->execute([$candidature_id, $user_id]);
    
    // Supprimer les notifications associées
    $stmt = $pdo->prepare("DELETE FROM notifications WHERE data LIKE ?");
    $stmt->execute(['%"candidature_id":' . $candidature_id . '%']);
    
    // Logger l'action
    log_activity($user_id, 'delete_candidature', "Suppression de la candidature $candidature_id");
    
    echo json_encode([
        'success' => true, 
        'message' => 'Candidature supprimée avec succès!'
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false, 
        'message' => 'Erreur lors de la suppression: ' . $e->getMessage()
    ]);
}
?>
