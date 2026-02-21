<?php
require_once 'config.php';

if(!is_logged_in()) {
    header('Location: ' . BASE_URL . '/login.php');
    exit();
}

$error = null;
$success = null;

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Récupérer l'utilisateur
    $user_stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
    $user_stmt->execute([$_SESSION['auth_id']]);
    $user = $user_stmt->fetch();

    // Validations
    if(!$user) {
        $error = "Utilisateur introuvable";
    } elseif(!check_password($current_password, $user['password'])) {
        $error = "Le mot de passe actuel est incorrect";
    } elseif(strlen($new_password) < 8) {
        $error = "Le nouveau mot de passe doit avoir au moins 8 caractères";
    } elseif($new_password !== $confirm_password) {
        $error = "Les mots de passe ne correspondent pas";
    } else {
        try {
            $hashed = hash_password($new_password);
            $stmt = $pdo->prepare("UPDATE users SET password = ?, updated_at = NOW() WHERE id = ?");
            $stmt->execute([$hashed, $_SESSION['auth_id']]);
            
            log_activity($_SESSION['auth_id'], 'change_password', 'Mot de passe changé');
            
            $success = "Mot de passe changé avec succès!";
        } catch(Exception $e) {
            $error = "Erreur lors de la mise à jour du mot de passe: " . $e->getMessage();
        }
    }
}

include 'includes/header.php';
?>

<div class="container" style="max-width: 500px; padding: 40px 0;">
    <div class="card">
        <h1 style="font-size: 1.8rem; color: var(--primary); margin-bottom: 30px;text-align:center;">
            🔐 Changer le Mot de Passe
        </h1>

        <?php if($error): ?>
            <div class="alert alert-error" style="margin-bottom: 20px;">
                ✕ <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <?php if($success): ?>
            <div class="alert alert-success" style="margin-bottom: 20px;">
                ✓ <?php echo htmlspecialchars($success); ?>
            </div>
            <a href="<?php echo BASE_URL; ?>/profil.php" class="btn btn-primary btn-block" style="padding: 12px;">Retour au Profil</a>
        <?php else: ?>
            <form method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

                <div class="form-group">
                    <label for="current_password">🔑 Mot de Passe Actuel</label>
                    <input type="password" id="current_password" name="current_password" placeholder="••••••••" required style="padding: 12px;">
                </div>

                <div class="form-group">
                    <label for="new_password">🔐 Nouveau Mot de Passe</label>
                    <input type="password" id="new_password" name="new_password" placeholder="Minimum 8 caractères" required style="padding: 12px;">
                </div>

                <div class="form-group">
                    <label for="confirm_password">🔐 Confirmer le Mot de Passe</label>
                    <input type="password" id="confirm_password" name="confirm_password" placeholder="Répétez le nouveau mot de passe" required style="padding: 12px;">
                </div>

                <button type="submit" class="btn btn-primary btn-block" style="padding: 14px;">
                    Changer le Mot de Passe
                </button>
            </form>

            <div style="text-align: center; margin-top: 20px;">
                <a href="<?php echo BASE_URL; ?>/profil.php" style="color: var(--primary); text-decoration: none;">← Retour au Profil</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>