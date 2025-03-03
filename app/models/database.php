<?php
/**
 * Classe de gestion de la base de données
 * Fournit des méthodes pour interagir avec la base de données
 */
class Database {
    private static $instance = null;
    private $conn;
    private $stmt;
    
    /**
     * Constructeur privé (pattern Singleton)
     */
    private function __construct() {
        $db_config = require APP_ROOT . '/app/config/database.php';
        
        try {
            $dsn = "mysql:host={$db_config['host']};dbname={$db_config['database']};charset={$db_config['charset']}";
            $this->conn = new PDO($dsn, $db_config['username'], $db_config['password'], $db_config['options']);
        } catch (PDOException $e) {
            if (APP_ENV === 'development') {
                throw new Exception("Erreur de connexion à la base de données: " . $e->getMessage());
            } else {
                throw new Exception("Une erreur est survenue lors de la connexion à la base de données.");
            }
        }
    }
    
    /**
     * Récupérer l'instance unique de la classe (pattern Singleton)
     * 
     * @return Database
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        
        return self::$instance;
    }
    
    /**
     * Préparer une requête SQL
     * 
     * @param string $sql Requête SQL avec placeholders
     * @return Database
     */
    public function prepare($sql) {
        $this->stmt = $this->conn->prepare($sql);
        return $this;
    }
    
    /**
     * Lier des valeurs aux paramètres de la requête
     * 
     * @param array $params Tableau associatif des paramètres
     * @return Database
     */
    public function bind($params) {
        foreach ($params as $param => $value) {
            $param = is_numeric($param) ? $param + 1 : $param;
            
            switch (true) {
                case is_int($value):
                    $type = PDO::PARAM_INT;
                    break;
                case is_bool($value):
                    $type = PDO::PARAM_BOOL;
                    break;
                case is_null($value):
                    $type = PDO::PARAM_NULL;
                    break;
                default:
                    $type = PDO::PARAM_STR;
            }
            
            $this->stmt->bindValue($param, $value, $type);
        }
        
        return $this;
    }
    
    /**
     * Exécuter la requête préparée
     * 
     * @return bool
     */
    public function execute() {
        return $this->stmt->execute();
    }
    
    /**
     * Récupérer un seul enregistrement
     * 
     * @return mixed
     */
    public function fetch() {
        $this->execute();
        return $this->stmt->fetch();
    }
    
    /**
     * Récupérer tous les enregistrements
     * 
     * @return array
     */
    public function fetchAll() {
        $this->execute();
        return $this->stmt->fetchAll();
    }
    
    /**
     * Compter le nombre d'enregistrements affectés
     * 
     * @return int
     */
    public function rowCount() {
        return $this->stmt->rowCount();
    }
    
    /**
     * Récupérer le dernier ID inséré
     * 
     * @return string
     */
    public function lastInsertId() {
        return $this->conn->lastInsertId();
    }
    
    /**
     * Exécuter une requête et récupérer un seul enregistrement
     * 
     * @param string $sql Requête SQL
     * @param array $params Paramètres de la requête
     * @return mixed
     */
    public function single($sql, $params = []) {
        $this->prepare($sql);
        $this->bind($params);
        return $this->fetch();
    }
    
    /**
     * Exécuter une requête et récupérer tous les enregistrements
     * 
     * @param string $sql Requête SQL
     * @param array $params Paramètres de la requête
     * @return array
     */
    public function query($sql, $params = []) {
        $this->prepare($sql);
        $this->bind($params);
        return $this->fetchAll();
    }
    
    /**
     * Insérer des données dans une table
     * 
     * @param string $table Nom de la table
     * @param array $data Données à insérer
     * @return int|bool ID de l'enregistrement inséré ou false
     */
    public function insert($table, $data) {
        $columns = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));
        
        $sql = "INSERT INTO {$table} ({$columns}) VALUES ({$placeholders})";
        
        if ($this->prepare($sql)->bind($data)->execute()) {
            return $this->lastInsertId();
        }
        
        return false;
    }
    
    /**
     * Mettre à jour des données dans une table
     * 
     * @param string $table Nom de la table
     * @param array $data Données à mettre à jour
     * @param string $condition Condition WHERE
     * @param array $params Paramètres de la condition
     * @return bool
     */
    public function update($table, $data, $condition, $params = []) {
        $set = [];
        
        foreach ($data as $column => $value) {
            $set[] = "{$column} = :{$column}";
        }
        
        $set_string = implode(', ', $set);
        
        $sql = "UPDATE {$table} SET {$set_string} WHERE {$condition}";
        
        $this->prepare($sql);
        $this->bind(array_merge($data, $params));
        
        return $this->execute();
    }
    
    /**
     * Supprimer des données d'une table
     * 
     * @param string $table Nom de la table
     * @param string $condition Condition WHERE
     * @param array $params Paramètres de la condition
     * @return bool
     */
    public function delete($table, $condition, $params = []) {
        $sql = "DELETE FROM {$table} WHERE {$condition}";
        
        $this->prepare($sql);
        $this->bind($params);
        
        return $this->execute();
    }
    
    /**
     * Démarrer une transaction
     * 
     * @return bool
     */
    public function beginTransaction() {
        return $this->conn->beginTransaction();
    }
    
    /**
     * Valider une transaction
     * 
     * @return bool
     */
    public function commit() {
        return $this->conn->commit();
    }
    
    /**
     * Annuler une transaction
     * 
     * @return bool
     */
    public function rollBack() {
        return $this->conn->rollBack();
    }
}