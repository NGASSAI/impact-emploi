<?php
require_once 'config.php';

if(!is_logged_in()) {
    header('Location: ' . BASE_URL . '/login.php');
    exit();
}

$error = '';
$success = '';

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    
    $sujet = clean($_POST['sujet'] ?? '');
    $message = clean($_POST['message'] ?? '');
    
    // Validations
    if(strlen($sujet) < 3) {
        $error = "Le sujet doit avoir au moins 3 caractères";
    } elseif(strlen($message) < 10) {
        $error = "Le message doit avoir au moins 10 caractères";
    } else {
        try {
            $sql = "INSERT INTO feedbacks (user_id, sujet, message) VALUES (?, ?, ?)";
            $pdo->prepare($sql)->execute([$_SESSION['auth_id'], $sujet, $message]);
            
            log_activity($_SESSION['auth_id'], 'submit_feedback', "Feedback envoyé : $sujet");
            
            $success = "✓ Merci ! Votre feedback a été envoyé avec succès.";
            $_POST = []; // Vider le formulaire
        } catch(Exception $e) {
            $error = "Erreur lors de l'envoi : " . $e->getMessage();
        }
    }
}

include 'includes/header.php';
?>

<div class="container" style="max-width: 700px; padding: 40px 0;">
    <h1 style="font-size: 2rem; color: var(--primary); margin-bottom: 30px;">
        💬 Envoyer un Feedback
    </h1>

    <?php if($error): ?>
        <div class="alert alert-error" style="margin-bottom: 20px;">
            ✕ <?php echo $error; ?>
        </div>
    <?php endif; ?>

    <?php if($success): ?>
        <div class="alert alert-success" style="margin-bottom: 20px;">
            <?php echo $success; ?>
        </div>
    <?php endif; ?>

    <div class="card">
        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

            <div class="form-group">
                <label for="sujet">📌 Sujet</label>
                <input type="text" id="sujet" name="sujet" value="<?php if(isset($_POST['sujet'])) echo htmlspecialchars_decode($_POST['sujet']); ?>"
                       value="<?php echo htmlspecialchars($_POST['sujet'] ?? ''); ?>" 
                       placeholder="Ex: Demande de fonctionnalité, Signaler un bug..." 
                       required style="padding: 12px;">
                <small style="color: #666;">Minimum 3 caractères</small>
            </div>

            <div class="form-group">
                <label for="message">📝 Message</label>
                <textarea id="message" name="message" ><?php if(isset($_POST['message'])) echo htmlspecialchars_decode($_POST['message']); ?></textarea>
                          placeholder="Décrivez votre feedback en détail..."
                          required style="min-height: 180px; padding: 12px;"></textarea>
                <small style="color: #666;">Minimum 10 caractères</small>
            </div>

            <div style="display: flex; gap: 10px;">
                <button type="submit" class="btn btn-primary btn-block" style="padding: 14px;">
                    📤 Envoyer
                </button>
                <a href="<?php echo BASE_URL; ?>/index.php" class="btn btn-outline btn-block" style="padding: 14px;">
                    Annuler
                </a>
            </div>
        </form>
    </div>

    <div style="margin-top: 40px; padding: 20px; background: #f0f7ff; border-radius: 8px; border-left: 4px solid var(--primary);">
        <h3 style="color: var(--primary); margin-top: 0;">💡 Qu'est-ce qu'un feedback ?</h3>
        <p style="color: #666; margin-bottom: 0;">
            Utilisez cette formulaire pour :
        </p>
        <ul style="color: #666; margin: 10px 0;">
            <li>Signaler un bug ou un problème</li>
            <li>Demander une nouvelle fonctionnalité</li>
            <li>Partager vos suggestions d'amélioration</li>
            <li>Donner votre avis sur la plateforme</li>
        </ul>
        <p style="color: #666; margin: 0;">Votre feedback nous aide à améliorer le service ! 🙌</p>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
