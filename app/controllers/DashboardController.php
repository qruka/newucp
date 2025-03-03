<?php
/**
 * Contrôleur du tableau de bord
 * Gère l'affichage du tableau de bord principal
 */
class DashboardController extends BaseController {
    /**
     * Page principale du tableau de bord
     */
    public function index() {
        // Récupérer les personnages approuvés
        $approved_characters = get_approved_characters($_SESSION['user_id'], $this->conn);
        
        // Récupérer le nombre de personnages en attente (pour les administrateurs)
        $pending_characters_count = 0;
        if ($this->is_admin) {
            $pending_characters = get_pending_characters($this->conn);
            $pending_characters_count = count($pending_characters);
        }
        
        // Générer des notifications 
        $notifications = [];
        
        // Si l'utilisateur a des personnages et que la dernière connexion date d'il y a plus d'un jour
        if (!empty($approved_characters) && isset($this->user['last_login']) && 
            (strtotime($this->user['last_login']) < strtotime('-1 day'))) {
            $notifications[] = [
                'type' => 'alert',
                'message' => 'Vous n\'avez pas vérifié votre compte depuis plus de 24 heures',
                'date' => date('Y-m-d H:i:s'),
                'read' => false
            ];
        }
        
        // Si l'utilisateur a activé la notification de changement d'IP
        if (isset($_SESSION['ip_change_notice']) && $_SESSION['ip_change_notice']) {
            $notifications[] = [
                'type' => 'warn',
                'message' => 'Votre compte a été accédé depuis une nouvelle adresse IP. Si ce n\'était pas vous, veuillez sécuriser votre compte.',
                'date' => date('Y-m-d H:i:s'),
                'read' => false
            ];
            unset($_SESSION['ip_change_notice']);
        }
        
        // Afficher le tableau de bord
        $this->view('dashboard/index', [
            'title' => 'Tableau de bord',
            'approved_characters' => $approved_characters,
            'pending_characters_count' => $pending_characters_count,
            'notifications' => $notifications
        ]);
    }
    
    /**
     * Page des paramètres
     */
    public function settings() {
        $success = '';
        $errors = [];
        
        // Traitement du formulaire de mise à jour du profil
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
            if ($this->isValidFormSubmission()) {
                // Récupérer les données du formulaire
                $name = $_POST['name'] ?? '';
                $email = $_POST['email'] ?? '';
                
                // Valider les données
                $validator = new Validator($_POST);
                $validator->required('name', 'Le nom est requis')
                         ->pattern('name', '/^[a-zA-Z-\' ]*$/', 'Seuls les lettres et les espaces sont autorisés')
                         ->required('email', 'L\'email est requis')
                         ->email('email', 'Format d\'email invalide');
                
                // Vérifier si l'email existe déjà (sauf pour l'utilisateur actuel)
                if (!$validator->hasErrors() && !empty($email) && $email !== $this->user['email']) {
                    $validator->unique('email', 'users', 'email', $_SESSION['user_id'], 'Cet email est déjà utilisé');
                }
                
                if (!$validator->hasErrors()) {
                    // Mettre à jour le profil
                    $stmt = $this->conn->prepare("UPDATE users SET name = ?, email = ? WHERE id = ?");
                    $stmt->bind_param("ssi", $name, $email, $_SESSION['user_id']);
                    
                    if ($stmt->execute()) {
                        $success = "Votre profil a été mis à jour avec succès.";
                        
                        // Mettre à jour le nom dans la session
                        $_SESSION['user_name'] = $name;
                        
                        // Mettre à jour les informations de l'utilisateur
                        $this->user['name'] = $name;
                        $this->user['email'] = $email;
                    } else {
                        $errors[] = "Une erreur s'est produite lors de la mise à jour du profil.";
                    }
                } else {
                    $errors = array_values($validator->getErrors());
                }
            }
        }
        
        // Traitement du formulaire de changement de mot de passe
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
            if ($this->isValidFormSubmission()) {
                // Récupérer les données du formulaire
                $current_password = $_POST['current_password'] ?? '';
                $new_password = $_POST['new_password'] ?? '';
                $confirm_password = $_POST['confirm_password'] ?? '';
                
                // Valider les données
                $validator = new Validator($_POST);
                $validator->required('current_password', 'Le mot de passe actuel est requis')
                         ->required('new_password', 'Le nouveau mot de passe est requis')
                         ->length('new_password', 6, null, 'Le nouveau mot de passe doit contenir au moins 6 caractères')
                         ->required('confirm_password', 'La confirmation du mot de passe est requise')
                         ->matches('confirm_password', 'new_password', 'Les mots de passe ne correspondent pas');
                
                if (!$validator->hasErrors()) {
                    // Vérifier le mot de passe actuel
                    $stmt = $this->conn->prepare("SELECT password FROM users WHERE id = ?");
                    $stmt->bind_param("i", $_SESSION['user_id']);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    
                    if ($result->num_rows === 1) {
                        $user = $result->fetch_assoc();
                        
                        if (password_verify($current_password, $user['password'])) {
                            // Le mot de passe actuel est correct
                            // Hacher le nouveau mot de passe
                            $password_hash = Security::hashPassword($new_password);
                            
                            // Mettre à jour le mot de passe
                            $stmt = $this->conn->prepare("UPDATE users SET password = ? WHERE id = ?");
                            $stmt->bind_param("si", $password_hash, $_SESSION['user_id']);
                            
                            if ($stmt->execute()) {
                                $success = "Votre mot de passe a été modifié avec succès.";
                                Security::logSecurityActivity('PASSWORD_CHANGE', "Changement de mot de passe pour l'utilisateur #{$_SESSION['user_id']}");
                            } else {
                                $errors[] = "Une erreur s'est produite lors de la modification du mot de passe.";
                            }
                        } else {
                            $errors[] = "Le mot de passe actuel est incorrect.";
                        }
                    } else {
                        $errors[] = "Une erreur s'est produite. Veuillez réessayer.";
                    }
                } else {
                    $errors = array_values($validator->getErrors());
                }
            }
        }
        
        // Afficher la page des paramètres
        $this->view('dashboard/settings', [
            'title' => 'Paramètres',
            'success' => $success,
            'errors' => $errors
        ]);
    }
}