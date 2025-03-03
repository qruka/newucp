<?php
// Initialisation de la session et des configurations
require_once '../app/config/init.php';

// Routeur simple
$route = $_GET['route'] ?? 'home';

// Routes autorisées et leurs contrôleurs
$routes = [
    'home' => 'HomeController',
    'login' => 'AuthController@login',
    'register' => 'AuthController@register',
    'dashboard' => 'DashboardController',
    'characters' => 'CharacterController@index',
    // etc.
];

// Vérifier si la route existe
if (isset($routes[$route])) {
    $parts = explode('@', $routes[$route]);
    $controller = $parts[0];
    $method = $parts[1] ?? 'index';
    
    // Charger le contrôleur
    require_once "../app/controllers/{$controller}.php";
    $controllerInstance = new $controller();
    $controllerInstance->$method();
} else {
    // Page 404
    header('HTTP/1.0 404 Not Found');
    require_once '../app/views/errors/404.php';
}