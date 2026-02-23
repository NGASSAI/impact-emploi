<?php
require_once 'config.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : null;

if(!$id) {
    header('Location: ' . BASE_URL . '/recruteur_dashboard.php');
    exit();
}

// Récupérer l'offre
$job_stmt = $pdo->prepare("SELECT * FROM jobs WHERE id = ?");
$job_stmt->execute([$id]);
$job = $job_stmt->fetch();

if(!$job) {
    header('Location: ' . BASE_URL . '/recruteur_dashboard.php?error=Offre non trouvée');
    exit();
}

// Vérifier les permissions
if($_SESSION['auth_role'] !== 'admin' && $_SESSION['auth_id'] !== $job['user_id']) {
    header('Location: ' . BASE_URL . '/recruteur_dashboard.php?error=Accès non autorisé');
    exit();
}

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    
    $errors = [];
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

    // Handle image upload/deletion
    $new_image = $job['image_offre']; // Keep existing image by default
    
    // Check if user wants to delete current image
    if(isset($_POST['delete_image']) && $_POST['delete_image'] == 'yes' && !empty($job['image_offre'])) {
        $image_path = __DIR__ . '/uploads/jobs/' . $job['image_offre'];
        if(file_exists($image_path)) {
            unlink($image_path);
        }
        $new_image = null;
    }
    
    // Handle new image upload
    if(isset($_FILES['image_offre']) && $_FILES['image_offre']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['image_offre'];
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $max_size = 5 * 1024 * 1024; // 5MB
        
        if(!in_array($file['type'], $allowed_types)) {
            $errors[] = "Format d'image non autorisé (JPEG, PNG, GIF, WebP uniquement)";
        } elseif($file['size'] > $max_size) {
            $errors[] = "L'image ne doit pas dépasser 5 MB";
        } else {
            // S'assurer que le répertoire existe
            $upload_dir = __DIR__ . '/uploads/jobs';
            if(!is_dir($upload_dir)) {
                if(!@mkdir($upload_dir, 0777, true)) {
                    $errors[] = "Impossible de créer le répertoire uploads/jobs";
                }
            }
            
            if(!$errors) {
                // Vérifier que le répertoire est accessible en écriture
                if(!is_writable($upload_dir)) {
                    @chmod($upload_dir, 0777);
                }
                
                if(is_writable($upload_dir)) {
                    // Delete old image if it exists
                    if(!empty($job['image_offre'])) {
                        $old_path = __DIR__ . '/uploads/jobs/' . $job['image_offre'];
                        if(file_exists($old_path)) {
                            unlink($old_path);
                        }
                    }
                    
                    // Generate filename
                    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
                    $filename = 'job_' . $id . '_' . time() . '.' . $ext;
                    $filepath = __DIR__ . '/uploads/jobs/' . $filename;
                    
                    if(move_uploaded_file($file['tmp_name'], $filepath)) {
                        $new_image = $filename;
                    } else {
                        $errors[] = "Erreur lors du téléchargement de l'image (move_uploaded_file échoué)";
                    }
                } else {
                    $errors[] = "Le répertoire uploads/jobs n'est pas accessible en écriture";
                }
            }
        }
    }

    // Validations
    if(strlen($titre) < 5) $errors[] = "Le titre doit avoir au moins 5 caractères";
    if(strlen($lieu) < 2) $errors[] = "Le lieu est obligatoire";
    if($salaire < 0) $errors[] = "Le salaire ne peut pas être négatif";

    if(count($errors) > 0) {
        $error = implode('<br>', $errors);
    } else {
        try {
            $sql = "UPDATE jobs SET titre = ?, description = ?, lieu = ?, salaire = ?, type_contrat = ?, competences = ?, image_offre = ?, updated_at = NOW() WHERE id = ?";
            $pdo->prepare($sql)->execute([$titre, $description, $lieu, $salaire, $type_contrat, $competences, $new_image, $id]);
            
            log_activity($_SESSION['auth_id'], 'edit_job', "Offre $id modifiée");
            
            header('Location: ' . BASE_URL . '/recruteur_dashboard.php?success=Offre mise à jour');
            exit();
        } catch(Exception $e) {
            $error = "Erreur lors de la mise à jour : " . $e->getMessage();
        }
    }
}

include 'includes/header.php';
?>

