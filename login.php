<?php 
require_once 'config.php'; 
include 'includes/header.php'; 
?>

<div class="container flex-center" style="padding-top: 40px; padding-bottom: 40px;">
    <div class="card" style="max-width: 450px; width: 100%;">
        <div style="text-align: center; margin-bottom: 30px;">
            <h2 style="font-size: 2rem; color: var(--primary); margin-bottom: 10px;">🔐 Connexion</h2>
            <p class="text-muted">Accédez à votre compte Impact Emploi</p>
        </div>

        <?php if(isset($_GET['error'])): ?>
            <div class="alert alert-error" style="margin-bottom: 20px;">
                ✕ <?php echo htmlspecialchars($_GET['error']); ?>
            </div>
        <?php endif; ?>

        <?php if(isset($_GET['success'])): ?>
            <div class="alert alert-success" style="margin-bottom: 20px;">
                ✓ <?php echo htmlspecialchars($_GET['success']); ?>
            </div>
        <?php endif; ?>

        <form action="<?php echo BASE_URL; ?>/login_action.php" method="POST" style="display: flex; flex-direction: column; gap: 20px;">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            
            <div class="form-group">
                <label for="email">📧 Adresse Email</label>
                <input type="email" id="email" name="email" placeholder="votre@email.com" required style="padding: 14px;">
            </div>

            <div class="form-group">
                <label for="password">🔑 Mot de passe</label>
                <input type="password" id="password" name="password" placeholder="••••••••" required style="padding: 14px;">
            </div>

            <button type="submit" class="btn btn-primary btn-block" style="padding: 14px; font-size: 1rem; font-weight: 700;">
                Se Connecter
            </button>
        </form>

        <div style="margin-top: 25px; padding-top: 20px; border-top: 1px solid var(--border-color); text-align: center;">
            <p class="text-muted">Pas encore de compte?</p>
            <a href="<?php echo BASE_URL; ?>/register.php" class="btn btn-outline btn-block" style="margin-top: 10px;">
                Créer un compte
            </a>
        </div>

        <div style="margin-top: 20px; padding: 15px; background: var(--light); border-radius: 8px; font-size: 0.85rem; color: var(--text-secondary); line-height: 1.6;">
            <strong>🔒 Sécurité de votre compte:</strong><br>
            ✓ Tous les mots de passe sont cryptés<br>
            ✓ HTTPS sécurisé sur tout le site<br>
            ✓ Protection contre les attaques CSRF
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>