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
                // ÉTAPE 1: Insérer la candidature
                $sql = "INSERT INTO candidatures (id_utilisateur, id_offre, nom_cv, date_postulation, statut, recruteur_id) 
                        VALUES (?, ?, ?, NOW(), 'En attente', ?)";
                $stmt = $pdo->prepare($sql);
                $result = $stmt->execute([$user_id, $job_id, $filename, $job_data['user_id']]);
                
                if(!$result) {
                    throw new Exception("Erreur insertion candidature");
                }
                
                $candidature_id = $pdo->lastInsertId();
                
                // ÉTAPE 2: Logger l'action
                log_activity($user_id, 'apply', "Candidature #$candidature_id pour l'offre $job_id");
                
                // ÉTAPE 3: Créer notification (optionnel, ne bloque pas le processus)
                try {
                    $stmt = $pdo->prepare("SELECT u.prenom, u.nom, j.titre FROM users u JOIN jobs j ON j.id = ? WHERE u.id = ?");
                    $stmt->execute([$job_id, $user_id]);
                    $user_info = $stmt->fetch();
                    
                    if ($user_info) {
                        $notification_data = json_encode([
                            'candidature_id' => $candidature_id,
                            'candidat' => $user_info['prenom'] . ' ' . $user_info['nom'],
                            'poste' => $user_info['titre']
                        ]);
                        
                        $stmt = $pdo->prepare("
                            INSERT INTO notifications (user_id, type, title, message, data) 
                            VALUES (?, 'new_candidature', 'Nouvelle candidature reçue', ?, ?)
                        ");
                        $stmt->execute([
                            $job_data['user_id'],
                            "Nouvelle candidature de {$user_info['prenom']} {$user_info['nom']} pour le poste: {$user_info['titre']}",
                            $notification_data
                        ]);
                        
                        // Marquer la notification du recruteur comme non lue
                        $pdo->prepare("UPDATE candidatures SET notification_seen = 0 WHERE id = ?")
                             ->execute([$candidature_id]);
                    }
                } catch (Exception $notif_error) {
                    // La notification échoue mais la candidature est réussie
                    error_log("Erreur notification (candidature #$candidature_id réussie): " . $notif_error->getMessage());
                }
                
                // ÉTAPE 4: Succès
                header('Location: ' . BASE_URL . '/index.php?success=Candidature envoyée avec succès!');
                exit();
                
            } catch(Exception $e) {
                error_log("ERREUR CANDIDATURE DÉTAILLÉE: " . $e->getMessage());
                error_log("User ID: $user_id, Job ID: $job_id, File: $filename");
                header('Location: ' . BASE_URL . '/index.php?error=Erreur technique lors de l\'envoi');
                exit();
            }
        } else {
            error_log("ERREUR TÉLÉCHARGEMENT: impossible de déplacer {$file['tmp_name']} vers {$upload_dir}{$filename}");
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
?>
