<?php
require_once 'config.php';

if($_SESSION['auth_role'] !== 'recruteur' && $_SESSION['auth_role'] !== 'admin') {
    header('Location: ' . BASE_URL . '/index.php?error=Accès non autorisé');
    exit();
}

// Initialiser les variables
$titre = '';
$description = '';
$lieu = '';
$salaire = '';
$type_contrat = 'CDI';
$competences = '';
$error = null;

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    
    $titre = clean($_POST['titre'] ?? '');
    $description = clean($_POST['description'] ?? '');
    $lieu = clean($_POST['lieu'] ?? '');
    $salaire_raw = $_POST['salaire'] ?? '';
    $salaire = 0;
    if($salaire_raw !== '' && $salaire_raw !== null) {
        $salaire = (float)$salaire_raw;
    }
    $type_contrat = clean($_POST['type_contrat'] ?? 'CDI');
    $competences = clean($_POST['competences'] ?? '');
    $user_id = $_SESSION['auth_id'];
    
    // Gestion de l'upload d'image
    $image_offre = null;
    $upload_error = null;
    
    if(isset($_FILES['image_offre']) && $_FILES['image_offre']['size'] > 0) {
        $file = $_FILES['image_offre'];
        $allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $max_size = 5 * 1024 * 1024; // 5MB
        
        if(!in_array($file['type'], $allowed)) {
            $upload_error = "Format d'image invalide. Acceptés: JPG, PNG, GIF, WebP";
        } elseif($file['size'] > $max_size) {
            $upload_error = "L'image dépasse 5MB";
        } elseif($file['error'] !== UPLOAD_ERR_OK) {
            $upload_error = "Erreur lors de l'upload";
        } else {
            // S'assurer que le répertoire existe
            $upload_dir = __DIR__ . '/uploads/jobs';
            if(!is_dir($upload_dir)) {
                if(!@mkdir($upload_dir, 0777, true)) {
                    $upload_error = "Impossible de créer le répertoire uploads/jobs";
                }
            }
            
            if(!$upload_error) {
                // Vérifier que le répertoire est accessible en écriture
                if(!is_writable($upload_dir)) {
                    @chmod($upload_dir, 0777);
                }
                
                if(is_writable($upload_dir)) {
                    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
                    $image_offre = 'job_' . $user_id . '_' . time() . '.' . $ext;
                    $upload_path = $upload_dir . '/' . $image_offre;
                    
                    if(!move_uploaded_file($file['tmp_name'], $upload_path)) {
                        $upload_error = "Erreur lors du stockage de l'image (move_uploaded_file échoué)";
                        $image_offre = null;
                    }
                } else {
                    $upload_error = "Le répertoire uploads/jobs n'est pas accessible en écriture";
                }
            }
        }
    }

    // Validations
    $errors = [];
    if(strlen($titre) < 5) $errors[] = "Le titre doit avoir au moins 5 caractères";
    if(strlen($lieu) < 2) $errors[] = "Le lieu est obligatoire";
    if($salaire < 0) $errors[] = "Le salaire ne peut pas être négatif";
    if($upload_error) $errors[] = $upload_error;

    if(count($errors) > 0) {
        $error = implode('<br>', $errors);
    } else {
        try {
            $sql = "INSERT INTO jobs (user_id, titre, description, lieu, salaire, type_contrat, competences, image_offre, date_publication) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$user_id, $titre, $description, $lieu, $salaire, $type_contrat, $competences, $image_offre]);
            
            log_activity($user_id, 'create_job', "Nouvelle offre créée: $titre");
            
            header('Location: ' . BASE_URL . '/recruteur_dashboard.php?success=Offre créée avec succès');
            exit();
        } catch(Exception $e) {
            $error = "Erreur lors de la création de l'offre: " . $e->getMessage();
        }
    }
}

include 'includes/header.php';
?>

