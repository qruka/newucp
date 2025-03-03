<?php
/**
 * Fichier d'initialisation de l'application
 * Ce fichier est chargé au début de chaque requête
 */

// Définir les constantes
define('APP_ROOT', dirname(__DIR__, 2));
define('APP_URL', 'http://localhost/monUCP2025');
define('APP_ENV', 'development'); // 'development' ou 'production'

// Gestion des erreurs
ini_set('display_errors', APP_ENV === 'development' ? 1 : 0);
error_reporting(APP_ENV === 'development' ? E_ALL : 0);

// Charger la configuration principale
$config = require_once APP_ROOT . '/app/config/config.php';

// Charger la configuration de la base de données
$db_config = require_once APP_ROOT . '/app/config/database.php';

// Créer la connexion à la base de données (pour compatibilité avec l'ancien code)
// À terme, cette partie sera gérée par la classe Database
$conn = new mysqli(
    $db_config['host'],
    $db_config['username'],
    $db_config['password'],
    $db_config['database']
);

// Vérifier la connexion
if ($conn->connect_error) {
    if (APP_ENV === 'development') {
        die("Erreur de connexion à la base de données: " . $conn->connect_error);
    } else {
        die("Une erreur est survenue. Veuillez réessayer plus tard.");
    }
}

// Définir l'encodage des caractères
$conn->set_charset($db_config['charset']);

// Charger les classes et fonctions utilitaires
require_once APP_ROOT . '/app/helpers/ErrorHandler.php';
require_once APP_ROOT . '/app/helpers/Validator.php';
require_once APP_ROOT . '/app/helpers/Security.php';

// Charger les anciens fichiers utilitaires (pour la transition)
require_once APP_ROOT . '/includes/user_utils.php';
require_once APP_ROOT . '/includes/admin_utils.php';
require_once APP_ROOT . '/includes/character_utils.php';
require_once APP_ROOT . '/includes/ip_utils.php';

// Charger le contrôleur de base
require_once APP_ROOT . '/app/controllers/BaseController.php';

// Initialiser la gestion des erreurs
$errorHandler = new ErrorHandler();
set_error_handler([$errorHandler, 'handleError']);
set_exception_handler([$errorHandler, 'handleException']);

// Vérifier/générer le token CSRF
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Mettre à jour l'activité de l'utilisateur
if (isset($_SESSION['user_id'])) {
    update_user_activity($_SESSION['user_id'], $conn);
}