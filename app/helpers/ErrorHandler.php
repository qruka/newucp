<?php
/**
 * Gestionnaire d'erreurs
 * Gère les erreurs et exceptions de manière centralisée
 */
class ErrorHandler {
    /**
     * Gérer une erreur PHP
     * 
     * @param int $errno Niveau de l'erreur
     * @param string $errstr Message d'erreur
     * @param string $errfile Fichier où l'erreur s'est produite
     * @param int $errline Ligne où l'erreur s'est produite
     * @return bool
     */
    public function handleError($errno, $errstr, $errfile, $errline) {
        // Générer le message d'erreur détaillé
        $error = "Erreur [$errno] $errstr - $errfile:$errline";
        
        // Journaliser l'erreur
        $this->logError($error);
        
        // En mode développement, afficher les détails de l'erreur
        if (APP_ENV === 'development') {
            echo $this->formatErrorForDisplay($error, $errfile, $errline);
        } else {
            // En production, afficher un message générique
            if ($this->isFatalError($errno)) {
                $this->displayProductionError();
            }
        }
        
        // Ne pas exécuter le gestionnaire d'erreurs interne de PHP
        return true;
    }
    
    /**
     * Gérer une exception non capturée
     * 
     * @param Throwable $exception Exception non capturée
     * @return void
     */
    public function handleException($exception) {
        // Générer le message d'erreur détaillé
        $error = "Exception: " . $exception->getMessage();
        
        // Journaliser l'erreur
        $this->logError($error);
        
        // En mode développement, afficher les détails de l'exception
        if (APP_ENV === 'development') {
            echo $this->formatExceptionForDisplay($exception);
        } else {
            // En production, afficher un message générique
            $this->displayProductionError();
        }
        
        exit(1);
    }
    
    /**
     * Vérifier si l'erreur est fatale
     * 
     * @param int $errno Niveau de l'erreur
     * @return bool
     */
    private function isFatalError($errno) {
        return in_array($errno, [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR]);
    }
    
    /**
     * Journaliser une erreur ou une exception
     * 
     * @param string $message Message à journaliser
     * @return void
     */
    private function logError($message) {
        $logFile = APP_ROOT . '/logs/error.log';
        $timestamp = date('Y-m-d H:i:s');
        $logMessage = "[$timestamp] $message" . PHP_EOL;
        
        // Créer le répertoire des logs s'il n'existe pas
        if (!is_dir(dirname($logFile))) {
            mkdir(dirname($logFile), 0755, true);
        }
        
        // Écrire dans le fichier de log
        error_log($logMessage, 3, $logFile);
    }
    
    /**
     * Formater une erreur pour l'affichage en mode développement
     * 
     * @param string $error Message d'erreur
     * @param string $errfile Fichier où l'erreur s'est produite
     * @param int $errline Ligne où l'erreur s'est produite
     * @return string
     */
    private function formatErrorForDisplay($error, $errfile, $errline) {
        $output = '<div style="background: #f8d7da; color: #721c24; padding: 10px; margin: 10px; border-radius: 5px; border: 1px solid #f5c6cb;">';
        $output .= '<h2 style="margin-top: 0;">Erreur PHP</h2>';
        $output .= "<p><strong>Message:</strong> $error</p>";
        $output .= "<p><strong>Fichier:</strong> $errfile</p>";
        $output .= "<p><strong>Ligne:</strong> $errline</p>";
        
        // Ajouter la trace d'appel
        $output .= '<h3>Trace d\'appel:</h3>';
        $output .= '<ol style="background: #f9f9f9; padding: 10px; border-radius: 5px;">';
        
        $trace = debug_backtrace();
        // Ignorer les premiers appels (ceux du gestionnaire d'erreurs)
        $trace = array_slice($trace, 2);
        
        foreach ($trace as $i => $step) {
            $file = $step['file'] ?? '[fichier inconnu]';
            $line = $step['line'] ?? '[ligne inconnue]';
            $function = $step['function'] ?? '[fonction inconnue]';
            $class = isset($step['class']) ? $step['class'] . $step['type'] : '';
            
            $output .= "<li><strong>$file</strong> ($line): $class$function()</li>";
        }
        
        $output .= '</ol>';
        $output .= '</div>';
        
        return $output;
    }
    
    /**
     * Formater une exception pour l'affichage en mode développement
     * 
     * @param Throwable $exception Exception
     * @return string
     */
    private function formatExceptionForDisplay($exception) {
        $output = '<div style="background: #f8d7da; color: #721c24; padding: 10px; margin: 10px; border-radius: 5px; border: 1px solid #f5c6cb;">';
        $output .= '<h2 style="margin-top: 0;">Exception non capturée</h2>';
        $output .= "<p><strong>Message:</strong> " . $exception->getMessage() . "</p>";
        $output .= "<p><strong>Fichier:</strong> " . $exception->getFile() . "</p>";
        $output .= "<p><strong>Ligne:</strong> " . $exception->getLine() . "</p>";
        
        // Ajouter la trace d'appel
        $output .= '<h3>Trace d\'appel:</h3>';
        $output .= '<ol style="background: #f9f9f9; padding: 10px; border-radius: 5px;">';
        
        $trace = $exception->getTrace();
        
        foreach ($trace as $i => $step) {
            $file = $step['file'] ?? '[fichier inconnu]';
            $line = $step['line'] ?? '[ligne inconnue]';
            $function = $step['function'] ?? '[fonction inconnue]';
            $class = isset($step['class']) ? $step['class'] . $step['type'] : '';
            
            $output .= "<li><strong>$file</strong> ($line): $class$function()</li>";
        }
        
        $output .= '</ol>';
        $output .= '</div>';
        
        return $output;
    }
    
    /**
     * Afficher un message d'erreur générique en production
     * 
     * @return void
     */
    private function displayProductionError() {
        http_response_code(500);
        include APP_ROOT . '/app/views/errors/500.php';
        exit;
    }
}