<?php
require_once 'config.php';

// Vérifier si c'est une requête AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajax_response'])) {
    header('Content-Type: application/json');
    
    verify_csrf();
    
    $candidature_id = (int)($_POST['candidature_id'] ?? 0);
    // Utiliser sanitize() pour encoder correctement le message
    $statut = $_POST['statut'] ?? '';
    $message = sanitize($_POST['message'] ?? ''); // Utiliser sanitize() au lieu de clean()
    
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
        
        // Créer une notification pour le candidat
        $stmt = $pdo->prepare("SELECT id_utilisateur FROM candidatures WHERE id = ?");
        $stmt->execute([$candidature_id]);
        $candidature = $stmt->fetch();
        
        if ($candidature) {
            $stmt = $pdo->prepare("
                INSERT INTO notifications (user_id, type, title, message, data, created_at) 
                VALUES (?, 'recruiter_response', 'Réponse à votre candidature', ?, ?, NOW())
            ");
            $notification_data = json_encode([
                'candidature_id' => $candidature_id,
                'statut' => $statut,
                'message' => substr($message, 0, 100)
            ]);
            $stmt->execute([
                $candidature['id_utilisateur'],
                "Un recruteur a répondu à votre candidature. Statut: $statut",
                $notification_data
            ]);
            
            // Marquer la notification du candidat comme non lue
            $pdo->prepare("UPDATE candidatures SET candidate_notification_seen = 0 WHERE id = ?")
                 ->execute([$candidature_id]);
        }
        
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
