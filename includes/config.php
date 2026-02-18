<?php
/**
 * CONFIGURATION DE LA BASE DE DONNÉES
 * Ce fichier est inclus dans toutes les pages qui ont besoin de la BDD.
 */

// 1. Informations de connexion (À modifier selon ton hébergeur plus tard)
$host = 'localhost';     // Souvent 'localhost' sur ton PC (XAMPP/WAMP)
$dbname = 'impact_emploi'; // Le nom exact de la base créée dans phpMyAdmin
$username = 'root';      // Par défaut 'root' sur Windows
$password = '';          // Par défaut vide sur Windows (ou 'root' sur Mac)

try {
    // 2. Création de la connexion PDO
    // On ajoute 'charset=utf8' pour que les accents (é, à, ç) s'affichent correctement
    $db = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8", 
        $username, 
        $password,
        [
            // Option Pro : On demande à PDO de générer des exceptions en cas d'erreur SQL
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            // Option Pro : On définit le mode de récupération par défaut en tableau associatif
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            // Option Sécurité : Désactive les simulations de requêtes préparées
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );

} catch (PDOException $e) {
    // En production : afficher un message générique
    // En développement : afficher le détail (pour debug)
    $isDev = (strpos($_SERVER['HTTP_HOST'] ?? '', 'localhost') !== false || 
              strpos($_SERVER['HTTP_HOST'] ?? '', '127.0.0.1') !== false ||
              strpos($_SERVER['HTTP_HOST'] ?? '', '192.168.') === 0);
    
    if ($isDev) {
        // Mode debug local - afficher les détails pour faciliter le debug
        die("❌ Erreur de connexion à la base de données : " . $e->getMessage());
    } else {
        // Mode production (en ligne) - message générique pour éviter fuites d'infos
        error_log("PDO Connection Error: " . $e->getMessage());
        http_response_code(500);
        die("❌ Erreur serveur. L'équipe technique a été notifiée. Veuillez réessayer dans quelques instants.");
    }
}

/**
 * LOGIQUE DE SÉCURITÉ GLOBALE
 * Ici, on peut définir des fonctions ou des constantes utiles à tout le projet
 */

// Exemple : Définir l'URL racine du projet si besoin
define('SITE_URL', 'http://localhost/impact_emploi/');

// Code pays par défaut pour les numéros locaux (utilisé si le numéro commence par '0')
// Valeur par défaut: +243 (République Démocratique du Congo). Change si nécessaire.
if (!defined('DEFAULT_COUNTRY_CODE')) {
    define('DEFAULT_COUNTRY_CODE', '242');
}

/**
 * Normalise un numéro de téléphone pour wa.me
 * - Supprime tout caractère non numérique
 * - Si le numéro commence par '00' on enlève les zéros initiaux
 * - Si commence par '0' on remplace par le code pays par défaut
 * - Si commence par '+' on enlève le '+'
 * Retourne une chaîne de chiffres prête à être utilisée dans https://wa.me/{digits}
 */
function format_phone_for_wa($phone) {
    if (empty($phone)) return '';
    // Garder uniquement les chiffres et le plus
    $p = preg_replace('/[^+0-9]/', '', $phone);

    // Si commence par +, on enlève le +
    if (strpos($p, '+') === 0) {
        return substr($p, 1);
    }

    // Si commence par 00 (format international avec 00)
    if (strpos($p, '00') === 0) {
        return substr($p, 2);
    }

    // Si commence par 0 -> remplacer par code pays par défaut
    if (strpos($p, '0') === 0) {
        // Retirer le zéro initial
        $rest = substr($p, 1);
        return DEFAULT_COUNTRY_CODE . $rest;
    }

    // Sinon on suppose que le numéro est déjà en international (sans +)
    return $p;
}

?>