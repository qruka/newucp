<?php
/**
 * Classe de gestion de la sécurité
 * Fonctionnalités de sécurité pour l'application
 */
class Security {
    /**
     * Limite de tentatives de connexion
     * @var int
     */
    private static $loginAttemptsLimit = 5;
    
    /**
     * Période de limitation (en secondes)
     * @var int
     */
    private static $loginAttemptsWindow = 3600; // 1 heure
    
    /**
     * Vérifier les tentatives de connexion pour une adresse IP
     * 
     * @param string $ip Adresse IP
     * @param mysqli $conn Connexion à la base de données
     * @return bool True si l'IP est bloquée, false sinon
     */
    public static function checkLoginAttempts($ip, $conn) {
        $time = time() - self::$loginAttemptsWindow;
        
        $stmt = $conn->prepare("
            SELECT COUNT(*) as attempts 
            FROM login_logs 
            WHERE ip_address = ? 
            AND success = 0 
            AND login_time > FROM_UNIXTIME(?)
        ");
        
        $stmt->bind_param("si", $ip, $time);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        return $row['attempts'] >= self::$loginAttemptsLimit;
    }
    
    /**
     * Générer un hash de mot de passe sécurisé
     * 
     * @param string $password Mot de passe en clair
     * @return string Hash du mot de passe
     */
    public static function hashPassword($password) {
        return password_hash($password, PASSWORD_DEFAULT);
    }
    
    /**
     * Vérifier un mot de passe
     * 
     * @param string $password Mot de passe en clair
     * @param string $hash Hash du mot de passe
     * @return bool True si le mot de passe correspond, false sinon
     */
    public static function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }
    
    /**
     * Générer un token CSRF
     * 
     * @return string Token CSRF
     */
    public static function generateCsrfToken() {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        
        return $_SESSION['csrf_token'];
    }
    
    /**
     * Vérifier un token CSRF
     * 
     * @param string $token Token CSRF à vérifier
     * @return bool True si le token est valide, false sinon
     */
    public static function verifyCsrfToken($token) {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }
    
    /**
     * Générer un token d'authentification
     * 
     * @param int $user_id ID de l'utilisateur
     * @param string $selector Sélecteur (optionnel)
     * @return array Tableau contenant le sélecteur et le jeton validateur
     */
    public static function generateAuthToken($user_id, $selector = null) {
        // Générer un sélecteur aléatoire s'il n'est pas fourni
        $selector = $selector ?? bin2hex(random_bytes(8));
        
        // Générer un jeton aléatoire
        $validator = bin2hex(random_bytes(32));
        
        // Hacher le jeton pour le stockage
        $token_hash = hash('sha256', $validator);
        
        return [
            'user_id' => $user_id,
            'selector' => $selector,
            'validator' => $validator,
            'token_hash' => $token_hash,
            'expires' => date('Y-m-d H:i:s', time() + 86400 * 30) // 30 jours
        ];
    }
    
    /**
     * Nettoyer les données entrantes
     * 
     * @param mixed $data Données à nettoyer
     * @return mixed Données nettoyées
     */
    public static function sanitizeInput($data) {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $data[$key] = self::sanitizeInput($value);
            }
        } else {
            $data = trim($data);
            $data = stripslashes($data);
            $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
        }
        
        return $data;
    }
    
    /**
     * Générer un nom de fichier sécurisé
     * 
     * @param string $filename Nom du fichier original
     * @return string Nom de fichier sécurisé
     */
    public static function secureFilename($filename) {
        // Supprimer les caractères spéciaux
        $filename = preg_replace('/[^\w\-\.]/', '', $filename);
        
        // Ajouter un préfixe aléatoire pour éviter les collisions
        $prefix = substr(md5(uniqid(mt_rand(), true)), 0, 8);
        
        return $prefix . '_' . $filename;
    }
    
    /**
     * Journal d'activité de sécurité
     * 
     * @param string $action Action effectuée
     * @param string $description Description de l'action
     * @param int $user_id ID de l'utilisateur (optionnel)
     * @param string $ip Adresse IP (optionnel)
     * @return void
     */
    public static function logSecurityActivity($action, $description, $user_id = null, $ip = null) {
        $logFile = APP_ROOT . '/logs/security.log';
        $timestamp = date('Y-m-d H:i:s');
        $ip = $ip ?? $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
        $user_id = $user_id ?? ($_SESSION['user_id'] ?? 'Non authentifié');
        
        $logMessage = "[$timestamp] [$ip] [User: $user_id] [$action] $description" . PHP_EOL;
        
        // Créer le répertoire des logs s'il n'existe pas
        if (!is_dir(dirname($logFile))) {
            mkdir(dirname($logFile), 0755, true);
        }
        
        // Écrire dans le fichier de log
        error_log($logMessage, 3, $logFile);
    }
    
    /**
     * Détection des attaques XSS
     * 
     * @param array $data Données à vérifier
     * @return bool True si une attaque XSS est détectée, false sinon
     */
    public static function detectXss($data) {
        $xssPatterns = [
            '/<script\b[^>]*>(.*?)<\/script>/is',
            '/javascript\s*:/i',
            '/onclick\s*=/i',
            '/onerror\s*=/i',
            '/onload\s*=/i',
            '/onmouseover\s*=/i',
            '/\bdata\s*:.*base64/i'
        ];
        
        foreach ($data as $key => $value) {
            if (is_string($value)) {
                foreach ($xssPatterns as $pattern) {
                    if (preg_match($pattern, $value)) {
                        self::logSecurityActivity('XSS_ATTEMPT', "Tentative d'attaque XSS détectée dans le champ '$key'");
                        return true;
                    }
                }
            }
        }
        
        return false;
    }
    
    /**
     * Détection des attaques par injection SQL
     * 
     * @param array $data Données à vérifier
     * @return bool True si une attaque par injection SQL est détectée, false sinon
     */
    public static function detectSqlInjection($data) {
        $sqlPatterns = [
            '/(\s|^)(SELECT|INSERT|UPDATE|DELETE|DROP|ALTER|UNION|INTO|OUTFILE)/i',
            '/(\s|^)(FROM|WHERE|GROUP BY|HAVING|ORDER BY|LIMIT)/i',
            '/--/',
            '/;.*/s',
            '/\/\*.*\*\//Us'
        ];
        
        foreach ($data as $key => $value) {
            if (is_string($value)) {
                foreach ($sqlPatterns as $pattern) {
                    if (preg_match($pattern, $value)) {
                        self::logSecurityActivity('SQL_INJECTION_ATTEMPT', "Tentative d'injection SQL détectée dans le champ '$key'");
                        return true;
                    }
                }
            }
        }
        
        return false;
    }
}