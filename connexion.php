<?php
// On inclut les fichiers nécessaires
require_once 'includes/header.php';
require_once 'includes/config.php';
require_once 'includes/csrf.php';

$error = null;

// Si l'utilisateur est déjà connecté, on le redirige vers l'accueil
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validation CSRF
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = "⚠️ Erreur de sécurité : requête invalide.";
    } else {
        $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
        $password = $_POST['password'];

        if ($email && $password) {
            // 1. On cherche l'utilisateur par son email
            $stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            // 2. Si l'utilisateur existe et que le mot de passe est correct
            if ($user && password_verify($password, $user['password'])) {
                
                // 3. On crée les variables de session (Le "Badge" d'accès)
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['nom'] = $user['nom'];
                $_SESSION['prenom'] = $user['prenom'];
                $_SESSION['role'] = $user['role']; // Très important pour la suite !

                // 4. Redirection vers la page d'accueil
                header('Location: index.php');
                exit;
            } else {
                $error = "Identifiants incorrects.";
            }
        } else {
            $error = "Veuillez remplir tous les champs correctement.";
        }
    }
}
?>

<div class="form-card">
    <h2>Connexion</h2>
    <p style="text-align:center; margin-bottom:20px;">Accédez à Impact Emploi</p>

    <?php if($error): ?>
        <div class="alert alert-error"><?php echo $error; ?></div>
    <?php endif; ?>

    <form action="connexion.php" method="POST">
        <?php csrfField(); ?>
        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" required placeholder="votre@email.com">
        </div>

        <div class="form-group">
            <label>Mot de passe</label>
            <input type="password" name="password" required placeholder="Votre mot de passe">
        </div>

        <button type="submit">Se connecter</button>
    </form>

    <p style="margin-top:20px; text-align:center;">
        Pas encore de compte ? <a href="inscription.php">S'inscrire</a>
    </p>
</div>

<?php require_once 'includes/footer.php'; ?>