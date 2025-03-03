<?php
/**
 * Configuration de la base de données
 * Ce fichier contient toutes les informations nécessaires pour se connecter à la BDD
 */

return [
    'host' => 'localhost',
    'username' => 'root',
    'password' => '',
    'database' => 'ucp',
    'charset' => 'utf8mb4',
    'options' => [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]
];