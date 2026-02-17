<?php
// On inclut le header qui contient déjà session_start() et le début du HTML
require_once 'includes/header.php';
// On inclut la connexion à la base de données
require_once 'includes/config.php';
// On inclut la protection CSRF
require_once 'includes/csrf.php';

$error = null;
$success = null;

// Vérification : Est-ce que le formulaire a été envoyé ?
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validation CSRF
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = "⚠️ Erreur de sécurité : requête invalide.";
    } else {
        // 1. Nettoyage des données (Protection XSS)
        $nom       = htmlspecialchars(trim($_POST['nom']));
        $prenom    = htmlspecialchars(trim($_POST['prenom']));
        $email     = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
        $telephone = htmlspecialchars(trim($_POST['telephone']));
        $role      = $_POST['role']; // 'chercheur' ou 'recruteur'
        $password  = $_POST['password'];
        $confirm   = $_POST['confirm_password'];
        $has_whatsapp = isset($_POST['has_whatsapp']) ? 1 : 0;

        // 2. Validation des champs
        if (!$email) {
            $error = "Format d'email invalide.";
        } elseif ($password !== $confirm) {
            $error = "Les mots de passe ne correspondent pas.";
        } elseif (strlen($password) < 6) {
            $error = "Le mot de passe doit faire au moins 6 caractères.";
        } else {
            // 3. Vérifier si l'email existe déjà en base de données
            $check = $db->prepare("SELECT id FROM users WHERE email = ?");
            $check->execute([$email]);
            
            if ($check->fetch()) {
                $error = "Cet email est déjà utilisé par un autre compte.";
            } else {
                // 4. Hachage du mot de passe (Sécurité Pro)
                $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

                // 5. Insertion dans la base de données
                try {
                    $stmt = $db->prepare("INSERT INTO users (nom, prenom, email, telephone, role, password, has_whatsapp) VALUES (?, ?, ?, ?, ?, ?, ?)");
                    $stmt->execute([$nom, $prenom, $email, $telephone, $role, $hashedPassword, $has_whatsapp]);

                    // Connexion automatique après inscription
                    $newId = $db->lastInsertId();
                    $_SESSION['user_id'] = $newId;
                    $_SESSION['role'] = $role;
                    header('Location: profil.php');
                    exit;
                } catch (PDOException $e) {
                    $error = "Une erreur est survenue lors de l'enregistrement.";
                    error_log("PDO Error in inscription.php: " . $e->getMessage());
                }
            }
        }
    }
}
?>

<div class="form-card">
    <h2>Créer un compte</h2>
    <p style="text-align:center; margin-bottom:20px;">Rejoignez Impact Emploi</p>

    <?php if($error): ?>
        <div class="alert alert-error"><?php echo $error; ?></div>
    <?php endif; ?>

    <?php if($success): ?>
        <div class="alert" style="background: #d1fae5; color: #065f46; border: 1px solid #065f46;">
            <?php echo $success; ?>
        </div>
    <?php endif; ?>

    <form action="inscription.php" method="POST">
        <?php csrfField(); ?>
        <div class="form-group">
            <label>Vous êtes :</label>
            <select name="role" required>
                <option value="chercheur">Je cherche un emploi</option>
                <option value="recruteur">Je souhaite recruter / Poster une offre</option>
            </select>
        </div>

        <div class="form-group">
            <label>Nom</label>
            <input type="text" name="nom" required placeholder="Votre nom">
        </div>

        <div class="form-group">
            <label>Prénom</label>
            <input type="text" name="prenom" required placeholder="Votre prénom">
        </div>

        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" required placeholder="exemple@mail.com">
        </div>

        <div class="form-group">
            <label>Téléphone</label>
            <input type="tel" name="telephone" placeholder="Ex: 0600000000">
        </div>

        <div class="form-group">
            <label><input type="checkbox" name="has_whatsapp" value="1"> J'ai WhatsApp (autorise les contacts via WhatsApp)</label>
        </div>

        <div class="form-group">
            <label>Mot de passe</label>
            <input type="password" name="password" required placeholder="6 caractères minimum">
        </div>

        <div class="form-group">
            <label>Confirmez le mot de passe</label>
            <input type="password" name="confirm_password" required>
        </div>

        <button type="submit">S'inscrire gratuitement</button>
    </form>

    <p style="margin-top:20px; text-align:center;">
        Déjà inscrit ? <a href="connexion.php">Se connecter</a>
    </p>
</div>

<?php 
// On inclut le footer pour fermer les balises HTML
require_once 'includes/footer.php'; 
?>