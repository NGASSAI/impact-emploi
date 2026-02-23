<?php
require_once 'includes/header.php';
require_once 'includes/config.php';
require_once 'includes/csrf.php';

// --- SÉCURITÉ : Vérification de l'accès ---
// Si l'utilisateur n'est pas connecté OU n'est pas recruteur
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'recruteur') {
    // On le redirige vers l'accueil avec un message
    header('Location: index.php?error=acces_refuse');
    exit;
}

$error = null;
$success = null;

// --- TRAITEMENT DU FORMULAIRE ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // ===== VALIDATION CSRF =====
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        header('HTTP/1.1 403 Forbidden');
        $error = "⚠️ Erreur de sécurité : requête invalide. Veuillez recharger et réessayer.";
    } else {
        $titre         = htmlspecialchars(trim($_POST['titre']));
        $lieu          = htmlspecialchars(trim($_POST['lieu']));
        $type_contrat  = htmlspecialchars(trim($_POST['type_contrat']));
        $salaire       = htmlspecialchars(trim($_POST['salaire']));
        $description   = htmlspecialchars(trim($_POST['description']));
        $user_id       = $_SESSION['user_id']; // On récupère l'ID de celui qui est connecté

        if (empty($titre) || empty($description)) {
            $error = "Le titre et la description sont obligatoires.";
        } else {
            try {
                // ===== GESTION SÉCURISÉE DE L'IMAGE =====
                $imageName = null;
                if (!empty($_FILES['image']['name'])) {
                    // Configuration de sécurité
                    $maxSize = 5 * 1024 * 1024; // 5 MB max
                    $allowedMimes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
                    $allowedExt = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
                    
                    // Vérifier l'existence du fichier uploadé
                    if (!isset($_FILES['image']['tmp_name']) || !is_uploaded_file($_FILES['image']['tmp_name'])) {
                        throw new Exception('❌ Erreur : pas de fichier image valide.');
                    }
                    
                    // Vérifier la taille du fichier
                    if ($_FILES['image']['size'] > $maxSize) {
                        throw new Exception('❌ Erreur : le fichier dépasse 5 MB.');
                    }
                    
                    // Vérifier l'extension (première vérification)
                    $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
                    if (!in_array($ext, $allowedExt)) {
                        throw new Exception('❌ Erreur : format d\'image non accepté (.jpg, .png, .webp, .gif uniquement).');
                    }
                    
                    // Vérifier le type MIME réel (pas basé sur l'extension)
                    $mimeType = mime_content_type($_FILES['image']['tmp_name']);
                    if (!in_array($mimeType, $allowedMimes)) {
                        throw new Exception('❌ Erreur : type de fichier non accepté. Envoyez une image (JPEG, PNG, WebP, GIF).');
                    }
                    
                    // Créer le répertoire d'upload s'il n'existe pas
                    $uploadDir = __DIR__ . '/assets/uploads/jobs';
                    if (!is_dir($uploadDir)) {
                        if (!mkdir($uploadDir, 0755, true)) {
                            throw new Exception('❌ Erreur : impossible de créer le répertoire d\'upload.');
                        }
                    }
                    
                    // Générer un nom de fichier SÉCURISÉ (sans risque d'injection d'extension)
                    $imageName = time() . '_' . bin2hex(random_bytes(6)) . '.' . $ext;
                    $target = $uploadDir . '/' . $imageName;
                    
                    // Déplacer le fichier temporaire vers le répertoire final
                    if (!move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
                        throw new Exception('❌ Erreur lors de l\'upload du fichier. Vérifiez les permissions.');
                    }
                    
                    // Définir les permissions du fichier uploadé (lecture seule)
                    chmod($target, 0644);
                }

                // Insertion dans la table 'jobs' (avec image si fournie)
                $sql = "INSERT INTO jobs (titre, description, lieu, salaire, type_contrat, user_id, image) 
                        VALUES (:titre, :description, :lieu, :salaire, :type_contrat, :user_id, :image)";
                
                $stmt = $db->prepare($sql);
                $stmt->execute([
                    ':titre'        => $titre,
                    ':description'  => $description,
                    ':lieu'         => $lieu,
                    ':salaire'      => $salaire,
                    ':type_contrat' => $type_contrat,
                    ':user_id'      => $user_id,
                    ':image'        => $imageName
                ]);

                $success = "✅ L'offre d'emploi a été publiée avec succès !";
            } catch (Exception $e) {
                $error = $e->getMessage();
            } catch (PDOException $e) {
                $error = "❌ Erreur serveur lors de la publication. Veuillez réessayer.";
                error_log("PDO Error in poster_offre.php: " . $e->getMessage());
            }
        }
    }
}
?>

<div class="form-card" style="max-width: 700px;">
    <h2>Publier une offre d'emploi</h2>
    <p style="text-align:center; margin-bottom:20px;">Décrivez le poste pour attirer les meilleurs candidats.</p>

    <?php if($error): ?>
        <div class="alert alert-error"><?php echo $error; ?></div>
    <?php endif; ?>

    <?php if($success): ?>
        <div class="alert" style="background: #d1fae5; color: #065f46; border: 1px solid #065f46;">
            <?php echo $success; ?>
        </div>
    <?php endif; ?>

    <form action="poster_offre.php" method="POST" enctype="multipart/form-data">
        <?php csrfField(); ?>
        <div class="form-group">
            <label>Titre du poste</label>
            <input type="text" name="titre" required placeholder="Ex: Menuisier qualifié, Comptable, etc." value="<?php if(isset($_POST['titre'])) echo htmlspecialchars_decode($_POST['titre']); ?>">
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
            <div class="form-group">
                <label>Lieu (Ville/Quartier)</label>
                <input type="text" name="lieu" required placeholder="Ex: Poto-Poto, Centre-ville" value="<?php if(isset($_POST['lieu'])) echo htmlspecialchars_decode($_POST['lieu']); ?>">
            </div>
            <div class="form-group">
                <label>Type de contrat</label>
                <select name="type_contrat">
                    <option value="CDI">CDI</option>
                    <option value="CDD">CDD</option>
                    <option value="Stage">Stage</option>
                    <option value="Journalier">Journalier / Prestation</option>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label>Salaire (Optionnel)</label>
            <input type="text" name="salaire" placeholder="Ex: 150.000 FCFA / mois" value="<?php if(isset($_POST['salaire'])) echo htmlspecialchars_decode($_POST['salaire']); ?>">
        </div>

        <div class="form-group">
            <label>Description détaillée du poste</label>
            <textarea name="description" rows="6" required placeholder="Expliquez les missions et les compétences requises..."><?php if(isset($_POST['description'])) echo htmlspecialchars_decode($_POST['description']); ?></textarea>
        </div>

        <div class="form-group">
            <label>Image de l'offre (optionnelle)</label>
            <input type="file" name="image" accept="image/*">
        </div>

        <button type="submit">Publier l'annonce</button>
    </form>
</div>

<?php require_once 'includes/footer.php'; ?>