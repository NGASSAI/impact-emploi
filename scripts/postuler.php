<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../includes/config.php';

// ===== PROTECTION =====
if (!isset($_SESSION['user_id'])) {
    header('Location: ../connexion.php?error=connexion');
    exit();
}

// ===== TRAITEMENT DE LA CANDIDATURE =====
if (isset($_POST['submit_postule']) && isset($_FILES['cv_file'])) {
    try {
        $id_user = intval($_SESSION['user_id']);
        $id_offre = intval($_POST['id_offre']);
        
        // Vérifier que l'offre existe
        $check = $db->prepare("SELECT id FROM jobs WHERE id = ?");
        $check->execute([$id_offre]);
        if ($check->rowCount() === 0) {
            header("Location: ../voir_offre.php?id=$id_offre&error=non_trouve");
            exit();
        }
        
        $file = $_FILES['cv_file'];
        $fileName = $file['name'];
        $fileTmpName = $file['tmp_name'];
        $fileError = $file['error'];
        $fileSize = $file['size'];

        // Validation du fichier - Vérifier error code first
        if ($fileError !== UPLOAD_ERR_OK) {
            // Mapper les erreurs PHP
            $errorCodes = [
                UPLOAD_ERR_INI_SIZE => 'fichier_trop_gros',
                UPLOAD_ERR_FORM_SIZE => 'fichier_trop_gros',
                UPLOAD_ERR_PARTIAL => 'fichier',
                UPLOAD_ERR_NO_FILE => 'fichier',
                UPLOAD_ERR_NO_TMP_DIR => 'fichier',
                UPLOAD_ERR_CANT_WRITE => 'fichier',
                UPLOAD_ERR_EXTENSION => 'fichier',
            ];
            $error = $errorCodes[$fileError] ?? 'fichier';
            header("Location: ../voir_offre.php?id=$id_offre&error=$error");
            exit();
        }

        // Vérifier le fichier existe
        if (!file_exists($fileTmpName) || !is_uploaded_file($fileTmpName)) {
            header("Location: ../voir_offre.php?id=$id_offre&error=fichier");
            exit();
        }

        // Validation de l'extension
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        if ($fileExt !== 'pdf') {
            header("Location: ../voir_offre.php?id=$id_offre&error=format_pdf");
            exit();
        }

        // Validation de la taille
        $maxSize = 5 * 1024 * 1024; // 5 Mo
        if ($fileSize > $maxSize) {
            header("Location: ../voir_offre.php?id=$id_offre&error=fichier_trop_gros");
            exit();
        }

        // Vérifier qu'il n'y a pas d'upload en double
        $check_dup = $db->prepare("SELECT id FROM candidatures WHERE id_utilisateur = ? AND id_offre = ?");
        $check_dup->execute([$id_user, $id_offre]);
        if ($check_dup->rowCount() > 0) {
            header("Location: ../voir_offre.php?id=$id_offre&error=deja_postule");
            exit();
        }

        // Créer le répertoire s'il n'existe pas
        $uploadDir = '../assets/uploads/cv';
        if (!is_dir($uploadDir)) {
            if (!mkdir($uploadDir, 0777, true)) {
                error_log("Impossible de créer le répertoire: $uploadDir");
                header("Location: ../voir_offre.php?id=$id_offre&error=dossier");
                exit();
            }
        }

        // Vérifier que le dossier est accessible en écriture
        if (!is_writable($uploadDir)) {
            // Essayer de changer les permissions
            @chmod($uploadDir, 0777);
            if (!is_writable($uploadDir)) {
                error_log("Répertoire non writable: $uploadDir");
                header("Location: ../voir_offre.php?id=$id_offre&error=permissions");
                exit();
            }
        }

        // Générer un nom unique et sécurisé
        $newFileName = "cv_" . $id_user . "_" . time() . "_" . bin2hex(random_bytes(4)) . ".pdf";
        $fileDestination = $uploadDir . '/' . $newFileName;

        // Vérifier le type MIME (sécurité supplémentaire)
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $fileTmpName);
        
        if ($mimeType !== 'application/pdf') {
            header("Location: ../voir_offre.php?id=$id_offre&error=format_pdf");
            exit();
        }

        // Déplacer le fichier
        if (!move_uploaded_file($fileTmpName, $fileDestination)) {
            error_log("Impossible de déplacer le fichier: $fileTmpName vers $fileDestination");
            header("Location: ../voir_offre.php?id=$id_offre&error=fichier");
            exit();
        }

        // Vérifier que le fichier a bien été créé
        if (!file_exists($fileDestination)) {
            error_log("Le fichier n'existe pas après move_uploaded_file: $fileDestination");
            header("Location: ../voir_offre.php?id=$id_offre&error=fichier");
            exit();
        }

        // ===== ENREGISTREMENT EN BASE DE DONNÉES =====
        $sql = "INSERT INTO candidatures (id_utilisateur, id_offre, nom_cv) 
                VALUES (?, ?, ?)";
        $stmt = $db->prepare($sql);
        $stmt->execute([$id_user, $id_offre, $newFileName]);

        // Rediriger vers l'offre avec un message de succès
        header("Location: ../voir_offre.php?id=$id_offre&success=postule");
        exit();

    } catch (PDOException $e) {
        // Log l'erreur détaillée
        error_log("=== ERREUR PDO - CANDIDATURE ===");
        error_log("Message: " . $e->getMessage());
        error_log("Code: " . $e->getCode());
        error_log("SQLSTATE: " . $e->errorInfo[0]);
        error_log("Erreur SQL: " . $e->errorInfo[2]);
        error_log("User ID: $id_user");
        error_log("Job ID: $id_offre");
        error_log("Fichier: $newFileName");
        error_log("=============================");
        
        // En développement, afficher le détail pour debug
        $isDev = (strpos($_SERVER['HTTP_HOST'] ?? '', 'localhost') !== false || 
                  strpos($_SERVER['HTTP_HOST'] ?? '', '127.0.0.1') !== false);
        
        if ($isDev) {
            // Afficher l'erreur pour debug local
            error_log("ERREUR BD (Dev Mode): " . $e->getMessage());
        }
        
        header("Location: ../voir_offre.php?id=$id_offre&error=base_donnees");
        exit();
    } catch (Exception $e) {
        error_log("Erreur générale postuler.php: " . $e->getMessage());
        header("Location: ../voir_offre.php?id=$id_offre&error=fichier");
        exit();
    }
}
?>