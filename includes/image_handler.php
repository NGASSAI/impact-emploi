<?php
/**
 * Gestionnaire d'images optimisé pour Impact Emploi
 * Redimensionne, optimise et sauvegarde les images
 */

/**
 * Redimensionne et optimise une image
 * @param string $source Chemin du fichier temporaire
 * @param string $dest Chemin de destination
 * @param int $maxWidth Largeur maximale (par défaut 800px)
 * @param int $quality Qualité JPEG (0-100, défaut 85)
 * @return bool Succès ou erreur
 */
function optimizeImage($source, $dest, $maxWidth = 800, $quality = 85) {
    try {
        // Vérifier si GD est disponible
        if (!extension_loaded('gd')) {
            throw new Exception('❌ Erreur : extension GD non disponible sur le serveur.');
        }

        // Obtenir les infos de l'image
        $imageInfo = getimagesize($source);
        if ($imageInfo === false) {
            throw new Exception('❌ Erreur : impossible de lire l\'image.');
        }

        $imageType = $imageInfo[2]; // Type MIME numérique
        $width = $imageInfo[0];
        $height = $imageInfo[1];

        // Charger l'image selon son type
        switch ($imageType) {
            case IMAGETYPE_JPEG:
                $image = imagecreatefromjpeg($source);
                $ext = 'jpg';
                break;
            case IMAGETYPE_PNG:
                $image = imagecreatefrompng($source);
                $ext = 'jpg'; // Convertir PNG en JPG pour réduire taille
                break;
            case IMAGETYPE_GIF:
                $image = imagecreatefromgif($source);
                $ext = 'jpg'; // Convertir GIF en JPG
                break;
            case IMAGETYPE_WEBP:
                if (function_exists('imagecreatefromwebp')) {
                    $image = imagecreatefromwebp($source);
                    $ext = 'jpg';
                } else {
                    throw new Exception('❌ Erreur : format WebP non supporté.');
                }
                break;
            default:
                throw new Exception('❌ Erreur : format d\'image non reconnu.');
        }

        if ($image === false) {
            throw new Exception('❌ Erreur : impossible de charger l\'image.');
        }

        // Redimensionner si nécessaire
        if ($width > $maxWidth) {
            // Calculer la nouvelle hauteur pour conserver les proportions
            $newHeight = intval(($maxWidth / $width) * $height);
            
            // Créer une nouvelle image redimensionnée
            $newImage = imagecreatetruecolor($maxWidth, $newHeight);
            
            // Préserver la transparence pour PNG
            if ($imageType === IMAGETYPE_PNG) {
                imagealphablending($newImage, false);
                imagesavealpha($newImage, true);
                $transparent = imagecolorallocatealpha($newImage, 255, 255, 255, 127);
                imagefilledrectangle($newImage, 0, 0, $maxWidth, $newHeight, $transparent);
            }
            
            // Redimensionner avec interpolation de qualité
            imagecopyresampled($newImage, $image, 0, 0, 0, 0, $maxWidth, $newHeight, $width, $height);
            imagedestroy($image);
            $image = $newImage;
            
            $width = $maxWidth;
            $height = $newHeight;
        }

        // Changer l'extension de destination si conversion
        $dest = preg_replace('/\.(png|gif|webp)$/i', '.jpg', $dest);

        // Sauvegarder l'image optimisée en JPG
        if (!imagejpeg($image, $dest, $quality)) {
            throw new Exception('❌ Erreur : impossible de sauvegarder l\'image optimisée.');
        }

        imagedestroy($image);

        // Définir les permissions
        chmod($dest, 0644);

        return true;

    } catch (Exception $e) {
        error_log("Image Optimization Error: " . $e->getMessage());
        throw $e;
    }
}

/**
 * Valide et traite un upload d'image
 * @param array $file Tableau $_FILES
 * @param string $uploadDir Répertoire de destination
 * @return string Nom du fichier sauvegardé
 */
function handleImageUpload($file, $uploadDir) {
    $maxSize = 10 * 1024 * 1024; // 10 MB max
    $allowedMimes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
    $allowedExt = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

    try {
        // Vérifier l'existence du fichier
        if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
            throw new Exception('❌ Erreur : pas de fichier image valide.');
        }

        // Vérifier la taille
        if ($file['size'] > $maxSize) {
            throw new Exception('❌ Erreur : le fichier dépasse 10 MB.');
        }

        // Vérifier l'extension
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $allowedExt)) {
            throw new Exception('❌ Erreur : format d\'image non accepté.');
        }

        // Vérifier le MIME type réel
        $mimeType = mime_content_type($file['tmp_name']);
        if (!in_array($mimeType, $allowedMimes)) {
            throw new Exception('❌ Erreur : type de fichier non accepté.');
        }

        // Créer le répertoire s'il n'existe pas
        if (!is_dir($uploadDir)) {
            if (!mkdir($uploadDir, 0755, true)) {
                throw new Exception('❌ Erreur : impossible de créer le répertoire.');
            }
        }

        // Générer un nom sécurisé de base (sans suffixe)
        $basename = time() . '_' . bin2hex(random_bytes(6));

        // Tailles souhaitées pour srcset (widths)
        $sizes = [400, 800, 1200];
        $savedMain = null;

        foreach ($sizes as $w) {
            $outName = $basename . '_' . $w . '.jpg';
            $outPath = $uploadDir . '/' . $outName;
            optimizeImage($file['tmp_name'], $outPath, $w, 85);
            // conserver la version 800 comme principale
            if ($w === 800) {
                $savedMain = $outPath;
            }
        }

        // Créer un fichier principal sans suffixe qui pointe vers la version 800 (compatibilité)
        $finalName = $basename . '.jpg';
        $finalPath = $uploadDir . '/' . $finalName;
        if ($savedMain && file_exists($savedMain)) {
            copy($savedMain, $finalPath);
            chmod($finalPath, 0644);
        } else {
            // fallback simple : tenter de sauvegarder directement
            optimizeImage($file['tmp_name'], $finalPath, 800, 85);
        }

        return $finalName;

    } catch (Exception $e) {
        error_log("Image Upload Error: " . $e->getMessage());
        throw $e;
    }
}
?>
