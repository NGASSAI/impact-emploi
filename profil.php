<?php
require_once 'config.php';
track_visitor($pdo, 'profil.php');

if(!is_logged_in()) { 
    header('Location: ' . BASE_URL . '/login.php'); 
    exit(); 
}

$user_id = $_SESSION['auth_id'];

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    
    try {
        // Traiter l'upload de la photo
        if(!empty($_FILES['photo']['name'])){
            $file = $_FILES['photo'];
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
            
            if(!in_array($file['type'], $allowed_types) || $file['size'] > 2 * 1024 * 1024) {
                header('Location: ' . BASE_URL . '/profil.php?error=Photo invalide (max 2MB, JPEG/PNG/GIF)');
                exit();
            }
            
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = "profile_" . time() . "_" . $user_id . "." . $ext;
            $upload_dir = __DIR__ . "/uploads/profiles/";
            
            if(!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            
            if(move_uploaded_file($file['tmp_name'], $upload_dir . $filename)) {
                $pdo->prepare("UPDATE users SET photo_profil = ? WHERE id = ?")->execute([$filename, $user_id]);
                $_SESSION['auth_photo'] = $filename;
            }
        }
        
        // Mettre à jour les autres champs
        $updates = [];
        $params = [];
        
        if(!empty($_POST['phone'] ?? '')) {
            if(validate_phone($_POST['phone'])) {
                $updates[] = "telephone = ?";
                $params[] = $_POST['phone'];
            }
        }
        
        if(!empty($_POST['bio'] ?? '')) {
            $updates[] = "bio = ?";
            $params[] = sanitize($_POST['bio']);
        }
        
        if(!empty($updates)) {
            // Some DB schemas may not have `updated_at`; avoid updating it explicitly to maintain compatibility
            $params[] = $user_id;
            $pdo->prepare("UPDATE users SET " . implode(", ", $updates) . " WHERE id = ?")->execute($params);
        }
        
        log_activity($user_id, 'update_profile', 'Mise à jour du profil');
        header('Location: ' . BASE_URL . '/profil.php?success=Profil mis à jour');
        exit();
    } catch(Exception $e) {
        header('Location: ' . BASE_URL . '/profil.php?error=Erreur lors de la mise à jour');
        exit();
    }
}

$user = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$user->execute([$user_id]);
$u = $user->fetch();

include 'includes/header.php';
?>

<div class="container" style="max-width: 800px; padding: 40px 0;">
    <!-- Messages -->
    <?php if(isset($_GET['success'])): ?>
        <div class="alert alert-success" style="margin-bottom: 20px;">
            ✓ <?php echo htmlspecialchars($_GET['success']); ?>
        </div>
    <?php endif; ?>
    
    <?php if(isset($_GET['error'])): ?>
        <div class="alert alert-error" style="margin-bottom: 20px;">
            ✕ <?php echo htmlspecialchars($_GET['error']); ?>
        </div>
    <?php endif; ?>

    <div class="card">
        <h2 style="color: var(--primary); margin-bottom: 30px;">👤 Mon Profil</h2>

        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

            <!-- Photo de profil -->
            <div style="margin-bottom: 30px; text-align: center;">
                <div style="width: 200px; height: 200px; margin: 0 auto 20px; border-radius: 50%; overflow: hidden; border: 4px solid var(--primary); box-shadow: 0 4px 15px rgba(0,0,0,0.2);">
                    <?php $profile_photo = $u['photo_profil'] ?? ''; ?>
                    <?php if(!empty($profile_photo) && file_exists(__DIR__ . '/uploads/profiles/' . $profile_photo)): ?>
                        <img src="<?php echo BASE_URL; ?>/uploads/profiles/<?php echo htmlspecialchars($profile_photo); ?>" 
                             alt="Photo" 
                             class="profile-photo"
                             data-lightbox 
                             loading="lazy" 
                             decoding="async"
                             style="width: 100%; height: 100%; object-fit: cover; cursor: pointer;">
                    <?php else: ?>
                        <img src="<?php echo BASE_URL; ?>/default-avatar.php" 
                             alt="Photo par défaut"
                             class="profile-photo"
                             data-lightbox 
                             style="width: 100%; height: 100%; object-fit: cover;">
                    <?php endif; ?>
                </div>
                <label for="photo" style="display: block; margin-bottom: 10px; font-weight: 600;">Changer de photo</label>
                <input type="file" id="photo" name="photo" accept="image/*" style="max-width: 300px; display: block; margin: 0 auto;">
                <small class="text-muted">JPEG, PNG ou GIF (max 2MB)</small>
            </div>

            <!-- Informations personnelles -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 25px;">
                <div class="form-group" style="margin-bottom: 0;">
                    <label>📛 Nom</label>
                    <input type="text" value="<?php echo htmlspecialchars($u['nom']); ?>" disabled style="background: var(--light);">
                </div>
                <div class="form-group" style="margin-bottom: 0;">
                    <label>📛 Prénom</label>
                    <input type="text" value="<?php echo htmlspecialchars($u['prenom']); ?>" disabled style="background: var(--light);">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 25px;">
                <div class="form-group" style="margin-bottom: 0;">
                    <label>📧 Email</label>
                    <input type="email" value="<?php echo htmlspecialchars($u['email']); ?>" disabled style="background: var(--light);">
                </div>
                <div class="form-group" style="margin-bottom: 0;">
                    <label>📱 Téléphone</label>
                    <input type="tel" name="phone" value="<?php echo htmlspecialchars_decode($u['telephone'] ?? ''); ?>" placeholder="Mettre à jour votre numéro">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 25px;">
                <div class="form-group" style="margin-bottom: 0;">
                    <label>🎯 Rôle</label>
                    <input type="text" value="<?php echo ucfirst($u['role']); ?>" disabled style="background: var(--light);">
                </div>
                <div class="form-group" style="margin-bottom: 0;">
                    <label>📅 Inscrit depuis</label>
                    <?php if(!empty($u['created_at'])): ?>
                        <input type="text" value="<?php echo date('d/m/Y', strtotime($u['created_at'])); ?>" disabled style="background: var(--light);">
                    <?php else: ?>
                        <input type="text" value="N/A" disabled style="background: var(--light);">
                    <?php endif; ?>
                </div>
            </div>

            <!-- Biographie -->
            <div class="form-group">
                <label>📝 Biographie</label>
                <textarea name="bio" placeholder="Parlez un peu de vous..." style="min-height: 120px;"><?php echo htmlspecialchars_decode($u['bio'] ?? ''); ?></textarea>
                <small class="text-muted">Maximum 500 caractères</small>
            </div>

            <!-- Boutons -->
            <div style="display: flex; gap: 10px; margin-top: 30px;">
                <button type="submit" class="btn btn-primary btn-block">💾 Sauvegarder les modifications</button>
                <a href="<?php echo BASE_URL; ?>/index.php" class="btn btn-outline btn-block">Retour</a>
            </div>
        </form>
    </div>

    <!-- Autre section: Sécurité (optionnel) -->
    <div class="card" style="margin-top: 30px;">
        <h3 style="color: var(--primary); margin-bottom: 20px;">🔒 Sécurité</h3>
        <div style="padding: 20px; background: var(--light); border-radius: 8px;">
            <p style="margin-bottom: 15px;">
                <strong>Dernier changement de mot de passe:</strong><br>
                <span class="text-muted">Jamais changé</span>
            </p>
            <a href="<?php echo BASE_URL; ?>/change_password.php" class="btn btn-outline">Changer le mot de passe</a>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>