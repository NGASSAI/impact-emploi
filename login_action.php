<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    
    $email = clean($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if(!validate_email($email)) {
        header('Location: ' . BASE_URL . '/login.php?error=Email invalide');
        exit();
    }

    if(strlen($password) < 8) {
        header('Location: ' . BASE_URL . '/login.php?error=Identifiants invalides');
        exit();
    }

    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND is_blocked = 0");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && check_password($password, $user['password'])) {
            // Authentification réussie
            $_SESSION['auth_id'] = $user['id'];
            $_SESSION['auth_role'] = $user['role'];
            $_SESSION['auth_nom'] = $user['nom'];
            $_SESSION['auth_prenom'] = $user['prenom'];
            $_SESSION['auth_email'] = $user['email'];
            $_SESSION['auth_photo'] = $user['photo_profil'] ?? 'default.png';
            
            // Log activity
            log_activity($user['id'], 'login', 'Connexion réussie');
            
            // Redirection selon le rôle
            if($user['role'] === 'admin') {
                header('Location: ' . BASE_URL . '/admin_dashboard.php');
            } elseif($user['role'] === 'recruteur') {
                header('Location: ' . BASE_URL . '/recruteur_dashboard.php');
            } elseif($user['role'] === 'candidat') {
                header('Location: ' . BASE_URL . '/candidat_dashboard.php');
            } else {
                header('Location: ' . BASE_URL . '/index.php');
            }
            exit();
        } else {
            header('Location: ' . BASE_URL . '/login.php?error=Identifiants invalides ou compte bloqué');
            exit();
        }
    } catch(Exception $e) {
        header('Location: ' . BASE_URL . '/login.php?error=Une erreur est survenue');
        exit();
    }
} else {
    header('Location: ' . BASE_URL . '/login.php');
    exit();
}