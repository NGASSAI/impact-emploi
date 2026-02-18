<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../includes/config.php';

// ===== PROTECTION : Seul l'admin (ID 1) peut supprimer =====
if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] != 1) {
    http_response_code(403);
    header('Location: ../index.php?error=permissions');
    exit();
}

try {
    // ===== SUPPRESSION D'UNE OFFRE =====
    if (isset($_GET['delete_offre'])) {
        $id = intval($_GET['delete_offre']);
        
        // Vérifier que l'offre existe
        $check = $db->prepare("SELECT id FROM jobs WHERE id = ?");
        $check->execute([$id]);
        if ($check->rowCount() === 0) {
            header("Location: ../admin_dashboard.php?error=non_trouve");
            exit();
        }

        $sql = "DELETE FROM jobs WHERE id = ?";
        $stmt = $db->prepare($sql);
        $stmt->execute([$id]);
        
        header("Location: ../admin_dashboard.php?success=offre_supprimee");
        exit();
    }

    // ===== SUPPRESSION D'UNE CANDIDATURE =====
    if (isset($_GET['delete_cand'])) {
        $id = intval($_GET['delete_cand']);
        
        // Récupérer le fichier CV avant suppression (pour le supprimer physiquement)
        $get_file = $db->prepare("SELECT nom_cv FROM candidatures WHERE id = ?");
        $get_file->execute([$id]);
        $file = $get_file->fetch();
        
        if (!$file) {
            header("Location: ../admin_dashboard.php?error=non_trouve");
            exit();
        }

        // Supprimer le fichier PDF du serveur
        $filePath = "../assets/uploads/cv/" . basename($file['nom_cv']);
        if (file_exists($filePath)) {
            @unlink($filePath);
        }

        // Supprimer l'enregistrement de la base de données
        $sql = "DELETE FROM candidatures WHERE id = ?";
        $stmt = $db->prepare($sql);
        $stmt->execute([$id]);
        
        header("Location: ../admin_dashboard.php?success=candidature_supprimee");
        exit();
    }

    // Aucune action spécifiée
    header('Location: ../admin_dashboard.php');
    exit();

} catch (PDOException $e) {
    error_log("Erreur suppression: " . $e->getMessage());
    header("Location: ../admin_dashboard.php?error=fichier");
    exit();
}
?>