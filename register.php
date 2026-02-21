<?php 
require_once 'config.php';
include 'includes/header.php'; 

$error = null;

// Old values to repopulate form fields after validation
$old = [
    'nom' => '',
    'prenom' => '',
    'email' => '',
    'telephone' => '',
    'role' => 'candidat'
];

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    
    $nom = clean($_POST['nom'] ?? '');
    $prenom = clean($_POST['prenom'] ?? '');
    $email = clean($_POST['email'] ?? '');
    $tel = clean($_POST['telephone'] ?? '');
    $role = $_POST['role'] ?? 'candidat';
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';

    // Préparer valeurs pour repopulation
    $old['nom'] = $nom;
    $old['prenom'] = $prenom;
    $old['email'] = $email;
    $old['telephone'] = $tel;
    $old['role'] = $role;

    // Validation (associative pour cibler les champs)
    $errors = [];

    if(strlen($nom) < 2) $errors['nom'] = "Le nom doit avoir au moins 2 caractères";
    if(strlen($prenom) < 2) $errors['prenom'] = "Le prénom doit avoir au moins 2 caractères";
    if(!validate_email($email)) $errors['email'] = "Email invalide";
    if(!validate_phone($tel)) $errors['telephone'] = "Numéro de téléphone invalide";
    if(strlen($password) < 8) $errors['password'] = "Le mot de passe doit avoir au moins 8 caractères";
    if($password !== $password_confirm) $errors['password_confirm'] = "Les mots de passe ne correspondent pas";
    if(!in_array($role, ['candidat', 'recruteur'])) $errors['role'] = "Rôle invalide";

    if(empty($errors)) {
        // vérifier unicité email avant insertion
        $existing = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
        $existing->execute([$email]);
        if($existing->fetchColumn() > 0) {
            $error = "Cet email est déjà utilisé.";
            // clear the email field only
            $old['email'] = '';
        } else {
                try {
                    $hashed_password = hash_password($password);
                        $sql = "INSERT INTO users (nom, prenom, email, telephone, role, password, photo_profil, is_blocked) 
                            VALUES (?, ?, ?, ?, ?, ?, 'default.png', 0)";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute([$nom, $prenom, $email, $tel, $role, $hashed_password]);

                    // Log activity
                    log_activity($pdo->lastInsertId(), 'register', "Nouvel utilisateur: $role");

                    header('Location: ' . BASE_URL . '/login.php?success=' . urlencode('Compte créé avec succès! Connectez-vous.'));
                    exit();
                } catch(Exception $e) {
                    $error = "Une erreur est survenue lors de l'inscription.";
                }
            }
    } else {
        // Vider uniquement les champs invalides pour que l'utilisateur ne ressaisisse que ceux-ci
        foreach($errors as $field => $msg) {
            if(in_array($field, ['nom','prenom','email','telephone','role'])) {
                $old[$field] = '';
            }
        }
        $error = implode("<br>", $errors);
    }
}
?>

<div class="container flex-center" style="padding-top: 40px; padding-bottom: 40px;">
    <div class="card" style="max-width: 500px; width: 100%;">
        <div style="text-align: center; margin-bottom: 30px;">
            <h2 style="font-size: 2rem; color: var(--primary); margin-bottom: 10px;">📝 Créer un compte</h2>
            <p class="text-muted">Rejoignez la plateforme Impact Emploi</p>
        </div>

        <?php if($error): ?>
            <div class="alert alert-error" style="margin-bottom: 20px;">
                ✕ <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form method="POST" style="display: flex; flex-direction: column; gap: 18px;">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <div class="form-group" style="margin-bottom: 0;">
                    <label for="nom">👤 Nom</label>
                    <input type="text" id="nom" name="nom" placeholder="Votre nom" required style="padding: 12px;" value="<?php echo htmlspecialchars($old['nom'] ?? ''); ?>">
                </div>
                <div class="form-group" style="margin-bottom: 0;">
                    <label for="prenom">👤 Prénom</label>
                    <input type="text" id="prenom" name="prenom" placeholder="Votre prénom" required style="padding: 12px;" value="<?php echo htmlspecialchars($old['prenom'] ?? ''); ?>">
                </div>
            </div>

            <div class="form-group">
                <label for="email">📧 Email</label>
                <input type="email" id="email" name="email" placeholder="votre@email.com" required style="padding: 12px;" value="<?php echo htmlspecialchars($old['email'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="telephone">📱 Téléphone</label>
                <input type="tel" id="telephone" name="telephone" placeholder="+242 XXX XXX XXX" required style="padding: 12px;" value="<?php echo htmlspecialchars($old['telephone'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="role">🎯 Je suis...</label>
                <select name="role" id="role" required style="padding: 12px;">
                    <option value="candidat" <?php echo (isset($old['role']) && $old['role'] === 'candidat') ? 'selected' : ''; ?>>Chercheur d'emploi / Candidat</option>
                    <option value="recruteur" <?php echo (isset($old['role']) && $old['role'] === 'recruteur') ? 'selected' : ''; ?>>Recruteur / Entreprise</option>
                </select>
            </div>

            <div class="form-group">
                <label for="password">🔑 Mot de passe</label>
                <input type="password" id="password" name="password" placeholder="Minimum 8 caractères" required style="padding: 12px;">
            </div>

            <div class="form-group">
                <label for="password_confirm">🔑 Confirmer le mot de passe</label>
                <input type="password" id="password_confirm" name="password_confirm" placeholder="Répétez votre mot de passe" required style="padding: 12px;">
            </div>

            <button type="submit" class="btn btn-primary btn-block" style="padding: 14px; font-size: 1rem; font-weight: 700;">
                S'inscrire
            </button>
        </form>

        <div style="margin-top: 25px; padding-top: 20px; border-top: 1px solid var(--border-color); text-align: center;">
            <p class="text-muted">Vous avez déjà un compte?</p>
            <a href="<?php echo BASE_URL; ?>/login.php" class="btn btn-outline btn-block" style="margin-top: 10px;">
                Se connecter
            </a>
        </div>

        <div style="margin-top: 20px; padding: 15px; background: var(--light); border-radius: 8px; font-size: 0.85rem; color: var(--text-secondary); line-height: 1.6;">
            <strong>ℹ️ Conditions d'inscription:</strong><br>
            ✓ Vous devez avoir au moins 18 ans<br>
            ✓ Email valide et unique<br>
            ✓ Vos données sont sécurisées et ne seront pas partagées
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>