<div class="container" style="max-width: 800px; padding: 40px 0;">
    <h1 style="font-size: 2rem; color: var(--primary); margin-bottom: 30px;">
        ✏️ Modifier l'Offre d'Emploi
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
                    <input type="text" id="titre" name="titre" value="<?php echo htmlspecialchars_decode($job['titre']); ?>" required style="padding: 12px;">
                </div>
                <div class="form-group" style="margin-bottom: 0;">
                    <label for="type_contrat">📋 Type de Contrat</label>
                    <select id="type_contrat" name="type_contrat" style="padding: 12px;">
                        <option value="CDI" <?php echo $job['type_contrat'] === 'CDI' ? 'selected' : ''; ?>>CDI</option>
                        <option value="CDD" <?php echo $job['type_contrat'] === 'CDD' ? 'selected' : ''; ?>>CDD</option>
                        <option value="Stage" <?php echo $job['type_contrat'] === 'Stage' ? 'selected' : ''; ?>>Stage</option>
                        <option value="Freelance" <?php echo $job['type_contrat'] === 'Freelance' ? 'selected' : ''; ?>>Freelance</option>
                    </select>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                <div class="form-group" style="margin-bottom: 0;">
                    <label for="lieu">📍 Lieu de Travail</label>
                    <input type="text" id="lieu" name="lieu" value="<?php echo htmlspecialchars_decode($job['lieu']); ?>" required style="padding: 12px;">
                </div>
                <div class="form-group" style="margin-bottom: 0;">
                    <label for="salaire">💰 Salaire (FCFA)</label>
                    <input type="number" id="salaire" name="salaire" value="<?php echo $job['salaire']; ?>" required style="padding: 12px;">
                </div>
            </div>

            <div class="form-group">
                <label for="description">📝 Description du Poste</label>
                <textarea id="description" name="description" required style="min-height: 150px; padding: 12px;"><?php echo htmlspecialchars_decode($job['description']); ?></textarea>
            </div>

            <div class="form-group">
                <label for="competences">🎯 Compétences Requises</label>
                <textarea id="competences" name="competences" style="min-height: 100px; padding: 12px;"><?php echo htmlspecialchars_decode($job['competences'] ?? ''); ?></textarea>
            </div>

            <!-- Image Management Section -->
            <div class="form-group">
                <label>🖼️ Image de l'Offre</label>
                
                <!-- Current Image Display -->
                <?php if(!empty($job['image_offre'])): ?>
                    <div style="margin-bottom: 20px; padding: 15px; background: #f5f5f5; border-radius: 8px;">
                        <p style="margin: 0 0 10px 0; color: #666;">📷 Image actuelle :</p>
                        <img id="current_image" src="<?php echo BASE_URL; ?>/uploads/jobs/<?php echo htmlspecialchars($job['image_offre']); ?>" 
                             alt="Image actuelle" loading="lazy" style="max-width: 300px; height: auto; border-radius: 6px; display: block; margin-bottom: 10px;">
                        <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; font-size: 14px; color: #e74c3c;">
                            <input type="checkbox" name="delete_image" value="yes"> Supprimer cette image
                        </label>
                    </div>
                <?php endif; ?>
                
                <!-- New Image Upload -->
                <div style="padding: 15px; border: 2px dashed #3498db; border-radius: 8px; background: #f9f9f9;">
                    <input type="file" id="image_offre" name="image_offre" accept="image/*" style="display: block; margin-bottom: 10px;">
                    <small style="color: #666;">Formats autorisés : JPG, PNG, GIF, WebP | Max 5 MB</small>
                    
                    <!-- Preview for New Image -->
                    <div id="preview_container" style="margin-top: 15px; display: none;">
                        <p style="margin: 0 0 10px 0; color: #666;">👁️ Aperçu :</p>
                        <img id="preview_image" alt="Aperçu" style="max-width: 300px; height: auto; border-radius: 6px; display: block;">
                    </div>
                </div>
            </div>

            <script>
            document.getElementById('image_offre').addEventListener('change', function(e) {
                const file = e.target.files[0];
                if(file) {
                    const reader = new FileReader();
                    reader.onload = function(event) {
                        document.getElementById('preview_image').src = event.target.result;
                        document.getElementById('preview_container').style.display = 'block';
                    };
                    reader.readAsDataURL(file);
                } else {
                    document.getElementById('preview_container').style.display = 'none';
                }
            });
            </script>

            <div style="display: flex; gap: 10px;">
                <button type="submit" class="btn btn-primary btn-block" style="padding: 14px;">
                    💾 Enregistrer les Modifications
                </button>
                <a href="<?php echo BASE_URL; ?>/recruteur_dashboard.php" class="btn btn-outline btn-block" style="padding: 14px;">
                    Annuler
                </a>
            </div>
        </form>
    </div>
</div>

<?php include 'includes/footer.php'; ?>