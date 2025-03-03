<?php
/**
 * Classe de validation des données
 * Permet de valider les données des formulaires
 */
class Validator {
    /**
     * Données à valider
     * @var array
     */
    private $data = [];
    
    /**
     * Erreurs de validation
     * @var array
     */
    private $errors = [];
    
    /**
     * Constructeur
     * 
     * @param array $data Données à valider
     */
    public function __construct($data) {
        $this->data = $data;
    }
    
    /**
     * Valider que le champ est requis
     * 
     * @param string $field Nom du champ
     * @param string $message Message d'erreur personnalisé
     * @return Validator
     */
    public function required($field, $message = null) {
        if (!isset($this->data[$field]) || trim($this->data[$field]) === '') {
            $this->errors[$field] = $message ?? "Le champ $field est requis";
        }
        
        return $this;
    }
    
    /**
     * Valider que le champ est une adresse email valide
     * 
     * @param string $field Nom du champ
     * @param string $message Message d'erreur personnalisé
     * @return Validator
     */
    public function email($field, $message = null) {
        if (isset($this->data[$field]) && !empty($this->data[$field])) {
            if (!filter_var($this->data[$field], FILTER_VALIDATE_EMAIL)) {
                $this->errors[$field] = $message ?? "Le champ $field doit être une adresse email valide";
            }
        }
        
        return $this;
    }
    
    /**
     * Valider que le champ est un nombre
     * 
     * @param string $field Nom du champ
     * @param int|null $min Valeur minimale
     * @param int|null $max Valeur maximale
     * @param string $message Message d'erreur personnalisé
     * @return Validator
     */
    public function numeric($field, $min = null, $max = null, $message = null) {
        if (isset($this->data[$field]) && !empty($this->data[$field])) {
            if (!is_numeric($this->data[$field])) {
                $this->errors[$field] = $message ?? "Le champ $field doit être un nombre";
            } else {
                $value = (float) $this->data[$field];
                
                if ($min !== null && $value < $min) {
                    $this->errors[$field] = "Le champ $field doit être supérieur ou égal à $min";
                }
                
                if ($max !== null && $value > $max) {
                    $this->errors[$field] = "Le champ $field doit être inférieur ou égal à $max";
                }
            }
        }
        
        return $this;
    }
    
    /**
     * Valider que le champ a une longueur spécifique
     * 
     * @param string $field Nom du champ
     * @param int $min Longueur minimale
     * @param int|null $max Longueur maximale
     * @param string $message Message d'erreur personnalisé
     * @return Validator
     */
    public function length($field, $min, $max = null, $message = null) {
        if (isset($this->data[$field]) && !empty($this->data[$field])) {
            $length = mb_strlen($this->data[$field]);
            
            if ($length < $min) {
                $this->errors[$field] = $message ?? "Le champ $field doit contenir au moins $min caractères";
            }
            
            if ($max !== null && $length > $max) {
                $this->errors[$field] = $message ?? "Le champ $field ne doit pas dépasser $max caractères";
            }
        }
        
        return $this;
    }
    
    /**
     * Valider que le champ correspond à un motif regex
     * 
     * @param string $field Nom du champ
     * @param string $pattern Motif regex
     * @param string $message Message d'erreur personnalisé
     * @return Validator
     */
    public function pattern($field, $pattern, $message = null) {
        if (isset($this->data[$field]) && !empty($this->data[$field])) {
            if (!preg_match($pattern, $this->data[$field])) {
                $this->errors[$field] = $message ?? "Le champ $field n'est pas au format attendu";
            }
        }
        
        return $this;
    }
    
    /**
     * Valider que le champ correspond à un autre champ
     * 
     * @param string $field Nom du champ
     * @param string $match Nom du champ à comparer
     * @param string $message Message d'erreur personnalisé
     * @return Validator
     */
    public function matches($field, $match, $message = null) {
        if (isset($this->data[$field]) && isset($this->data[$match])) {
            if ($this->data[$field] !== $this->data[$match]) {
                $this->errors[$field] = $message ?? "Le champ $field doit correspondre au champ $match";
            }
        }
        
        return $this;
    }
    
    /**
     * Valider que le champ est unique dans la table
     * 
     * @param string $field Nom du champ
     * @param string $table Nom de la table
     * @param string $column Nom de la colonne (si différent du champ)
     * @param int|null $ignore ID à ignorer
     * @param string $message Message d'erreur personnalisé
     * @return Validator
     */
    public function unique($field, $table, $column = null, $ignore = null, $message = null) {
        if (isset($this->data[$field]) && !empty($this->data[$field])) {
            $column = $column ?? $field;
            global $conn;
            
            $sql = "SELECT COUNT(*) as count FROM $table WHERE $column = ?";
            $params = [$this->data[$field]];
            
            if ($ignore !== null) {
                $sql .= " AND id != ?";
                $params[] = $ignore;
            }
            
            $stmt = $conn->prepare($sql);
            $stmt->bind_param(str_repeat('s', count($params)), ...$params);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            
            if ($row['count'] > 0) {
                $this->errors[$field] = $message ?? "La valeur du champ $field est déjà utilisée";
            }
        }
        
        return $this;
    }
    
    /**
     * Valider que le champ est une date valide
     * 
     * @param string $field Nom du champ
     * @param string $format Format de la date
     * @param string $message Message d'erreur personnalisé
     * @return Validator
     */
    public function date($field, $format = 'Y-m-d', $message = null) {
        if (isset($this->data[$field]) && !empty($this->data[$field])) {
            $date = \DateTime::createFromFormat($format, $this->data[$field]);
            
            if (!$date || $date->format($format) !== $this->data[$field]) {
                $this->errors[$field] = $message ?? "Le champ $field doit être une date valide au format $format";
            }
        }
        
        return $this;
    }
    
    /**
     * Vérifier si la validation a échoué
     * 
     * @return bool
     */
    public function hasErrors() {
        return !empty($this->errors);
    }
    
    /**
     * Récupérer toutes les erreurs
     * 
     * @return array
     */
    public function getErrors() {
        return $this->errors;
    }
    
    /**
     * Récupérer la première erreur
     * 
     * @return string|null
     */
    public function getFirstError() {
        return !empty($this->errors) ? reset($this->errors) : null;
    }
    
    /**
     * Récupérer les erreurs pour un champ spécifique
     * 
     * @param string $field Nom du champ
     * @return string|null
     */
    public function getError($field) {
        return $this->errors[$field] ?? null;
    }
    
    /**
     * Nettoyer une valeur
     * 
     * @param string $value Valeur à nettoyer
     * @return string
     */
    public static function sanitize($value) {
        return htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
    }
}