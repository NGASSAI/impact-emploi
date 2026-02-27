/**
 * Image Optimizer Script - Impact Emploi
 * Optimise automatiquement les images à l'upload
 */

error_reporting(0);
ini_set('display_errors', 0);

header('Content-Type: application/json');

$upload_dir = __DIR__ . '/uploads/profiles/';
$jobs_dir = __DIR__ . '/assets/uploads/jobs/';

// Créer les répertoires si besoin
if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);
if (!is_dir($jobs_dir)) mkdir($jobs_dir, 0755, true);

/**
 * Optimise une image JPEG avec compression
 */
function optimizeJPEG($source, $dest, $quality = 70) {
    $img = imagecreatefromjpeg($source);
    if (!$img) return false;
    
    imagejpeg($img, $dest, $quality);
    imagedestroy($img);
    return true;
}

/**
 * Optimise une image PNG
 */
function optimizePNG($source, $dest, $quality = 70) {
    $img = imagecreatefrompng($source);
    if (!$img) return false;
    
    imagealphablending($img, false);
    imagesavealpha($img, true);
    imagepng($img, $dest, $quality);
    imagedestroy($img);
    return true;
}

// Traiter les images existantes
$images_optimized = 0;

// Optimiser les profiles
if (is_dir($upload_dir)) {
    $files = glob($upload_dir . '*.{jpg,jpeg,png}', GLOB_BRACE);
    foreach ($files as $file) {
        $size = filesize($file);
        // Si plus de 30KB, optimiser
        if ($size > 30000) {
            $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            $new_file = $file;
            
            if (in_array($ext, ['jpg', 'jpeg'])) {
                optimizeJPEG($file, $file, 70);
                $images_optimized++;
            } elseif ($ext === 'png') {
                optimizePNG($file, $file, 70);
                $images_optimized++;
            }
        }
    }
}

// Optimiser les images d'offres d'emploi
if (is_dir($jobs_dir)) {
    $files = glob($jobs_dir . '*.{jpg,jpeg,png}', GLOB_BRACE);
    foreach ($files as $file) {
        $size = filesize($file);
        if ($size > 30000) {
            $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            if (in_array($ext, ['jpg', 'jpeg'])) {
                optimizeJPEG($file, $file, 70);
                $images_optimized++;
            } elseif ($ext === 'png') {
                optimizePNG($file, $file, 70);
                $images_optimized++;
            }
        }
    }
}

echo json_encode([
    'success' => true,
    'images_optimized' => $images_optimized,
    'message' => "Images optimisées avec succès"
]);

