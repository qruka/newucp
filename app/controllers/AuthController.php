<?php
/**
 * Contrôleur d'authentification
 * Gère les connexions, inscriptions et déconnexions
 */
class AuthController extends BaseController {
    /**
     * L'authentification n'est pas requise pour ce contrôleur
     */
    public function __construct() {
        global $conn;
        $this->conn = $conn;
    }
    
    /**
     * Page de connexion
     */
    public function index() {
        // Rediriger vers le tableau de bord si déjà connecté
        if (isset($_SESSION['user_id'])) {
            $this->redirect('dashboard');
            return;
        }
        
        $this->login();
    }
    
    /**
     * Traitement de la connexion
     */
    public function login() {
        // Rediriger vers le tableau de bord si déjà connecté
        if (isset($_SESSION['user_id'])) {
            $this->redirect('dashboard');
            return;
        }
        
        $email = '';
        $errors = [];
        
        // Vérifier si le formulaire a été soumis
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Récupérer les données du formulaire
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            $remember = isset($_POST['remember']) && $_POST['remember'] === 'on';
            
            // Valider les données
            $validator = new Validator($_POST);
            $validator->required('email', 'L\'email est requis')
                     ->email('email', 'Format d\'email invalide')
                     ->required('password', 'Le mot de passe est requis');
            
            if (!$validator->hasErrors()) {
                // Vérifier les tentatives de connexion
                $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
                if (Security::checkLoginAttempts($ip, $this->conn)) {
                    $errors[] = "Trop de tentatives de connexion. Veuillez réessayer plus tard.";
                    Security::logSecurityActivity('LOGIN_BLOCKED', "Trop de tentatives de connexion depuis l'IP: $ip");
                } else {
                    // Rechercher l'utilisateur
                    $stmt = $this->conn->prepare("SELECT id, name, password, is_banned FROM users WHERE email = ?");
                    $stmt->bind_param("s", $email);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    
                    if ($result->num_rows === 1) {
                        $user = $result->fetch_assoc();
                        
                        // Vérifier si l'utilisateur est banni
                        if ($user['is_banned']) {
                            // Vérifier les détails du bannissement
                            $ban_stmt = $this->conn->prepare("
                                SELECT b.reason, b.ban_expiry 
                                FROM user_bans b 
                                WHERE b.user_id = ? AND b.is_active = 1 
                                AND (b.ban_expiry IS NULL OR b.ban_expiry > NOW())
                            ");
                            $ban_stmt->bind_param("i", $user['id']);
                            $ban_stmt->execute();
                            $ban_result = $ban_stmt->get_result();
                            
                            if ($ban_result->num_rows > 0) {
                                $ban_info = $ban_result->fetch_assoc();
                                $ban_message = "Votre compte est suspendu";
                                
                                if ($ban_info['ban_expiry']) {
                                    $ban_message .= " jusqu'au " . date('d/m/Y H:i', strtotime($ban_info['ban_expiry']));
                                } else {
                                    $ban_message .= " indéfiniment";
                                }
                                
                                if ($ban_info['reason']) {
                                    $ban_message .= ". Raison: " . htmlspecialchars($ban_info['reason']);
                                }
                                
                                // Enregistrer la tentative de connexion (échouée à cause du bannissement)
                                log_login_attempt($user['id'], false, $this->conn);
                                
                                $errors[] = $ban_message;
                            } else {
                                $errors[] = "Votre compte est suspendu.";
                                log_login_attempt($user['id'], false, $this->conn);
                            }
                        } else {
                            // Vérifier le mot de passe
                            if (password_verify($password, $user['password'])) {
                                // Connexion réussie
                                $_SESSION['user_id'] = $user['id'];
                                $_SESSION['user_name'] = $user['name'];
                                
                                // Enregistrer la tentative de connexion (réussie)
                                log_login_attempt($user['id'], true, $this->conn);
                                
                                // Gérer "Se souvenir de moi"
                                if ($remember) {
                                    $this->setRememberMeCookie($user['id']);
                                }
                                
                                // Vérifier le changement d'IP
                                if (has_ip_changed($user['id'], $this->conn)) {
                                    $_SESSION['ip_change_notice'] = true;
                                    Security::logSecurityActivity('IP_CHANGE', "Changement d'adresse IP détecté pour l'utilisateur #{$user['id']}", $user['id']);
                                }
                                
                                // Rediriger vers le tableau de bord
                                $this->setFlash("Connexion réussie. Bienvenue {$user['name']} !", 'success');
                                $this->redirect('dashboard');
                                return;
                            } else {
                                // Mot de passe incorrect
                                log_login_attempt($user['id'], false, $this->conn);
                                $errors[] = "Email ou mot de passe incorrect";
                            }
                        }
                    } else {
                        // Utilisateur non trouvé
                        $errors[] = "Email ou mot de passe incorrect";
                    }
                }
            } else {
                $errors = array_values($validator->getErrors());
            }
        }
        
        // Afficher la page de connexion
        $this->view('auth/login', [
            'title' => 'Connexion',
            'email' => $email,
            'errors' => $errors
        ]);
    }
    
