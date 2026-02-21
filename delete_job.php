<?php
require_once 'config.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : null;

if(!$id) {
    header('Location: ' . BASE_URL . '/recruteur_dashboard.php');
    exit();
}

// Récupérer l'offre
$job_stmt = $pdo->prepare("SELECT * FROM jobs WHERE id = ?");
$job_stmt->execute([$id]);
$job = $job_stmt->fetch();

if(!$job) {
    header('Location: ' . BASE_URL . '/recruteur_dashboard.php?error=Offre non trouvée');
    exit();
}

// Vérifier les permissions
    if($_SESSION['auth_role'] !== 'admin' && $_SESSION['auth_id'] !== $job['user_id']) {
    header('Location: ' . BASE_URL . '/recruteur_dashboard.php?error=Accès non autorisé');
    exit();
}

try {
    $pdo->beginTransaction();
    
    // Supprimer les candidatures associées
    $pdo->prepare("DELETE FROM candidatures WHERE id_offre = ?")->execute([$id]);
    
    // Supprimer l'offre
    $pdo->prepare("DELETE FROM jobs WHERE id = ?")->execute([$id]);
    
    $pdo->commit();
    
    log_activity($_SESSION['auth_id'], 'delete_job', "Offre $id supprimée");
    
    header('Location: ' . BASE_URL . '/recruteur_dashboard.php?success=Offre supprimée');
    exit();
} catch(Exception $e) {
    $pdo->rollBack();
    header('Location: ' . BASE_URL . '/recruteur_dashboard.php?error=Erreur lors de la suppression');
    exit();
}