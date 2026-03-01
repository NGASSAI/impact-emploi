<?php
require_once 'config.php';

header('Content-Type: application/json');

// Vérifier si l'utilisateur est connecté
if (!is_logged_in()) {
    echo json_encode(['success' => false, 'message' => 'Non autorisé']);
    exit();
}

$user_id = $_SESSION['auth_id'];
$user_role = $_SESSION['auth_role'];

// Récupérer les notifications non lues
if ($user_role === 'admin' || $user_role === 'recruteur') {
    // Notifications pour admin: toutes les activités importantes
    $stmt = $pdo->prepare("
        SELECT * FROM notifications 
        WHERE user_id = ? AND seen = 0
        ORDER BY created_at DESC
        LIMIT 10
    ");
    $stmt->execute([$user_id]);
    $notifications = $stmt->fetchAll();
    
    // Marquer comme vues
    $stmt = $pdo->prepare("UPDATE notifications SET seen = 1 WHERE user_id = ? AND seen = 0");
    $stmt->execute([$user_id]);
    
} elseif ($user_role === 'recruteur') {
    // Notifications pour recruteur: nouvelles candidatures
    $stmt = $pdo->prepare("
        SELECT c.id, u.prenom, u.nom, j.titre, c.date_postulation, 'new_candidature' as type
        FROM candidatures c 
        JOIN users u ON c.id_utilisateur = u.id 
        JOIN jobs j ON c.id_offre = j.id 
        WHERE c.recruteur_id = ? AND c.notification_seen = 0
        ORDER BY c.date_postulation DESC
        LIMIT 10
    ");
    $stmt->execute([$user_id]);
    $notifications = $stmt->fetchAll();
    
    // Marquer comme vues
    $stmt = $pdo->prepare("UPDATE candidatures SET notification_seen = 1 WHERE recruteur_id = ? AND notification_seen = 0");
    $stmt->execute([$user_id]);
    
} elseif ($user_role === 'candidat') {
    // Notifications pour candidat: réponses recruteur
    $stmt = $pdo->prepare("
        SELECT c.id, j.titre, c.recruteur_message, c.statut, 'candidature_response' as type
        FROM candidatures c 
        JOIN jobs j ON c.id_offre = j.id 
        WHERE c.id_utilisateur = ? AND c.candidate_notification_seen = 0 AND c.recruteur_message IS NOT NULL
        ORDER BY c.date_postulation DESC
        LIMIT 10
    ");
    $stmt->execute([$user_id]);
    $notifications = $stmt->fetchAll();
    
    // Marquer comme vues
    $stmt = $pdo->prepare("UPDATE candidatures SET candidate_notification_seen = 1 WHERE id_utilisateur = ? AND candidate_notification_seen = 0");
    $stmt->execute([$user_id]);
} else {
    $notifications = [];
}

echo json_encode([
    'success' => true,
    'notifications' => $notifications,
    'count' => count($notifications)
]);
?>
