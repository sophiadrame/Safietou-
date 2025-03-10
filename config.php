<?php
// Configuration générale du site
define('SITE_TITLE', 'Location de Voitures');
define('SITE_URL', 'https://sophiaiam.alwaysdata.net');

// Configuration de la base de données
define('DB_HOST', 'mysql-sophiaiam.alwaysdata.net');
define('DB_NAME', 'sophiaiam_location_voiture');
define('DB_USER', 'sophiaiam');
define('DB_PASS', 'Sophia15072002'); // Mot de passe AlwaysData

// Configuration des chemins
define('ROOT_PATH', __DIR__);
define('UPLOAD_PATH', ROOT_PATH . '/images_voitures');

// Configuration des sessions
ini_set('session.cookie_lifetime', 3600); // Durée de vie du cookie en secondes (1 heure)
ini_set('session.gc_maxlifetime', 3600); // Durée de vie de la session

// Configuration du fuseau horaire
date_default_timezone_set('Europe/Paris');

// Configuration des erreurs (à mettre à true pour voir les erreurs pendant le développement)
define('DISPLAY_ERRORS', true);
if (DISPLAY_ERRORS) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
} else {
    error_reporting(0);
}

// Fonction pour gérer les erreurs
function handleError($errno, $errstr, $errfile, $errline) {
    $error_log = date('[Y-m-d H:i:s]') . " Erreur $errno : $errstr dans $errfile ligne $errline\n";
    error_log($error_log, 3, ROOT_PATH . '/logs/error.log');
    
    if (DISPLAY_ERRORS) {
        echo "Une erreur est survenue. Veuillez réessayer plus tard.";
    }
    return true;
}
set_error_handler('handleError');

// Fonction pour la connexion à la base de données
function getDBConnection() {
    try {
        $pdo = new PDO(
            "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8",
            DB_USER,
            DB_PASS,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
            ]
        );
        return $pdo;
    } catch(PDOException $e) {
        error_log(date('[Y-m-d H:i:s]') . " Erreur de connexion : " . $e->getMessage() . "\n", 3, ROOT_PATH . '/logs/db_error.log');
        die("Erreur de connexion à la base de données : " . $e->getMessage());
    }
}

// Création du dossier logs s'il n'existe pas
if (!file_exists(ROOT_PATH . '/logs')) {
    mkdir(ROOT_PATH . '/logs', 0755, true);
}

// Création du dossier images_voitures s'il n'existe pas
if (!file_exists(UPLOAD_PATH)) {
    mkdir(UPLOAD_PATH, 0755, true);
} 