    /**
     * Page d'inscription
     */
    public function register() {
        // Rediriger vers le tableau de bord si déjà connecté
        if (isset($_SESSION['user_id'])) {
            $this->redirect('dashboard');
            return;
        }
        
        $name = '';
        $email = '';
        $errors = [];
        $success = '';
        
        // Vérifier si le formulaire a été soumis
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Récupérer les données du formulaire
            $name = $_POST['name'] ?? '';
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            $confirm_password = $_POST['confirm_password'] ?? '';
            
            // Valider les données
            $validator = new Validator($_POST);
            $validator->required('name', 'Le nom est requis')
                     ->pattern('name', '/^[a-zA-Z-\' ]*$/', 'Seuls les lettres et les espaces sont autorisés')
                     ->required('email', 'L\'email est requis')
                     ->email('email', 'Format d\'email invalide')
                     ->required('password', 'Le mot de passe est requis')
                     ->length('password', 6, null, 'Le mot de passe doit contenir au moins 6 caractères')
                     ->required('confirm_password', 'La confirmation du mot de passe est requise')
                     ->matches('confirm_password', 'password', 'Les mots de passe ne correspondent pas');
            
            // Vérifier si l'email existe déjà
            if (!$validator->hasErrors() && !empty($email)) {
                $validator->unique('email', 'users', 'email', null, 'Cet email est déjà utilisé');
            }
            
            if (!$validator->hasErrors()) {
                // Hacher le mot de passe
                $password_hash = Security::hashPassword($password);
                
                // Insérer l'utilisateur
                $stmt = $this->conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
                $stmt->bind_param("sss", $name, $email, $password_hash);
                
                if ($stmt->execute()) {
                    // Enregistrement réussi
                    $success = "Inscription réussie ! Vous pouvez maintenant vous connecter.";
                    Security::logSecurityActivity('REGISTER', "Nouvel utilisateur enregistré: $email");
                    
                    // Réinitialiser les champs
                    $name = $email = '';
                } else {
                    $errors[] = "Une erreur s'est produite lors de l'inscription. Veuillez réessayer.";
                }
            } else {
                $errors = array_values($validator->getErrors());
            }
        }
        
