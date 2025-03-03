<?php
/**
 * Contrôleur d'administration
 * Gère les fonctionnalités réservées aux administrateurs
 */
class AdminController extends BaseController {
    /**
     * Constructeur: vérifie que l'utilisateur est admin
     */
    public function __construct() {
        parent::__construct();
        
        // Vérifie que l'utilisateur est un administrateur
        if (!$this->is_admin) {
            $this->setFlash("Accès refusé. Vous n'avez pas les droits d'administrateur.", 'error');
            $this->redirect('dashboard');
            exit;
        }
    }
    
    /**
     * Page d'accueil de l'administration
     */
    public function index() {
        $this->view('admin/index', [
            'title' => 'Administration'
        ]);
    }
    
    /**
     * Gestion des utilisateurs
     */
    public function manageUsers() {
        // Récupère tous les utilisateurs
        $users = get_all_users($this->conn);
        
        $this->view('admin/users', [
            'title' => 'Gestion des utilisateurs',
            'users' => $users
        ]);
    }
    
    /**
     * Affiche les détails d'un utilisateur
     */
    public function viewUser() {
        // Vérifier si l'ID est fourni
        if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
            $this->setFlash('ID d\'utilisateur invalide.', 'error');
            $this->redirect('manage_users');
            return;
        }
        
        $user_id = intval($_GET['id']);
        
        // Récupérer les détails de l'utilisateur
        $user = get_user_details($user_id, $this->conn);
        
        // Si l'utilisateur n'existe pas, rediriger
        if (!$user) {
            $this->setFlash('Utilisateur introuvable.', 'error');
            $this->redirect('manage_users');
            return;
        }
        
        // Récupérer l'historique des connexions
        $login_history = get_user_login_history($user_id, $this->conn);
        
