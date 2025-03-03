<?php
/**
 * Contrôleur de base
 * Tous les autres contrôleurs héritent de cette classe
 */
class BaseController {
    protected $conn;
    protected $user;
    protected $is_admin = false;
    
    /**
     * Constructeur
     */
    public function __construct() {
        global $conn;
        $this->conn = $conn;
        
        // Charger les informations de l'utilisateur si connecté
        if (isset($_SESSION['user_id'])) {
            $user_query = "SELECT * FROM users WHERE id = ?";
            $stmt = $this->conn->prepare($user_query);
            $stmt->bind_param("i", $_SESSION['user_id']);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows === 1) {
                $this->user = $result->fetch_assoc();
                $this->is_admin = (bool)$this->user['is_admin'];
            }
        }
    }
    
    /**
     * Méthode pour charger une vue
     * 
     * @param string $view Chemin de la vue à charger
     * @param array $data Données à passer à la vue
     * @param string $layout Layout à utiliser (main par défaut)
     * @return void
     */
    protected function view($view, $data = [], $layout = 'main') {
        // Ajouter les données de l'utilisateur
        $data['user'] = $this->user;
        $data['is_admin'] = $this->is_admin;
        
        // Ajouter le token CSRF
        $data['csrf_token'] = $_SESSION['csrf_token'] ?? '';
        
        // Extraire les données pour les rendre accessibles dans la vue
        extract($data);
        
        // Démarrer la mise en tampon de sortie
        ob_start();
        
        // Charger la vue
        $view_path = APP_ROOT . "/app/views/{$view}.php";
        if (file_exists($view_path)) {
            require $view_path;
        } else {
            // Vue non trouvée
            echo "Erreur: La vue '{$view}' n'existe pas.";
        }
        
        // Récupérer le contenu de la vue
        $content = ob_get_clean();
        
        // Charger le layout
        $layout_path = APP_ROOT . "/app/views/layouts/{$layout}.php";
        if (file_exists($layout_path)) {
            require $layout_path;
        } else {
            // Layout non trouvé, afficher directement le contenu
            echo $content;
        }
    }
    
    /**
     * Redirection
     * 
     * @param string $route Route vers laquelle rediriger
     * @param string $action Action à exécuter (optionnel)
     * @return void
     */
    protected function redirect($route, $action = null) {
        $url = "index.php?route={$route}";
        
        if ($action !== null) {
            $url .= "&action={$action}";
        }
        
        header("Location: {$url}");
        exit;
    }
    
    /**
     * Ajouter un message flash
     * 
     * @param string $message Message à afficher
     * @param string $type Type de message (success, error, warning, info)
     * @return void
     */
    protected function setFlash($message, $type = 'success') {
        $_SESSION['flash'] = [
            'message' => $message,
            'type' => $type
        ];
    }
    
    /**
     * Vérifier si un formulaire a été soumis via POST et avec un token CSRF valide
     * 
     * @return bool
     */
    protected function isValidFormSubmission() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return false;
        }
        
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            $this->setFlash('Token de sécurité invalide. Veuillez réessayer.', 'error');
            return false;
        }
        
        return true;
    }
    
    /**
     * Vérifier si l'utilisateur est authentifié
     * 
     * @return bool
     */
    protected function isAuthenticated() {
        return isset($_SESSION['user_id']) && $this->user !== null;
    }
    
    /**
     * Vérifier si l'utilisateur est administrateur
     * 
     * @return bool
     */
    protected function isAdmin() {
        return $this->isAuthenticated() && $this->is_admin;
    }
}