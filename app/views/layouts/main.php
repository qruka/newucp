<!DOCTYPE html>
<html lang="fr" class="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? 'monUCP'; ?></title>
    
    <!-- Intégration de Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#f0f9ff',
                            100: '#e0f2fe',
                            200: '#bae6fd',
                            300: '#7dd3fc',
                            400: '#38bdf8',
                            500: '#0ea5e9',
                            600: '#0284c7',
                            700: '#0369a1',
                            800: '#075985',
                            900: '#0c4a6e',
                        }
                    }
                }
            }
        }
    </script>
    
    <!-- CSS personnalisé -->
    <link rel="stylesheet" href="assets/css/main.css">
    <link rel="stylesheet" href="assets/css/animations.css">
    
    <style>
        /* Styles critiques inlinés pour éviter le FOUC (Flash Of Unstyled Content) */
        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        .slide-in {
            animation: slideIn 0.4s ease-out;
        }
        
        @keyframes slideIn {
            from { transform: translateY(-20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        
        /* Toggle switch pour le dark mode */
        .toggle-checkbox:checked {
            right: 0;
            border-color: #68D391;
        }
        .toggle-checkbox:checked + .toggle-label {
            background-color: #68D391;
        }
        
        /* Effet de survol pour les cartes */
        .hover-card {
            transition: all 0.3s ease;
        }
        
        .hover-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }
        
        /* Effet de gradient animé */
        .gradient-background {
            background: linear-gradient(-45deg, #ee7752, #e73c7e, #23a6d5, #23d5ab);
            background-size: 400% 400%;
            animation: gradient 15s ease infinite;
        }
        
        @keyframes gradient {
            0% {
                background-position: 0% 50%;
            }
            50% {
                background-position: 100% 50%;
            }
            100% {
                background-position: 0% 50%;
            }
        }
    </style>
</head>
<body class="bg-gray-100 dark:bg-gray-900 text-gray-800 dark:text-gray-200 transition-colors duration-200">
    <?php if (isset($_SESSION['user_id'])): ?>
    <!-- Layout pour les utilisateurs connectés -->
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar / Navigation latérale -->
        <div class="hidden md:flex md:flex-shrink-0">
            <div class="flex flex-col w-64 bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 transition-colors duration-200">
                <div class="flex flex-col flex-grow pt-5 pb-4 overflow-y-auto">
                    <div class="flex items-center flex-shrink-0 px-4 mb-5">
                        <span class="text-2xl font-bold text-blue-600 dark:text-blue-400">monUCP</span>
                    </div>
                    <div class="mt-5 flex-grow flex flex-col">
                        <nav class="flex-1 px-2 space-y-1">
                            <!-- Menu principal -->
                            <div class="space-y-1">
                                <a href="index.php?route=dashboard" class="<?php echo $route === 'dashboard' ? 'bg-gray-100 dark:bg-gray-700' : ''; ?> text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 group flex items-center px-2 py-2 text-sm font-medium rounded-md transition-colors duration-200">
                                    <svg class="mr-3 h-6 w-6 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                                    </svg>
                                    Accueil
                                </a>
                                
                                <a href="index.php?route=characters" class="<?php echo $route === 'characters' || $route === 'view_character' ? 'bg-gray-100 dark:bg-gray-700' : ''; ?> text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 group flex items-center px-2 py-2 text-sm font-medium rounded-md transition-colors duration-200">
                                    <svg class="mr-3 h-6 w-6 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    Personnages
                                </a>
                                
                                <a href="index.php?route=team" class="<?php echo $route === 'team' ? 'bg-gray-100 dark:bg-gray-700' : ''; ?> text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 group flex items-center px-2 py-2 text-sm font-medium rounded-md transition-colors duration-200">
                                    <svg class="mr-3 h-6 w-6 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                    </svg>
                                    L'équipe
                                </a>
                                
                                <a href="index.php?route=settings" class="<?php echo $route === 'settings' ? 'bg-gray-100 dark:bg-gray-700' : ''; ?> text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 group flex items-center px-2 py-2 text-sm font-medium rounded-md transition-colors duration-200">
                                    <svg class="mr-3 h-6 w-6 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                    Profil & Paramètres
                                </a>
                            </div>
                            
                            <?php if ($is_admin): ?>
                            <!-- Menu Administration -->
                            <div class="mt-8">
                                <h3 class="px-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Administration
                                </h3>
                                <div class="mt-1 space-y-1">
                                    <a href="index.php?route=admin_characters" class="<?php echo $route === 'admin_characters' || $route === 'review_character' ? 'bg-gray-100 dark:bg-gray-700' : ''; ?> text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 group flex items-center px-2 py-2 text-sm font-medium rounded-md transition-colors duration-200">
                                        <svg class="mr-3 h-6 w-6 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Valider les personnages
                                        <?php if (isset($pending_characters_count) && $pending_characters_count > 0): ?>
                                        <span class="ml-auto inline-block py-0.5 px-2 text-xs font-medium rounded-full bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                            <?php echo $pending_characters_count; ?>
                                        </span>
                                        <?php endif; ?>
                                    </a>
                                    
                                    <a href="index.php?route=manage_users" class="<?php echo $route === 'manage_users' ? 'bg-gray-100 dark:bg-gray-700' : ''; ?> text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 group flex items-center px-2 py-2 text-sm font-medium rounded-md transition-colors duration-200">
                                        <svg class="mr-3 h-6 w-6 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                        </svg>
                                        Gérer les comptes
                                    </a>
                                    
                                    <a href="index.php?route=security_alerts" class="<?php echo $route === 'security_alerts' ? 'bg-gray-100 dark:bg-gray-700' : ''; ?> text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 group flex items-center px-2 py-2 text-sm font-medium rounded-md transition-colors duration-200">
                                        <svg class="mr-3 h-6 w-6 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                        </svg>
                                        Alertes de sécurité
                                    </a>
                                    
                                    <a href="index.php?route=banned_users" class="<?php echo $route === 'banned_users' ? 'bg-gray-100 dark:bg-gray-700' : ''; ?> text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 group flex items-center px-2 py-2 text-sm font-medium rounded-md transition-colors duration-200">
                                        <svg class="mr-3 h-6 w-6 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path>
                                        </svg>
                                        Utilisateurs bannis
                                    </a>
                                </div>
                            </div>
                            <?php endif; ?>
                        </nav>
                    </div>
                </div>
                
                <div class="flex-shrink-0 flex border-t border-gray-200 dark:border-gray-700 p-4 transition-colors duration-200">
                    <div class="flex-shrink-0 group block w-full">
                        <div class="flex items-center">
                            <div class="inline-flex items-center justify-center h-10 w-10 rounded-full bg-blue-600 text-white">
                                <?php 
                                $initials = '';
                                $name_parts = explode(' ', $user['name'] ?? '');
                                foreach ($name_parts as $part) {
                                    $initials .= !empty($part) ? $part[0] : '';
                                }
                                echo htmlspecialchars(strtoupper(substr($initials, 0, 2)));
                                ?>
                            </div>
                            <div class="ml-3 w-full flex justify-between items-center">
                                <div>
                                    <p class="text-base font-medium text-gray-700 dark:text-gray-300">
                                        <?php echo htmlspecialchars($user['name'] ?? ''); ?>
                                    </p>
                                </div>
                                <a href="index.php?route=logout" class="text-sm font-medium text-red-600 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300">
                                    Déconnexion
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Main content -->
        <div class="flex flex-col w-0 flex-1 overflow-hidden">
            <div class="relative z-10 flex-shrink-0 flex h-16 bg-white dark:bg-gray-800 shadow border-b border-gray-200 dark:border-gray-700 transition-colors duration-200">
                <button id="sidebarToggle" type="button" class="px-4 border-r border-gray-200 dark:border-gray-700 text-gray-500 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-500 md:hidden">
                    <span class="sr-only">Ouvrir le menu</span>
                    <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
                
                <div class="flex-1 px-4 flex justify-between">
                    <div class="flex-1 flex items-center">
                        <!-- Titre de la page -->
                        <h1 class="text-xl font-semibold text-gray-900 dark:text-white">
                            <?php echo $title ?? 'Tableau de bord'; ?>
                        </h1>
                    </div>
                    
                    <div class="ml-4 flex items-center md:ml-6">
                        <!-- Dark Mode Toggle -->
                        <div class="mr-6 flex items-center">
                            <div class="relative inline-block w-10 mr-2 align-middle select-none">
                                <input type="checkbox" id="darkModeToggle" class="toggle-checkbox absolute block w-6 h-6 rounded-full bg-white border-4 border-gray-300 appearance-none cursor-pointer transition-all duration-300" />
                                <label for="darkModeToggle" class="toggle-label block overflow-hidden h-6 rounded-full bg-gray-300 cursor-pointer transition-all duration-300"></label>
                            </div>
                            <label for="darkModeToggle" class="text-sm text-gray-700 dark:text-gray-300">
                                <span class="hidden dark:inline-block">☀️</span>
                                <span class="inline-block dark:hidden">🌙</span>
                            </label>
                        </div>
                        
                        <!-- Notifications -->
                        <?php if (!empty($notifications)): ?>
                        <div class="relative">
                            <button id="notificationButton" class="p-1 text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <span class="sr-only">Voir les notifications</span>
                                <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                </svg>
                                <span class="absolute top-0 right-0 block h-2 w-2 rounded-full bg-red-500"></span>
                            </button>
                            
                            <!-- Dropdown menu for notifications -->
                            <div id="notificationDropdown" class="origin-top-right absolute right-0 mt-2 w-80 rounded-md shadow-lg py-1 bg-white dark:bg-gray-800 ring-1 ring-black ring-opacity-5 hidden">
                                <div class="px-4 py-2 border-b border-gray-200 dark:border-gray-700">
                                    <h3 class="text-sm font-medium text-gray-900 dark:text-white">Notifications</h3>
                                </div>
                                <div class="max-h-60 overflow-y-auto">
                                    <?php foreach ($notifications as $notification): ?>
                                    <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700">
                                        <?php if ($notification['type'] === 'info'): ?>
                                        <div class="flex">
                                            <div class="flex-shrink-0">
                                                <svg class="h-5 w-5 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                            </div>
                                            <div class="ml-3 w-0 flex-1">
                                                <p class="text-sm font-medium text-gray-900 dark:text-white"><?php echo htmlspecialchars($notification['message']); ?></p>
                                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400"><?php echo time_elapsed_string($notification['date']); ?></p>
                                            </div>
                                        </div>
                                        <?php else: ?>
                                        <div class="flex">
                                            <div class="flex-shrink-0">
                                                <svg class="h-5 w-5 text-yellow-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                                </svg>
                                            </div>
                                            <div class="ml-3 w-0 flex-1">
                                                <p class="text-sm font-medium text-gray-900 dark:text-white"><?php echo htmlspecialchars($notification['message']); ?></p>
                                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400"><?php echo time_elapsed_string($notification['date']); ?></p>
                                            </div>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                                <div class="px-4 py-2 text-center">
                                    <a href="#" class="text-sm font-medium text-blue-600 hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300">Voir toutes les notifications</a>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <main class="flex-1 relative overflow-y-auto focus:outline-none" tabindex="0">
                <!-- Affichage des messages flash -->
                <?php if (isset($_SESSION['flash'])): ?>
                    <?php 
                    $flash = $_SESSION['flash'];
                    unset($_SESSION['flash']);
                    
                    $bg_class = '';
                    $border_class = '';
                    $text_class = '';
                    
                    switch ($flash['type']) {
                        case 'success':
                            $bg_class = 'bg-green-100 dark:bg-green-900';
                            $border_class = 'border-green-500';
                            $text_class = 'text-green-700 dark:text-green-300';
                            break;
                        case 'error':
                            $bg_class = 'bg-red-100 dark:bg-red-900';
                            $border_class = 'border-red-500';
                            $text_class = 'text-red-700 dark:text-red-300';
                            break;
                        case 'warning':
                            $bg_class = 'bg-yellow-100 dark:bg-yellow-900';
                            $border_class = 'border-yellow-500';
                            $text_class = 'text-yellow-700 dark:text-yellow-300';
                            break;
                        case 'info':
                            $bg_class = 'bg-blue-100 dark:bg-blue-900';
                            $border_class = 'border-blue-500';
                            $text_class = 'text-blue-700 dark:text-blue-300';
                            break;
                    }
                    ?>
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8 py-4">
                        <div class="<?php echo $bg_class; ?> border-l-4 <?php echo $border_class; ?> <?php echo $text_class; ?> p-4 rounded-md fade-in" role="alert">
                            <p><?php echo $flash['message']; ?></p>
                        </div>
                    </div>
                <?php endif; ?>
                
                <div class="py-6">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
                        <!-- Contenu principal -->
                        <?php echo $content; ?>
                    </div>
                </div>
            </main>
        </div>
        
        <!-- Mobile sidebar (hidden by default) -->
        <div id="mobileSidebar" class="fixed inset-0 flex z-40 md:hidden transition-opacity duration-300 opacity-0 pointer-events-none">
            <div id="sidebarBackdrop" class="fixed inset-0 bg-gray-600 bg-opacity-75 transition-opacity duration-300 opacity-0"></div>
            
            <div id="sidebarPanel" class="relative flex-1 flex flex-col max-w-xs w-full bg-white dark:bg-gray-800 transform transition-transform duration-300 ease-in-out -translate-x-full">
                <div class="absolute top-0 right-0 -mr-12 pt-2">
                    <button id="closeSidebar" class="ml-1 flex items-center justify-center h-10 w-10 rounded-full focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white">
                        <span class="sr-only">Fermer le menu</span>
                        <svg class="h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                
                <div class="flex-1 h-0 pt-5 pb-4 overflow-y-auto">
                    <div class="flex-shrink-0 flex items-center px-4">
                        <span class="text-2xl font-bold text-blue-600 dark:text-blue-400">monUCP</span>
                    </div>
                    <nav class="mt-5 px-2 space-y-1">
                        <!-- Menu principal (même contenu que la sidebar desktop) -->
                        <a href="index.php?route=dashboard" class="<?php echo $route === 'dashboard' ? 'bg-gray-100 dark:bg-gray-700' : ''; ?> text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 group flex items-center px-2 py-2 text-base font-medium rounded-md transition-colors duration-200">
                            <svg class="mr-4 h-6 w-6 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                            </svg>
                            Accueil
                        </a>
                        
                        <!-- Autres options de menu -->
                        <!-- ... -->
                    </nav>
                </div>
                
                <div class="flex-shrink-0 flex border-t border-gray-200 dark:border-gray-700 p-4 transition-colors duration-200">
                    <div class="flex-shrink-0 group block w-full">
                        <div class="flex items-center">
                            <div class="inline-flex items-center justify-center h-10 w-10 rounded-full bg-blue-600 text-white">
                                <?php echo htmlspecialchars(strtoupper(substr($initials, 0, 2))); ?>
                            </div>
                            <div class="ml-3 w-full flex justify-between items-center">
                                <div>
                                    <p class="text-base font-medium text-gray-700 dark:text-gray-300">
                                        <?php echo htmlspecialchars($user['name'] ?? ''); ?>
                                    </p>
                                </div>
                                <a href="index.php?route=logout" class="text-sm font-medium text-red-600 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300">
                                    Déconnexion
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php else: ?>
    <!-- Layout pour les visiteurs non connectés -->
    <div class="min-h-screen flex flex-col">
        <!-- En-tête -->
        <header class="bg-white dark:bg-gray-800 shadow-sm transition-colors duration-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center py-4 md:justify-start md:space-x-10">
                    <div class="flex justify-start lg:w-0 lg:flex-1">
                        <a href="index.php" class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                            monUCP
                        </a>
                    </div>
                    
                    <div class="flex items-center">
                        <!-- Dark Mode Toggle -->
                        <div class="mr-6 flex items-center">
                            <div class="relative inline-block w-10 mr-2 align-middle select-none">
                                <input type="checkbox" id="darkModeToggle" class="toggle-checkbox absolute block w-6 h-6 rounded-full bg-white border-4 border-gray-300 appearance-none cursor-pointer transition-all duration-300" />
                                <label for="darkModeToggle" class="toggle-label block overflow-hidden h-6 rounded-full bg-gray-300 cursor-pointer transition-all duration-300"></label>
                            </div>
                            <label for="darkModeToggle" class="text-sm text-gray-700 dark:text-gray-300">
                                <span class="hidden dark:inline-block">☀️</span>
                                <span class="inline-block dark:hidden">🌙</span>
                            </label>
                        </div>
                        
                        <div class="space-x-3">
                            <a href="index.php?route=login" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 transition-colors duration-200">
                                Connexion
                            </a>
                            <a href="index.php?route=register" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors duration-200">
                                Inscription
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </header>
        
        <!-- Contenu principal -->
        <main class="flex-grow">
            <!-- Affichage des messages flash -->
            <?php if (isset($_SESSION['flash'])): ?>
                <?php 
                $flash = $_SESSION['flash'];
                unset($_SESSION['flash']);
                
                $bg_class = '';
                $border_class = '';
                $text_class = '';
                
                switch ($flash['type']) {
                    case 'success':
                        $bg_class = 'bg-green-100 dark:bg-green-900';
                        $border_class = 'border-green-500';
                        $text_class = 'text-green-700 dark:text-green-300';
                        break;
                    case 'error':
                        $bg_class = 'bg-red-100 dark:bg-red-900';
                        $border_class = 'border-red-500';
                        $text_class = 'text-red-700 dark:text-red-300';
                        break;
                    case 'warning':
                        $bg_class = 'bg-yellow-100 dark:bg-yellow-900';
                        $border_class = 'border-yellow-500';
                        $text_class = 'text-yellow-700 dark:text-yellow-300';
                        break;
                    case 'info':
                        $bg_class = 'bg-blue-100 dark:bg-blue-900';
                        $border_class = 'border-blue-500';
                        $text_class = 'text-blue-700 dark:text-blue-300';
                        break;
                }
                ?>
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
                    <div class="<?php echo $bg_class; ?> border-l-4 <?php echo $border_class; ?> <?php echo $text_class; ?> p-4 rounded-md fade-in" role="alert">
                        <p><?php echo $flash['message']; ?></p>
                    </div>
                </div>
            <?php endif; ?>
            
            <div class="py-10">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <!-- Contenu principal -->
                    <?php echo $content; ?>
                </div>
            </div>
        </main>
        
        <!-- Pied de page -->
        <footer class="bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 transition-colors duration-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                <p class="text-center text-gray-500 dark:text-gray-400 text-sm">
                    &copy; <?php echo date('Y'); ?> monUCP. Tous droits réservés.
                </p>
            </div>
        </footer>
    </div>
    <?php endif; ?>
    
    <!-- Toast de notification -->
    <div id="toast" class="fixed bottom-4 right-4 bg-blue-500 text-white px-6 py-3 rounded shadow-lg z-50 hidden">
        <div class="flex items-center">
            <span id="toastMessage"></span>
            <button class="ml-4 text-white" onclick="dismissToast()">×</button>
        </div>
    </div>
    
    <!-- Scripts -->
    <script src="assets/js/darkmode.js"></script>
    <script src="assets/js/toast.js"></script>
    <script src="assets/js/app.js"></script>
    
    <script>
    // Script pour mobile sidebar
    document.addEventListener('DOMContentLoaded', function() {
        const sidebarToggle = document.getElementById('sidebarToggle');
        const mobileSidebar = document.getElementById('mobileSidebar');
        const sidebarBackdrop = document.getElementById('sidebarBackdrop');
        const sidebarPanel = document.getElementById('sidebarPanel');
        const closeSidebar = document.getElementById('closeSidebar');
        
        function openSidebar() {
            mobileSidebar.classList.remove('opacity-0', 'pointer-events-none');
            sidebarBackdrop.classList.remove('opacity-0');
            sidebarPanel.classList.remove('-translate-x-full');
        }
        
        function closeSidebarMenu() {
            sidebarPanel.classList.add('-translate-x-full');
            sidebarBackdrop.classList.add('opacity-0');
            
            // Attendre la fin de l'animation avant de masquer complètement
            setTimeout(() => {
                mobileSidebar.classList.add('opacity-0', 'pointer-events-none');
            }, 300);
        }
        
        if (sidebarToggle) {
            sidebarToggle.addEventListener('click', openSidebar);
        }
        
        if (closeSidebar) {
            closeSidebar.addEventListener('click', closeSidebarMenu);
        }
        
        if (sidebarBackdrop) {
            sidebarBackdrop.addEventListener('click', closeSidebarMenu);
        }
        
        // Gestion des notifications
        const notificationButton = document.getElementById('notificationButton');
        const notificationDropdown = document.getElementById('notificationDropdown');
        
        if (notificationButton && notificationDropdown) {
            notificationButton.addEventListener('click', function(e) {
                e.stopPropagation();
                notificationDropdown.classList.toggle('hidden');
            });
            
            // Fermer le dropdown quand on clique ailleurs
            document.addEventListener('click', function(e) {
                if (!notificationDropdown.contains(e.target) && e.target !== notificationButton) {
                    notificationDropdown.classList.add('hidden');
                }
            });
        }
        
        // Dark mode
        const darkModeToggle = document.getElementById('darkModeToggle');
        const html = document.documentElement;
        
        // Fonction pour vérifier si le mode sombre est activé
        function isDarkMode() {
            return localStorage.getItem('darkMode') === 'true' || 
                  (localStorage.getItem('darkMode') === null && 
                   window.matchMedia('(prefers-color-scheme: dark)').matches);
        }
        
        // Appliquer le thème
        function applyTheme() {
            if (isDarkMode()) {
                html.classList.add('dark');
                darkModeToggle.checked = true;
            } else {
                html.classList.remove('dark');
                darkModeToggle.checked = false;
            }
        }
        
        // Basculer le mode
        function toggleDarkMode() {
            if (isDarkMode()) {
                localStorage.setItem('darkMode', 'false');
            } else {
                localStorage.setItem('darkMode', 'true');
            }
            applyTheme();
        }
        
        // Appliquer le thème au chargement
        applyTheme();
        
        // Écouter les changements de mode
        if (darkModeToggle) {
            darkModeToggle.addEventListener('change', toggleDarkMode);
        }
    });
    </script>
</body>
</html>