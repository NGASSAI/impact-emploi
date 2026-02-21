<?php
// ========== SÉCURITÉ & CONFIGURATION ==========
session_start();
date_default_timezone_set('Africa/Brazzaville');

// Headers de sécurité
if (!headers_sent()) {
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: SAMEORIGIN');
    header('X-XSS-Protection: 1; mode=block');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    // CACHE BUSTING AGRESSIF - Force la revalidation à chaque fois
    header('Cache-Control: no-cache, no-store, must-revalidate, max-age=0, s-maxage=0, proxy-revalidate, public');
    header('Pragma: no-cache');
    header('Expires: 0');
    header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
    header('ETag: "' . md5(microtime()) . '"');
    
    // CSRF Token
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
}

// Configuration Base de données
// Détection automatique: Production vs Local
$is_production = (
    strpos($_SERVER['HTTP_HOST'] ?? '', 'infinityfree.com') !== false ||
    strpos($_SERVER['HTTP_HOST'] ?? '', '.tc') !== false ||
    strpos($_SERVER['HTTP_HOST'] ?? '', '.gt.tc') !== false
);

if ($is_production) {
    // PRODUCTION (InfinityFREE)
    $host = 'localhost';
    $db   = 'impact_emploi';
    $user = 'root'; 
    $pass = '';
} else {
    // LOCAL (XAMPP)
    $host = 'localhost';
    $db   = 'impact_emploi';
    $user = 'root'; 
    $pass = '';
}

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false
    ]);
} catch (PDOException $e) {
    // En production, ne pas afficher les détails techniques
    if ($is_production) {
        error_log("Erreur BD: " . $e->getMessage() . " [Host: $host, User: $user]");
        // Rediriger vers une page d'erreur au lieu d'afficher le message
        if (php_sapi_name() !== 'cli') {
            http_response_code(503);
            die("
<!DOCTYPE html>
<html>
<head>
    <title>Service Indisponible</title>
    <style>
        body { font-family: Arial; text-align: center; padding: 50px; color: #333; }
        h1 { color: #e74c3c; }
    </style>
</head>
<body>
    <h1>⚠️ Service Temporairement Indisponible</h1>
    <p>Impossible de se connecter à la base de données.</p>
    <p>Notre équipe a été notifiée du problème.</p>
    <p><a href='/'>← Retour à l'accueil</a></p>
</body>
</html>
            ");
        }
    } else {
        // En développement, afficher l'erreur complète
        die("Erreur de connexion : " . $e->getMessage());
    }
}

// Constantes
define('ADMIN_EMAIL', 'nathanngassai885@gmail.com');
define('WHATSAPP_NUMBER', '+242066817726');
// Cache bust ultra-agressif par seconde pour forcer rechargement immédiat
define('CACHE_BUST', md5(date('YmdHis'))); // Cache bust à la seconde

// BASE URL dynamique : détecte si l'application est dans un sous-dossier
$__base = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
if ($__base === '/' || $__base === '.' || $__base === '') {
    $__base = '';
}
define('BASE_URL', rtrim($__base, '/'));

// Fonctions de sécurité
function clean($data) {
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}

// Sanitize: trim + strip_tags SANS htmlspecialchars (appliquer htmlspecialchars à l'affichage uniquement)
function sanitize($data) {
    return strip_tags(trim($data));
}

function validate_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

function validate_phone($phone) {
    return preg_match('/^[\+]?[(]?[0-9]{3}[)]?[-\s\.]?[0-9]{3}[-\s\.]?[0-9]{4,6}$/', str_replace(' ', '', $phone));
}

function verify_csrf() {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die('Erreur CSRF');
    }
}

function hash_password($password) {
    return password_hash($password, PASSWORD_ARGON2ID, ['memory_cost' => 65536, 'time_cost' => 4, 'threads' => 3]);
}

function check_password($password, $hash) {
    return password_verify($password, $hash);
}

function log_activity($user_id, $action, $description = null) {
    global $pdo;
    try {
        $pdo->prepare("INSERT INTO activity_logs (user_id, action, description, ip_address, user_agent, created_at) 
                      VALUES (?, ?, ?, ?, ?, NOW())")
           ->execute([$user_id, $action, $description, $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN', $_SERVER['HTTP_USER_AGENT'] ?? 'UNKNOWN']);
    } catch (Exception $e) {
        // Silent fail for logging
    }
}

function is_admin() {
    return isset($_SESSION['auth_role']) && $_SESSION['auth_role'] === 'admin';
}

function is_logged_in() {
    return isset($_SESSION['auth_id']);
}

// Track visitor stats
function track_visitor($pdo, $page) {
    try {
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN';
        $user_id = $_SESSION['auth_id'] ?? null;
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        $referer = $_SERVER['HTTP_REFERER'] ?? '';
        
        // Dédétecter les bots
        $is_bot = preg_match('/(bot|crawler|spider|googlebot|bing|crawl)/i', $user_agent) ? 1 : 0;
        
        $pdo->prepare("INSERT INTO visitors (ip_address, user_id, page_visited, user_agent, referer, is_bot) 
                      VALUES (?, ?, ?, ?, ?, ?)")
            ->execute([$ip, $user_id, $page, $user_agent, $referer, $is_bot]);
    } catch (Exception $e) {
        // Silent fail for tracking
    }
}
?>