        // Afficher la page d'inscription
        $this->view('auth/register', [
            'title' => 'Inscription',
            'name' => $name,
            'email' => $email,
            'errors' => $errors,
            'success' => $success
        ]);
    }
    
    /**
     * Déconnexion
     */
    public function logout() {
        // Journaliser la déconnexion
        if (isset($_SESSION['user_id'])) {
            Security::logSecurityActivity('LOGOUT', "Déconnexion de l'utilisateur #{$_SESSION['user_id']}", $_SESSION['user_id']);
        }
        
        // Supprimer le cookie "Se souvenir de moi"
        if (isset($_COOKIE['remember_user'])) {
            // Supprimer le token de la base de données
            $selector = $_COOKIE['remember_selector'] ?? '';
            
            if (!empty($selector)) {
                $stmt = $this->conn->prepare("DELETE FROM user_tokens WHERE selector = ?");
                $stmt->bind_param("s", $selector);
                $stmt->execute();
            }
            
            // Supprimer les cookies
            setcookie('remember_user', '', time() - 3600, '/');
            setcookie('remember_selector', '', time() - 3600, '/');
            setcookie('remember_validator', '', time() - 3600, '/');
        }
        
        // Détruire la session
        session_unset();
        session_destroy();
        
        // Rediriger vers la page de connexion
        $this->redirect('login');
    }
    
    /**
     * Réinitialisation du mot de passe - Demande
     */
    public function forgotPassword() {
        // Rediriger vers le tableau de bord si déjà connecté
        if (isset($_SESSION['user_id'])) {
            $this->redirect('dashboard');
            return;
        }
        
        $email = '';
        $errors = [];
        $success = '';
        
        // Vérifier si le formulaire a été soumis
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Récupérer l'email
            $email = $_POST['email'] ?? '';
            
            // Valider l'email
            $validator = new Validator(['email' => $email]);
            $validator->required('email', 'L\'email est requis')
                     ->email('email', 'Format d\'email invalide');
            
            if (!$validator->hasErrors()) {
                // Vérifier si l'email existe
                $stmt = $this->conn->prepare("SELECT id, name FROM users WHERE email = ?");
                $stmt->bind_param("s", $email);
                $stmt->execute();
                $result = $stmt->get_result();
                
                if ($result->num_rows === 1) {
                    $user = $result->fetch_assoc();
                    
                    // Générer un token de réinitialisation
                    $token = bin2hex(random_bytes(32));
                    $token_hash = hash('sha256', $token);
                    $expires = date('Y-m-d H:i:s', time() + 3600); // 1 heure
                    
                    // Enregistrer le token
                    $stmt = $this->conn->prepare("INSERT INTO password_resets (user_id, token, expires_at) VALUES (?, ?, ?)");
                    $stmt->bind_param("iss", $user['id'], $token_hash, $expires);
                    
                    if ($stmt->execute()) {
                        // Envoyer l'email (simulé pour l'instant)
                        $reset_url = APP_URL . "/index.php?route=auth&action=resetPassword&token={$token}";
                        $email_sent = true; // Simuler l'envoi d'email
                        
                        if ($email_sent) {
                            $success = "Un email de réinitialisation a été envoyé à votre adresse email.";
                            Security::logSecurityActivity('PASSWORD_RESET_REQUEST', "Demande de réinitialisation de mot de passe pour l'utilisateur #{$user['id']}", $user['id']);
                        } else {
                            $errors[] = "Une erreur s'est produite lors de l'envoi de l'email. Veuillez réessayer.";
                        }
                    } else {
                        $errors[] = "Une erreur s'est produite. Veuillez réessayer.";
                    }
                } else {
                    // Pour des raisons de sécurité, ne pas révéler si l'email existe ou non
                    $success = "Si votre email est enregistré, vous recevrez un email de réinitialisation.";
                }
            } else {
                $errors = array_values($validator->getErrors());
            }
        }
        
        // Afficher la page de demande de réinitialisation
        $this->view('auth/forgot_password', [
            'title' => 'Mot de passe oublié',
            'email' => $email,
            'errors' => $errors,
            'success' => $success
        ]);
    }
    
    /**
     * Réinitialisation du mot de passe - Nouvelle mot de passe
     */
    public function resetPassword() {
        // Rediriger vers le tableau de bord si déjà connecté
        if (isset($_SESSION['user_id'])) {
            $this->redirect('dashboard');
            return;
        }
        
        $token = $_GET['token'] ?? '';
        $errors = [];
        $success = '';
        $token_valid = false;
        $user_id = 0;
        
        // Vérifier si le token est valide
        if (!empty($token)) {
            $token_hash = hash('sha256', $token);
            
            $stmt = $this->conn->prepare("
                SELECT user_id 
                FROM password_resets 
                WHERE token = ? 
                AND expires_at > NOW() 
                AND used = 0
            ");
            
            $stmt->bind_param("s", $token_hash);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows === 1) {
                $token_valid = true;
                $row = $result->fetch_assoc();
                $user_id = $row['user_id'];
            } else {
                $errors[] = "Le lien de réinitialisation est invalide ou a expiré.";
            }
        } else {
            $errors[] = "Token de réinitialisation manquant.";
        }
        
        // Vérifier si le formulaire a été soumis
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $token_valid) {
            // Récupérer les données du formulaire
            $password = $_POST['password'] ?? '';
            $confirm_password = $_POST['confirm_password'] ?? '';
            
            // Valider les données
            $validator = new Validator($_POST);
            $validator->required('password', 'Le mot de passe est requis')
                     ->length('password', 6, null, 'Le mot de passe doit contenir au moins 6 caractères')
                     ->required('confirm_password', 'La confirmation du mot de passe est requise')
                     ->matches('confirm_password', 'password', 'Les mots de passe ne correspondent pas');
            
            if (!$validator->hasErrors()) {
                // Mettre à jour le mot de passe
                $password_hash = Security::hashPassword($password);
                
                $stmt = $this->conn->prepare("UPDATE users SET password = ? WHERE id = ?");
                $stmt->bind_param("si", $password_hash, $user_id);
                
                if ($stmt->execute()) {
                    // Marquer le token comme utilisé
                    $stmt = $this->conn->prepare("UPDATE password_resets SET used = 1 WHERE user_id = ? AND token = ?");
                    $stmt->bind_param("is", $user_id, $token_hash);
                    $stmt->execute();
                    
                    $success = "Votre mot de passe a été réinitialisé avec succès. Vous pouvez maintenant vous connecter.";
                    Security::logSecurityActivity('PASSWORD_RESET', "Réinitialisation du mot de passe pour l'utilisateur #$user_id", $user_id);
                    $token_valid = false; // Ne plus afficher le formulaire
                } else {
                    $errors[] = "Une erreur s'est produite lors de la réinitialisation du mot de passe.";
                }
            } else {
                $errors = array_values($validator->getErrors());
            }
        }
        
        // Afficher la page de réinitialisation
        $this->view('auth/reset_password', [
            'title' => 'Réinitialisation du mot de passe',
            'token' => $token,
            'token_valid' => $token_valid,
            'errors' => $errors,
            'success' => $success
        ]);
    }
    
    /**
     * Définir le cookie "Se souvenir de moi"
     * 
     * @param int $user_id ID de l'utilisateur
     * @return void
     */
    private function setRememberMeCookie($user_id) {
        // Générer un token
        $token = Security::generateAuthToken($user_id);
        
        // Sauvegarder le token dans la base de données
        $stmt = $this->conn->prepare("
            INSERT INTO user_tokens (user_id, selector, token, expires_at) 
            VALUES (?, ?, ?, ?)
        ");
        
        $stmt->bind_param("isss", $token['user_id'], $token['selector'], $token['token_hash'], $token['expires']);
        $stmt->execute();
        
        // Définir les cookies (30 jours)
        setcookie('remember_user', $user_id, time() + 86400 * 30, '/', '', false, true);
        setcookie('remember_selector', $token['selector'], time() + 86400 * 30, '/', '', false, true);
        setcookie('remember_validator', $token['validator'], time() + 86400 * 30, '/', '', false, true);
    }
}