<?php
require_once 'config.php';

if(!is_admin()) { 
    header('Location: ' . BASE_URL . '/index.php');
    exit('Accès refusé');
}

// Déterminer la page de redirection
$redirect = isset($_GET['redirect']) ? clean($_GET['redirect']) : 'admin_dashboard.php';
if(!in_array($redirect, ['admin_dashboard.php', 'admin_users.php'])) {
    $redirect = 'admin_dashboard.php';
}

// Bloquer ou Débloquer
if(isset($_GET['toggle_block'])) {
    $id = (int)$_GET['toggle_block'];
    
    // Vérifier que on ne se bloque pas soi-même
    if($id !== $_SESSION['auth_id']) {
        try {
            $pdo->prepare("UPDATE users SET is_blocked = NOT is_blocked WHERE id = ?")->execute([$id]);
            log_activity($_SESSION['auth_id'], 'toggle_block', "Utilisateur $id bloqué/débloqué");
        } catch(Exception $e) {
            // Silent fail
        }
    }
    header('Location: ' . BASE_URL . '/' . $redirect);
    exit();
}

// Supprimer un utilisateur
if(isset($_GET['del_user'])) {
    $id = (int)$_GET['del_user'];
    
    // Vérifier qu'on ne supprime pas soi-même
    if($id !== $_SESSION['auth_id']) {
        try {
            // Commencer une transaction pour éviter les incohérences
            $pdo->beginTransaction();
            
            // Supprimer les candidatures
            $pdo->prepare("DELETE FROM candidatures WHERE id_utilisateur = ? OR recruteur_id = ?")->execute([$id, $id]);
            
            // Supprimer les offres d'emploi
            $pdo->prepare("DELETE FROM jobs WHERE id_recruteur = ?")->execute([$id]);
            
            // Supprimer l'utilisateur
            $pdo->prepare("DELETE FROM users WHERE id = ?")->execute([$id]);
            
            // Supprimer les logs d'activité
            $pdo->prepare("DELETE FROM activity_logs WHERE user_id = ?")->execute([$id]);
            
            $pdo->commit();
            log_activity($_SESSION['auth_id'], 'delete_user', "Utilisateur $id supprimé");
        } catch(Exception $e) {
            $pdo->rollBack();
        }
    }
    header('Location: ' . BASE_URL . '/' . $redirect);
    exit();
}

// Rendre admin
if(isset($_GET['make_admin'])) {
    $id = (int)$_GET['make_admin'];
    
    try {
        $pdo->prepare("UPDATE users SET role = 'admin' WHERE id = ?")->execute([$id]);
        log_activity($_SESSION['auth_id'], 'make_admin', "Utilisateur $id promu admin");
    } catch(Exception $e) {
        // Silent fail
    }
    header('Location: ' . BASE_URL . '/' . $redirect);
    exit();
}

header('Location: ' . BASE_URL . '/' . $redirect);
exit();