<div class="container" style="max-width: 800px; padding: 40px 0;">
    <h1 style="font-size: 2rem; color: var(--primary); margin-bottom: 30px;">
        ➕ Créer une Offre d'Emploi
    </h1>

    <?php if(isset($error)): ?>
        <div class="alert alert-error" style="margin-bottom: 20px;">
            ✕ <?php echo $error; ?>
        </div>
    <?php endif; ?>

    <div class="card">
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

            <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 20px; margin-bottom: 20px;">
                <div class="form-group" style="margin-bottom: 0;">
                    <label for="titre">💼 Titre du Poste</label>
                    <input type="text" id="titre" name="titre" placeholder="Ex: Développeur PHP Senior" required style="padding: 12px;" value="<?php echo htmlspecialchars($_POST['titre'] ?? ''); ?>">
                </div>
                <div class="form-group" style="margin-bottom: 0;">
                    <label for="type_contrat">📋 Type de Contrat</label>
                    <select id="type_contrat" name="type_contrat" style="padding: 12px;">
                        <option value="CDI" <?php echo (($_POST['type_contrat'] ?? '') === 'CDI') ? 'selected' : ''; ?>>CDI</option>
                        <option value="CDD" <?php echo (($_POST['type_contrat'] ?? '') === 'CDD') ? 'selected' : ''; ?>>CDD</option>
                        <option value="Stage" <?php echo (($_POST['type_contrat'] ?? '') === 'Stage') ? 'selected' : ''; ?>>Stage</option>
                        <option value="Freelance" <?php echo (($_POST['type_contrat'] ?? '') === 'Freelance') ? 'selected' : ''; ?>>Freelance</option>
                    </select>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                <div class="form-group" style="margin-bottom: 0;">
                    <label for="lieu">📍 Lieu de Travail</label>
                    <input type="text" id="lieu" name="lieu" placeholder="Ex: Brazzaville" required style="padding: 12px;" value="<?php echo htmlspecialchars($_POST['lieu'] ?? ''); ?>">
                </div>
                <div class="form-group" style="margin-bottom: 0;">
                    <label for="salaire">💰 Salaire (FCFA)</label>
                    <input type="number" step="0.01" id="salaire" name="salaire" placeholder="Ex: 500000 (optionnel)" style="padding: 12px;" value="<?php echo htmlspecialchars($_POST['salaire'] ?? ''); ?>">
                </div>
            </div>

            <div class="form-group">
                <label for="description">📝 Description du Poste</label>
                <textarea id="description" name="description" placeholder="Décrivez le poste, les responsabilités, l'environnement de travail..." required style="min-height: 150px; padding: 12px; resize: none;" maxlength="300"><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
            </div>

            <div class="form-group">
                <label for="competences">🎯 Compétences Requises</label>
                <textarea id="competences" name="competences" placeholder="Listez les compétences requises (séparées par des virgules)" style="min-height: 100px; padding: 12px; resize: none;" maxlength="1000"><?php echo htmlspecialchars($_POST['competences'] ?? ''); ?></textarea>
            </div>

            <div class="form-group">
                <label for="image_offre">🖼️ Image de l'Offre (optionnel)</label>
                <p style="font-size: 0.85rem; color: var(--text-secondary); margin-bottom: 10px;">Max 5MB - Formats: JPG, PNG, GIF, WebP</p>
                <input type="file" id="image_offre" name="image_offre" accept="image/*" style="padding: 12px;">
                <div id="preview" style="margin-top: 10px;"></div>
            </div>

            <script>
            document.getElementById('image_offre').addEventListener('change', function(e) {
                const preview = document.getElementById('preview');
                const file = e.target.files[0];
                if(file) {
                    const reader = new FileReader();
                    reader.onload = function(event) {
                        preview.innerHTML = '<img src="' + event.target.result + '" style="max-width: 200px; border-radius: 8px; margin-top: 10px;">';
                    };
                    reader.readAsDataURL(file);
                } else {
                    preview.innerHTML = '';
                }
            });
            </script>

            <div style="display: flex; gap: 10px;">
                <button type="submit" class="btn btn-primary btn-block" style="padding: 14px;">
                    ✅ Créer l'Offre
                </button>
                <a href="<?php echo BASE_URL; ?>/recruteur_dashboard.php" class="btn btn-outline btn-block" style="padding: 14px;">
                    Annuler
                </a>
            </div>
        </form>
    </div>
</div>

<?php include 'includes/footer.php'; ?>