<?php
/**
 * Contrôleur de gestion des personnages
 * Gère l'affichage, la création et la modification des personnages
 */
class CharacterController extends BaseController {
    /**
     * Affiche la liste des personnages de l'utilisateur
     */
    public function index() {
        // Récupérer tous les personnages de l'utilisateur
        $characters = get_user_characters($_SESSION['user_id'], $this->conn);
        
        $this->view('characters/index', [
            'title' => 'Mes personnages',
            'characters' => $characters
        ]);
    }
    
    /**
     * Affiche les détails d'un personnage
     */
    public function view() {
        // Vérifier si l'ID est fourni
        if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
            $this->setFlash('ID de personnage invalide.', 'error');
            $this->redirect('characters');
            return;
        }
        
        $character_id = intval($_GET['id']);
        
        // Vérifier si l'utilisateur a le droit de voir ce personnage
        if (!can_view_character($_SESSION['user_id'], $character_id, $this->is_admin, $this->conn)) {
            $this->setFlash('Vous n\'avez pas accès à ce personnage.', 'error');
            $this->redirect('characters');
            return;
        }
        
        // Récupérer les détails du personnage
        $character = get_character_details($character_id, $this->conn);
        
        // Si le personnage n'existe pas, rediriger
        if (!$character) {
            $this->setFlash('Personnage introuvable.', 'error');
            $this->redirect('characters');
            return;
        }
        
