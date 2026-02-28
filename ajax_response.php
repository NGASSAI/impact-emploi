<?php
require_once 'config.php';

// Vérifier si c'est une requête AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajax_response'])) {
    header('Content-Type: application/json');
    
    verify_csrf();
    
    $candidature_id = (int)($_POST['candidature_id'] ?? 0);
    $statut = clean($_POST['statut'] ?? '');
    $message = clean($_POST['message'] ?? '');
    
    // Validation
    if ($candidature_id <= 0 || !in_array($statut, ['En attente', 'Accepté', 'Refusé']) || empty($message)) {
        echo json_encode(['success' => false, 'message' => 'Données invalides']);
        exit();
    }
    
    try {
        // Vérifier que le recruteur a bien le droit de répondre à cette candidature
        $stmt = $pdo->prepare("SELECT recruteur_id FROM candidatures WHERE id = ?");
        $stmt->execute([$candidature_id]);
        $candidature = $stmt->fetch();
        
        if (!$candidature || $candidature['recruteur_id'] != $_SESSION['auth_id']) {
            echo json_encode(['success' => false, 'message' => 'Accès non autorisé']);
            exit();
        }
        
        // Mettre à jour la candidature
        $stmt = $pdo->prepare("UPDATE candidatures SET statut = ?, recruteur_message = ?, updated_at = NOW() WHERE id = ?");
        $stmt->execute([$statut, $message, $candidature_id]);
        
        // Logger l'action
        log_activity($_SESSION['auth_id'], 'respond_candidature', "Réponse envoyée pour candidature $candidature_id: $statut");
        
        echo json_encode(['success' => true, 'message' => 'Réponse envoyée avec succès!']);
        
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'envoi: ' . $e->getMessage()]);
    }
    exit();
}

// Redirection si accès direct
header('Location: ' . BASE_URL . '/recruteur_dashboard.php');
exit();
?>
