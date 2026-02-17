<?php
require_once 'includes/header.php';
require_once 'includes/config.php';
require_once 'includes/csrf.php';

// Sécurité : Il faut être connecté pour donner son avis
if (!isset($_SESSION['user_id'])) {
    header('Location: connexion.php');
    exit;
}

$success = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validation CSRF
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = "⚠️ Erreur de sécurité : requête invalide.";
    } else {
        $sujet = htmlspecialchars($_POST['sujet']);
        $message = htmlspecialchars($_POST['message']);
        $user_id = $_SESSION['user_id'];

        $stmt = $db->prepare("INSERT INTO feedbacks (user_id, sujet, message) VALUES (?, ?, ?)");
        $stmt->execute([$user_id, $sujet, $message]);
        $success = "Merci ! Votre message a été transmis à l'équipe Impact Emploi.";
    }
}
?>

<div class="form-card">
    <h2>Votre avis nous intéresse</h2>
    <p style="text-align:center; margin-bottom:20px;">Une idée ? Un problème ? Dites-nous tout.</p>

    <?php if($success): ?>
        <div class="alert" style="background: #d1fae5; color: #065f46; border: 1px solid #065f46;">
            <?php echo $success; ?>
        </div>
    <?php endif; ?>

    <form action="suggestions.php" method="POST">
        <?php csrfField(); ?>
        <div class="form-group">
            <label>Sujet</label>
            <select name="sujet" required>
                <option value="Suggestion">Ajouter une fonctionnalité</option>
                <option value="Difficulté">Problème pour trouver du travail</option>
                <option value="Bug">Signaler un problème technique</option>
                <option value="Autre">Autre chose</option>
            </select>
        </div>

        <div class="form-group">
            <label>Votre message</label>
            <textarea name="message" rows="5" required placeholder="Expliquez-nous en détail..." style="resize: none;"></textarea>
        </div>

        <button type="submit" style="background: var(--success);">Envoyer mon message</button>
    </form>
</div>

<?php require_once 'includes/footer.php'; ?>