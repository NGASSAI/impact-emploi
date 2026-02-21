<?php
require_once 'config.php';

if(!is_logged_in()) { 
    header('Location: ' . BASE_URL . '/login.php?error=Connectez-vous pour postuler');
    exit(); 
}

if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['auth_id'])) {
    verify_csrf();
    
    $job_id = (int)($_POST['id_offre'] ?? 0);
    $user_id = $_SESSION['auth_id'];

    // Vérifications
    if(empty($job_id)) {
        header('Location: ' . BASE_URL . '/index.php?error=Offre d\'emploi invalide');
        exit();
    }

    // Vérifier que l'offre existe
    $job = $pdo->prepare("SELECT user_id FROM jobs WHERE id = ?");
    $job->execute([$job_id]);
    $job_data = $job->fetch();
    
    if(!$job_data) {
        header('Location: ' . BASE_URL . '/index.php?error=Offre d\'emploi non trouvée');
        exit();
    }

    // Vérifier si la candidature existe déjà
    $existing = $pdo->prepare("SELECT id FROM candidatures WHERE id_utilisateur = ? AND id_offre = ?");
    $existing->execute([$user_id, $job_id]);
    
    if($existing->fetch()) {
        header('Location: ' . BASE_URL . '/index.php?error=Vous avez déjà postulé à cette offre');
        exit();
    }

    // Traiter le fichier CV
    if(isset($_FILES['cv']) && $_FILES['cv']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['cv'];
        
        // Validation du fichier
        $allowed_ext = ['pdf'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        
            if(!in_array($ext, $allowed_ext)) {
            header('Location: ' . BASE_URL . '/index.php?error=Le CV doit être en PDF');
            exit();
        }
        
            if($file['size'] > 5 * 1024 * 1024) { // 5MB max
            header('Location: ' . BASE_URL . '/index.php?error=Le fichier dépasse 5MB');
            exit();
        }
        
        $filename = "cv_" . time() . "_" . $user_id . "." . $ext;
        $upload_dir = __DIR__ . "/uploads/cv/";
        
        if(!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        if(move_uploaded_file($file['tmp_name'], $upload_dir . $filename)) {
            try {
                $pdo->beginTransaction();
                
                $sql = "INSERT INTO candidatures (id_utilisateur, id_offre, nom_cv, date_postulation, statut, recruteur_id) 
                        VALUES (?, ?, ?, NOW(), 'En attente', ?)";
                $pdo->prepare($sql)->execute([$user_id, $job_id, $filename, $job_data['user_id']]);
                
                log_activity($user_id, 'apply', "Candidature pour l'offre $job_id");
                
                $pdo->commit();
                
                header('Location: ' . BASE_URL . '/index.php?success=Candidature envoyée avec succès!');
                exit();
            } catch(Exception $e) {
                $pdo->rollBack();
                header('Location: ' . BASE_URL . '/index.php?error=Erreur lors de l\'envoi de la candidature');
                exit();
            }
        } else {
            header('Location: ' . BASE_URL . '/index.php?error=Erreur lors du téléchargement du CV');
            exit();
        }
    } else {
        header('Location: ' . BASE_URL . '/index.php?error=Veuillez sélectionner un CV');
        exit();
    }
} else {
    header('Location: ' . BASE_URL . '/index.php');
    exit();
}