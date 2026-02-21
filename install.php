<?php
/**
 * Impact Emploi - Assistant d'Installation
 * Accès : http://localhost/test1/install.php
 * À SUPPRIMER après l'installation!
 */

require_once 'config.php';

$step = isset($_GET['step']) ? (int)$_GET['step'] : 1;
$installed = false;
$error = null;
$success = null;

// Vérifier si la base est déjà installée
try {
    $check = $pdo->query("SELECT COUNT(*) FROM users");
    $installed = true;
} catch(Exception $e) {
    $installed = false;
}

// Traitement de l'installation
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    if(isset($_POST['install_db'])) {
        try {
            // Créer les tables
            $sql = file_get_contents(__DIR__ . '/database.sql');
            
            // Diviser le SQL en plusieurs requêtes
            $queries = array_filter(array_map('trim', explode(';', $sql)));
            
            foreach($queries as $query) {
                if(!empty($query)) {
                    $pdo->exec($query);
                }
            }
            
            $success = "Base de données installée avec succès!";
            $installed = true;
        } catch(Exception $e) {
            $error = "Erreur lors de l'installation: " . $e->getMessage();
        }
    }
    
    if(isset($_POST['create_admin'])) {
        try {
            $email = clean($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $confirm = $_POST['confirm'] ?? '';
            
            if(!validate_email($email)) {
                $error = "Email invalide";
            } elseif(strlen($password) < 8) {
                $error = "Le mot de passe doit avoir au moins 8 caractères";
            } elseif($password !== $confirm) {
                $error = "Les mots de passe ne correspondent pas";
            } else {
                $hashed = hash_password($password);
                $pdo->prepare("INSERT INTO users (nom, prenom, email, password, role, is_blocked) 
                             VALUES (?, ?, ?, ?, ?, 0)")
                   ->execute(['Admin', 'Compte', $email, $hashed, 'admin']);
                
                $success = "Compte administrateur créé! Vous pouvez maintenant vous connecter.";
            }
        } catch(Exception $e) {
            $error = "Erreur: " . $e->getMessage();
        }
    }
}

// Vérifications système
$checks = [
    'PHP Version' => ['OK' => version_compare(PHP_VERSION, '7.4', '>='), 'version' => PHP_VERSION],
    'MySQL PDO' => ['OK' => extension_loaded('pdo_mysql'), 'info' => 'Extension MySQL PDO'],
    'Dossier uploads/cv' => ['OK' => is_writable('uploads/cv'), 'path' => 'uploads/cv'],
    'Dossier uploads/profiles' => ['OK' => is_writable('uploads/profiles'), 'path' => 'uploads/profiles'],
];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Installation - Impact Emploi</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css">
    <style>
        .install-container { max-width: 700px; margin: 40px auto; padding: 0 20px; }
        .step { margin: 20px 0; }
        .check-item { display: flex; align-items: center; padding: 10px; margin: 10px 0; border-radius: 8px; background: var(--light); }
        .check-item.ok { border-left: 4px solid var(--success); }
        .check-item.error { border-left: 4px solid var(--danger); }
        .check-status { font-weight: 700; margin-left: 10px; }
    </style>
</head>
<body style="padding: 20px;">
    <div class="install-container">
        <h1 style="color: var(--primary); text-align: center; margin-bottom: 40px;">⚡ Installation Impact Emploi</h1>

        <?php if($success): ?>
            <div class="alert alert-success" style="margin-bottom: 20px;">
                ✓ <?php echo htmlspecialchars($success); ?>
            </div>
        <?php endif; ?>

        <?php if($error): ?>
            <div class="alert alert-error" style="margin-bottom: 20px;">
                ✕ <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <!-- Vérifications Système -->
        <div class="card" style="margin-bottom: 30px;">
            <h2 style="color: var(--primary); margin-bottom: 20px;">✓ Vérifications Système</h2>
            
            <?php foreach($checks as $name => $check): ?>
                <div class="check-item <?php echo $check['OK'] ? 'ok' : 'error'; ?>">
                    <span><?php echo $check['OK'] ? '✓' : '✕'; ?></span>
                    <div style="flex: 1; margin: 0 15px;">
                        <strong><?php echo $name; ?></strong>
                        <div style="font-size: 0.85rem; color: var(--text-secondary);">
                            <?php echo $check['version'] ?? $check['path'] ?? $check['info'] ?? ''; ?>
                        </div>
                    </div>
                    <span class="check-status" style="color: <?php echo $check['OK'] ? 'var(--success)' : 'var(--danger)'; ?>;">
                        <?php echo $check['OK'] ? 'OK' : 'ERREUR'; ?>
                    </span>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Étape 1: Installation BD -->
        <?php if(!$installed): ?>
            <div class="card" style="margin-bottom: 30px;">
                <h2 style="color: var(--primary); margin-bottom: 20px;">📊 Étape 1: Base de Données</h2>
                <p class="text-muted" style="margin-bottom: 20px;">
                    Créer les tables et la structure de la base de données.
                </p>
                <form method="POST">
                    <button type="submit" name="install_db" class="btn btn-primary btn-block" style="padding: 14px;">
                        📦 Installer la Base de Données
                    </button>
                </form>
            </div>
        <?php else: ?>
            <!-- Étape 2: Créer Compte Admin -->
            <div class="card" style="margin-bottom: 30px;">
                <h2 style="color: var(--primary); margin-bottom: 20px;">👤 Étape 2: Compte Administrateur</h2>
                <p class="text-muted" style="margin-bottom: 20px;">
                    Créer le compte administrateur pour accéder au tableau de bord.
                </p>
                <form method="POST">
                    <div class="form-group">
                        <label for="email">📧 Email Admin</label>
                        <input type="email" id="email" name="email" placeholder="admin@impact-emploi.com" required style="padding: 12px;">
                    </div>

                    <div class="form-group">
                        <label for="password">🔐 Mot de Passe (min. 8 caractères)</label>
                        <input type="password" id="password" name="password" placeholder="••••••••" required style="padding: 12px;">
                    </div>

                    <div class="form-group">
                        <label for="confirm">🔐 Confirmer le Mot de Passe</label>
                        <input type="password" id="confirm" name="confirm" placeholder="••••••••" required style="padding: 12px;">
                    </div>

                    <button type="submit" name="create_admin" class="btn btn-primary btn-block" style="padding: 14px;">
                        ✅ Créer le Compte Admin
                    </button>
                </form>
            </div>

            <!-- Prochaines Étapes -->
            <div class="card">
                <h2 style="color: var(--primary); margin-bottom: 20px;">🚀 Prochaines Étapes</h2>
                <ol style="font-size: 1rem; line-height: 1.8;">
                    <li>Supprimer le fichier <strong>install.php</strong> du serveur</li>
                    <li>Se connecter avec votre compte admin</li>
                    <li>Inviter des recruteurs et candidats</li>
                    <li>Configurer les offres d'emploi</li>
                    <li>Monitoring via le tableau de bord</li>
                </ol>
                
                <div style="margin-top: 30px; padding: 20px; background: var(--light); border-radius: 8px;">
                    <strong>⚠️ IMPORTANT:</strong> Supprimez le fichier <strong>install.php</strong> après installation!
                </div>

                <a href="<?php echo BASE_URL; ?>/index.php" class="btn btn-primary btn-block" style="margin-top: 20px; padding: 14px;">
                    Aller à l'Accueil
                </a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>