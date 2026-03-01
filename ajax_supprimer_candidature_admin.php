<?php
require_once 'config.php';

header('Content-Type: application/json');

if (!is_logged_in() || !is_admin()) {
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
    // Vérifier que la candidature existe
    $stmt = $pdo->prepare("SELECT c.*, u.nom, u.prenom, j.titre, c.nom_cv 
                          FROM candidatures c 
                          JOIN users u ON c.id_utilisateur = u.id 
                          JOIN jobs j ON c.id_offre = j.id 
                          WHERE c.id = ?");
    $stmt->execute([$candidature_id]);
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
    $stmt = $pdo->prepare("DELETE FROM candidatures WHERE id = ?");
    $stmt->execute([$candidature_id]);
    
    // Supprimer les notifications associées
    $stmt = $pdo->prepare("DELETE FROM notifications WHERE data LIKE ?");
    $stmt->execute(['%"candidature_id":' . $candidature_id . '%']);
    
    // Logger l'action
    log_activity($user_id, 'delete_candidature_admin', "Suppression de la candidature $candidature_id (Admin: {$candidature['prenom']} {$candidature['nom']})");
    
    echo json_encode([
        'success' => true, 
        'message' => "Candidature de {$candidature['prenom']} {$candidature['nom']} pour le poste '{$candidature['titre']}' supprimée avec succès!"
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false, 
        'message' => 'Erreur lors de la suppression: ' . $e->getMessage()
    ]);
}
?>