        $this->view('characters/view', [
            'title' => 'Détails du personnage',
            'character' => $character
        ]);
    }
    
    /**
     * Affiche le formulaire de création de personnage
     */
    public function create() {
        $errors = [];
        $success = '';
        $character_data = [
            'first_last_name' => '',
            'age' => '',
            'ethnicity' => '',
            'background' => ''
        ];
        
        // Traitement du formulaire
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $this->isValidFormSubmission()) {
            // Récupérer les données du formulaire
            $character_data = [
                'first_last_name' => $_POST['first_last_name'] ?? '',
                'age' => $_POST['age'] ?? '',
                'ethnicity' => $_POST['ethnicity'] ?? '',
                'background' => $_POST['background'] ?? ''
            ];
            
            // Valider les données
            $validator = new Validator($_POST);
            $validator->required('first_last_name', 'Le prénom et nom est requis')
                     ->length('first_last_name', 2, 100, 'Le prénom et nom doit comporter entre 2 et 100 caractères')
                     ->required('age', 'L\'âge est requis')
                     ->numeric('age', 1, 120, 'L\'âge doit être un nombre entre 1 et 120')
                     ->required('ethnicity', 'L\'ethnie est requise')
                     ->length('ethnicity', 2, 100, 'L\'ethnie doit comporter entre 2 et 100 caractères')
                     ->required('background', 'Le background est requis')
                     ->length('background', 50, 5000, 'Le background doit comporter entre 50 et 5000 caractères');
            
            if (!$validator->hasErrors()) {
                // Créer le personnage
                if (create_character(
                    $_SESSION['user_id'],
                    $character_data['first_last_name'],
                    (int)$character_data['age'],
                    $character_data['ethnicity'],
                    $character_data['background'],
                    $this->conn
                )) {
                    $this->setFlash('Votre personnage a été créé avec succès et est en attente de validation par un administrateur.', 'success');
                    $this->redirect('characters');
                    return;
                } else {
                    $errors[] = "Une erreur s'est produite lors de la création du personnage. Veuillez réessayer.";
                }
            } else {
                $errors = array_values($validator->getErrors());
            }
        }
        
        $this->view('characters/create', [
            'title' => 'Créer un personnage',
            'errors' => $errors,
            'success' => $success,
            'character' => $character_data
        ]);
    }
    
    /**
     * Affiche le formulaire de modification d'un personnage
     */
    public function edit() {
        // Vérifier si l'ID est fourni
        if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
            $this->setFlash('ID de personnage invalide.', 'error');
            $this->redirect('characters');
            return;
        }
        
        $character_id = intval($_GET['id']);
        
        // Vérifier si l'utilisateur a le droit de modifier ce personnage
        if (!can_view_character($_SESSION['user_id'], $character_id, $this->is_admin, $this->conn)) {
            $this->setFlash('Vous n\'avez pas accès à ce personnage.', 'error');
            $this->redirect('characters');
            return;
        }
        
        // Récupérer les détails du personnage
        $character = get_character_details($character_id, $this->conn);
        
        // Si le personnage n'existe pas, rediriger
        if (!$character) {
            $this->setFlash('Personnage introuvable.', 'error');
            $this->redirect('characters');
            return;
        }
        
        // Vérifier si le personnage est en attente ou rejeté (seuls ces statuts peuvent être modifiés)
        if (!in_array($character['status'], ['pending', 'rejected'])) {
            $this->setFlash('Ce personnage ne peut pas être modifié car il a déjà été approuvé.', 'error');
            $this->redirect('characters');
            return;
        }
        
        $errors = [];
        $success = '';
        
        // Traitement du formulaire
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $this->isValidFormSubmission()) {
            // Récupérer les données du formulaire
            $character_data = [
                'first_last_name' => $_POST['first_last_name'] ?? '',
                'age' => $_POST['age'] ?? '',
                'ethnicity' => $_POST['ethnicity'] ?? '',
                'background' => $_POST['background'] ?? ''
            ];
            
            // Valider les données
            $validator = new Validator($_POST);
            $validator->required('first_last_name', 'Le prénom et nom est requis')
                     ->length('first_last_name', 2, 100, 'Le prénom et nom doit comporter entre 2 et 100 caractères')
                     ->required('age', 'L\'âge est requis')
                     ->numeric('age', 1, 120, 'L\'âge doit être un nombre entre 1 et 120')
                     ->required('ethnicity', 'L\'ethnie est requise')
                     ->length('ethnicity', 2, 100, 'L\'ethnie doit comporter entre 2 et 100 caractères')
                     ->required('background', 'Le background est requis')
                     ->length('background', 50, 5000, 'Le background doit comporter entre 50 et 5000 caractères');
            
            if (!$validator->hasErrors()) {
                // Mettre à jour le personnage
                $stmt = $this->conn->prepare("
                    UPDATE characters 
                    SET first_last_name = ?, age = ?, ethnicity = ?, background = ?, status = 'pending', admin_comment = NULL
                    WHERE id = ? AND user_id = ?
                ");
                
                $stmt->bind_param(
                    "sissii",
                    $character_data['first_last_name'],
                    $character_data['age'],
                    $character_data['ethnicity'],
                    $character_data['background'],
                    $character_id,
                    $_SESSION['user_id']
                );
                
                if ($stmt->execute()) {
                    $this->setFlash('Votre personnage a été mis à jour avec succès et est à nouveau en attente de validation.', 'success');
                    $this->redirect('characters');
                    return;
                } else {
                    $errors[] = "Une erreur s'est produite lors de la mise à jour du personnage. Veuillez réessayer.";
                }
            } else {
                $errors = array_values($validator->getErrors());
            }
            
            // Mettre à jour les données du personnage avec les valeurs soumises
            $character = array_merge($character, $character_data);
        }
        
        $this->view('characters/edit', [
            'title' => 'Modifier un personnage',
            'character' => $character,
            'errors' => $errors,
            'success' => $success
        ]);
    }
    
    /**
     * Supprime un personnage
     */
    public function delete() {
        // Vérifier si la méthode est bien POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !$this->isValidFormSubmission()) {
            $this->setFlash('Méthode invalide.', 'error');
            $this->redirect('characters');
            return;
        }
        
        // Vérifier si l'ID est fourni
        if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
            $this->setFlash('ID de personnage invalide.', 'error');
            $this->redirect('characters');
            return;
        }
        
        $character_id = intval($_POST['id']);
        
        // Vérifier si l'utilisateur a le droit de supprimer ce personnage
        $stmt = $this->conn->prepare("SELECT id FROM characters WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ii", $character_id, $_SESSION['user_id']);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows !== 1) {
            $this->setFlash('Vous n\'avez pas le droit de supprimer ce personnage.', 'error');
            $this->redirect('characters');
            return;
        }
        
        // Supprimer le personnage
        $stmt = $this->conn->prepare("DELETE FROM characters WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ii", $character_id, $_SESSION['user_id']);
        
        if ($stmt->execute()) {
            $this->setFlash('Le personnage a été supprimé avec succès.', 'success');
        } else {
            $this->setFlash('Une erreur s\'est produite lors de la suppression du personnage.', 'error');
        }
        
        $this->redirect('characters');
    }
}