        // Vérifier si l'utilisateur est banni
        $ban_info = null;
        if ($user['is_banned']) {
            $ban_query = "
                SELECT 
                    b.reason, 
                    b.banned_at, 
                    b.ban_expiry, 
                    a.name as banned_by
                FROM 
                    user_bans b
                JOIN
                    users a ON b.admin_id = a.id
                WHERE 
                    b.user_id = ? 
                    AND b.is_active = 1
                ORDER BY 
                    b.banned_at DESC
                LIMIT 1
            ";
            
            $stmt = $this->conn->prepare($ban_query);
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $ban_info = $result->fetch_assoc();
            }
        }
        
        $this->view('admin/user_details', [
            'title' => 'Détails de l\'utilisateur',
            'user' => $user,
            'login_history' => $login_history,
            'ban_info' => $ban_info
        ]);
    }
    
    /**
     * Bannir un utilisateur
     */
    public function banUser() {
        // Vérifier si la méthode est bien POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !$this->isValidFormSubmission()) {
            $this->setFlash('Méthode invalide.', 'error');
            $this->redirect('manage_users');
            return;
        }
        
        // Vérifier si l'ID est fourni
        if (!isset($_POST['user_id']) || !is_numeric($_POST['user_id'])) {
            $this->setFlash('ID d\'utilisateur invalide.', 'error');
            $this->redirect('manage_users');
            return;
        }
        
        $user_id = intval($_POST['user_id']);
        
        // Vérifier que l'utilisateur existe
        $user = get_user_details($user_id, $this->conn);
        if (!$user) {
            $this->setFlash('Utilisateur introuvable.', 'error');
            $this->redirect('manage_users');
            return;
        }
        
        // Vérifier que l'utilisateur ne se banne pas lui-même
        if ($user_id === $_SESSION['user_id']) {
            $this->setFlash('Vous ne pouvez pas vous bannir vous-même.', 'error');
            $this->redirect('manage_users');
            return;
        }
        
        // Récupérer les données du formulaire
        $reason = $_POST['reason'] ?? '';
        $duration = isset($_POST['duration']) && is_numeric($_POST['duration']) ? intval($_POST['duration']) : null;
        
        // Bannir l'utilisateur
        if (ban_user($user_id, $_SESSION['user_id'], $reason, $duration, $this->conn)) {
            $this->setFlash('L\'utilisateur a été banni avec succès.', 'success');
            Security::logSecurityActivity('USER_BANNED', "L'utilisateur #{$user_id} a été banni", $_SESSION['user_id']);
        } else {
            $this->setFlash('Une erreur s\'est produite lors du bannissement de l\'utilisateur.', 'error');
        }
        
        $this->redirect('admin', 'viewUser', ['id' => $user_id]);
    }
    
    /**
     * Débannir un utilisateur
     */
    public function unbanUser() {
        // Vérifier si la méthode est bien POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !$this->isValidFormSubmission()) {
            $this->setFlash('Méthode invalide.', 'error');
            $this->redirect('banned_users');
            return;
        }
        
        // Vérifier si l'ID est fourni
        if (!isset($_POST['user_id']) || !is_numeric($_POST['user_id'])) {
            $this->setFlash('ID d\'utilisateur invalide.', 'error');
            $this->redirect('banned_users');
            return;
        }
        
        $user_id = intval($_POST['user_id']);
        
        // Vérifier que l'utilisateur existe
        $user = get_user_details($user_id, $this->conn);
        if (!$user) {
            $this->setFlash('Utilisateur introuvable.', 'error');
            $this->redirect('banned_users');
            return;
        }
        
        // Débannir l'utilisateur
        if (unban_user($user_id, $this->conn)) {
            $this->setFlash('Le bannissement de l\'utilisateur a été levé avec succès.', 'success');
            Security::logSecurityActivity('USER_UNBANNED', "Le bannissement de l'utilisateur #{$user_id} a été levé", $_SESSION['user_id']);
        } else {
            $this->setFlash('Une erreur s\'est produite lors de la levée du bannissement.', 'error');
        }
        
        $this->redirect('banned_users');
    }
    
    /**
     * Promouvoir un utilisateur en administrateur
     */
    public function makeAdmin() {
        // Vérifier si la méthode est bien POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !$this->isValidFormSubmission()) {
            $this->setFlash('Méthode invalide.', 'error');
            $this->redirect('manage_users');
            return;
        }
        
        // Vérifier si l'ID est fourni
        if (!isset($_POST['user_id']) || !is_numeric($_POST['user_id'])) {
            $this->setFlash('ID d\'utilisateur invalide.', 'error');
            $this->redirect('manage_users');
            return;
        }
        
        $user_id = intval($_POST['user_id']);
        
        // Vérifier que l'utilisateur existe
        $user = get_user_details($user_id, $this->conn);
        if (!$user) {
            $this->setFlash('Utilisateur introuvable.', 'error');
            $this->redirect('manage_users');
            return;
        }
        
        // Promouvoir l'utilisateur
        if (update_admin_status($user_id, 1, $this->conn)) {
            $this->setFlash('L\'utilisateur a été promu administrateur avec succès.', 'success');
            Security::logSecurityActivity('USER_PROMOTED', "L'utilisateur #{$user_id} a été promu administrateur", $_SESSION['user_id']);
        } else {
            $this->setFlash('Une erreur s\'est produite lors de la promotion de l\'utilisateur.', 'error');
        }
        
        $this->redirect('manage_users');
    }
    
    /**
     * Révoquer les droits d'administrateur d'un utilisateur
     */
    public function removeAdmin() {
        // Vérifier si la méthode est bien POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !$this->isValidFormSubmission()) {
            $this->setFlash('Méthode invalide.', 'error');
            $this->redirect('manage_users');
            return;
        }
        
        // Vérifier si l'ID est fourni
        if (!isset($_POST['user_id']) || !is_numeric($_POST['user_id'])) {
            $this->setFlash('ID d\'utilisateur invalide.', 'error');
            $this->redirect('manage_users');
            return;
        }
        
        $user_id = intval($_POST['user_id']);
        
        // Vérifier que l'utilisateur existe
        $user = get_user_details($user_id, $this->conn);
        if (!$user) {
            $this->setFlash('Utilisateur introuvable.', 'error');
            $this->redirect('manage_users');
            return;
        }
        
        // Vérifier que l'utilisateur ne révoque pas ses propres droits
        if ($user_id === $_SESSION['user_id']) {
            $this->setFlash('Vous ne pouvez pas révoquer vos propres droits d\'administrateur.', 'error');
            $this->redirect('manage_users');
            return;
        }
        
        // Révoquer les droits d'administrateur
        if (update_admin_status($user_id, 0, $this->conn)) {
            $this->setFlash('Les droits d\'administrateur ont été révoqués avec succès.', 'success');
            Security::logSecurityActivity('ADMIN_DEMOTED', "Les droits d'administrateur de l'utilisateur #{$user_id} ont été révoqués", $_SESSION['user_id']);
        } else {
            $this->setFlash('Une erreur s\'est produite lors de la révocation des droits.', 'error');
        }
        
        $this->redirect('manage_users');
    }
    
    /**
     * Supprimer un utilisateur
     */
    public function deleteUser() {
        // Vérifier si la méthode est bien POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !$this->isValidFormSubmission()) {
            $this->setFlash('Méthode invalide.', 'error');
            $this->redirect('manage_users');
            return;
        }
        
        // Vérifier si l'ID est fourni
        if (!isset($_POST['user_id']) || !is_numeric($_POST['user_id'])) {
            $this->setFlash('ID d\'utilisateur invalide.', 'error');
            $this->redirect('manage_users');
            return;
        }
        
        $user_id = intval($_POST['user_id']);
        
        // Vérifier que l'utilisateur existe
        $user = get_user_details($user_id, $this->conn);
        if (!$user) {
            $this->setFlash('Utilisateur introuvable.', 'error');
            $this->redirect('manage_users');
            return;
        }
        
        // Vérifier que l'utilisateur ne supprime pas son propre compte
        if ($user_id === $_SESSION['user_id']) {
            $this->setFlash('Vous ne pouvez pas supprimer votre propre compte.', 'error');
            $this->redirect('manage_users');
            return;
        }
        
        // Supprimer l'utilisateur
        $stmt = $this->conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        
        if ($stmt->execute()) {
            $this->setFlash('L\'utilisateur a été supprimé avec succès.', 'success');
            Security::logSecurityActivity('USER_DELETED', "L'utilisateur #{$user_id} a été supprimé", $_SESSION['user_id']);
        } else {
            $this->setFlash('Une erreur s\'est produite lors de la suppression de l\'utilisateur.', 'error');
        }
        
        $this->redirect('manage_users');
    }
    
    /**
     * Gestion des personnages en attente de validation
     */
    public function adminCharacters() {
        // Récupérer les personnages en attente
        $pending_characters = get_pending_characters($this->conn);
        
        $this->view('admin/characters', [
            'title' => 'Validation des personnages',
            'pending_characters' => $pending_characters
        ]);
    }
    
    /**
     * Examiner un personnage en attente de validation
     */
    public function reviewCharacter() {
        // Vérifier si l'ID est fourni
        if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
            $this->setFlash('ID de personnage invalide.', 'error');
            $this->redirect('admin_characters');
            return;
        }
        
        $character_id = intval($_GET['id']);
        
        // Récupérer les détails du personnage
        $character = get_character_details($character_id, $this->conn);
        
        // Si le personnage n'existe pas ou n'est pas en attente, rediriger
        if (!$character || $character['status'] !== 'pending') {
            $this->setFlash('Personnage introuvable ou déjà traité.', 'error');
            $this->redirect('admin_characters');
            return;
        }
        
        $errors = [];
        
        // Traitement du formulaire de validation
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $this->isValidFormSubmission()) {
            $status = $_POST['status'] ?? '';
            $comment = $_POST['comment'] ?? '';
            
            // Valider le statut
            if ($status !== 'approved' && $status !== 'rejected') {
                $errors[] = "Le statut doit être 'approved' ou 'rejected'";
            }
            
            // Si le statut est 'rejected', un commentaire est requis
            if ($status === 'rejected' && empty($comment)) {
                $errors[] = "Un commentaire est requis pour expliquer le rejet";
            }
            
            if (empty($errors)) {
                // Mettre à jour le statut du personnage
                if (review_character($character_id, $_SESSION['user_id'], $status, $comment, $this->conn)) {
                    $action = $status === 'approved' ? 'approuvé' : 'rejeté';
                    $this->setFlash("Le personnage a été {$action} avec succès.", 'success');
                    $this->redirect('admin_characters');
                    return;
                } else {
                    $errors[] = "Une erreur s'est produite lors de la mise à jour du statut du personnage.";
                }
            }
        }
        
        $this->view('admin/review_character', [
            'title' => 'Examen de personnage',
            'character' => $character,
            'errors' => $errors
        ]);
    }
    
    /**
     * Affiche la liste des utilisateurs bannis
     */
    public function bannedUsers() {
        // Récupérer les utilisateurs bannis
        $banned_users = get_banned_users($this->conn);
        
        $this->view('admin/banned_users', [
            'title' => 'Utilisateurs bannis',
            'banned_users' => $banned_users
        ]);
    }
    
    /**
     * Affiche les alertes de sécurité
     */
    public function securityAlerts() {
        // Récupérer les changements d'IP récents
        $ip_changes = get_ip_changes($this->conn, 50);
        
        $this->view('admin/security_alerts', [
            'title' => 'Alertes de sécurité',
            'ip_changes' => $ip_changes
        ]);
    }
    
    /**
     * Modifier le profil d'un administrateur
     */
    public function adminProfile() {
        // Vérifier si l'ID est fourni
        if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
            $this->setFlash('ID d\'utilisateur invalide.', 'error');
            $this->redirect('team');
            return;
        }
        
        $user_id = intval($_GET['id']);
        
        // Récupérer les détails de l'utilisateur
        $user = get_user_details($user_id, $this->conn);
        
        // Si l'utilisateur n'existe pas ou n'est pas admin, rediriger
        if (!$user || !$user['is_admin']) {
            $this->setFlash('Administrateur introuvable.', 'error');
            $this->redirect('team');
            return;
        }
        
        $errors = [];
        $success = '';
        
        // Traitement du formulaire
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $this->isValidFormSubmission()) {
            $role = $_POST['role'] ?? '';
            $bio = $_POST['bio'] ?? '';
            
            // Valider les données
            $validator = new Validator($_POST);
            $validator->length('role', 0, 100, 'Le rôle ne doit pas dépasser 100 caractères')
                     ->length('bio', 0, 1000, 'La biographie ne doit pas dépasser 1000 caractères');
            
            if (!$validator->hasErrors()) {
                // Mettre à jour le profil
                if (update_admin_profile($user_id, $role, $bio, $this->conn)) {
                    $success = "Le profil d'administrateur a été mis à jour avec succès.";
                    // Rafraîchir les données de l'utilisateur
                    $user = get_user_details($user_id, $this->conn);
                } else {
                    $errors[] = "Une erreur s'est produite lors de la mise à jour du profil.";
                }
            } else {
                $errors = array_values($validator->getErrors());
            }
        }
        
        $this->view('admin/profile', [
            'title' => 'Édition de profil administrateur',
            'user' => $user,
            'errors' => $errors,
            'success' => $success
        ]);
    }
}