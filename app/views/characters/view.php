<div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden transition-colors duration-200 slide-in mb-8">
    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
        <h2 class="text-xl font-medium text-gray-800 dark:text-white">
            <?php echo htmlspecialchars($character['first_last_name']); ?>
        </h2>
        
        <?php 
        $status_class = '';
        $status_text = '';
        
        switch ($character['status']) {
            case 'pending':
                $status_class = 'status-pending';
                $status_text = 'En attente de validation';
                break;
            case 'approved':
                $status_class = 'status-approved';
                $status_text = 'Personnage approuvé';
                break;
            case 'rejected':
                $status_class = 'status-rejected';
                $status_text = 'Personnage rejeté';
                break;
        }
        ?>
        
        <span class="status-badge <?php echo $status_class; ?>">
            <?php echo $status_text; ?>
        </span>
    </div>
    
    <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="md:col-span-1">
                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                    <h3 class="text-lg font-medium text-gray-800 dark:text-white mb-4">Informations</h3>
                    
                    <div class="mb-3">
                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400 block">Nom complet</span>
                        <span class="text-gray-800 dark:text-white"><?php echo htmlspecialchars($character['first_last_name']); ?></span>
                    </div>
                    
                    <div class="mb-3">
                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400 block">Âge</span>
                        <span class="text-gray-800 dark:text-white"><?php echo htmlspecialchars($character['age']); ?> ans</span>
                    </div>
                    
                    <div class="mb-3">
                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400 block">Ethnie</span>
                        <span class="text-gray-800 dark:text-white"><?php echo htmlspecialchars($character['ethnicity']); ?></span>
                    </div>
                    
                    <div class="mb-3">
                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400 block">Créé par</span>
                        <span class="text-gray-800 dark:text-white"><?php echo htmlspecialchars($character['creator_name']); ?></span>
                    </div>
                    
                    <div class="mb-3">
                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400 block">Date de création</span>
                        <span class="text-gray-800 dark:text-white"><?php echo date('d/m/Y H:i', strtotime($character['created_at'])); ?></span>
                    </div>
                    
                    <?php if ($character['reviewer_id']): ?>
                    <div class="mb-3">
                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400 block">Évalué par</span>
                        <span class="text-gray-800 dark:text-white"><?php echo htmlspecialchars($character['reviewer_name']); ?></span>
                    </div>
                    
                    <div class="mb-3">
                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400 block">Date d'évaluation</span>
                        <span class="text-gray-800 dark:text-white"><?php echo date('d/m/Y H:i', strtotime($character['updated_at'])); ?></span>
                    </div>
                    <?php endif; ?>

                    <?php if ($character['status'] !== 'approved'): ?>
                    <div class="mt-6 flex space-x-3">
                        <a href="index.php?route=characters&action=edit&id=<?php echo $character['id']; ?>" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Modifier
                        </a>
                        
                        <form action="index.php?route=characters&action=delete" method="post" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce personnage ? Cette action est irréversible.');">
                            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                            <input type="hidden" name="character_id" value="<?php echo $character['id']; ?>">
                            <button type="submit" class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Supprimer
                            </button>
                        </form>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="md:col-span-2">
                <div>
                    <h3 class="text-lg font-medium text-gray-800 dark:text-white mb-4">Background / Histoire</h3>
                    <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                        <p class="text-gray-800 dark:text-white whitespace-pre-line"><?php echo htmlspecialchars($character['background']); ?></p>
                    </div>
                </div>
                
                <?php if ($character['status'] === 'rejected' && !empty($character['admin_comment'])): ?>
                <div class="mt-6">
                    <h3 class="text-lg font-medium text-gray-800 dark:text-white mb-4">Commentaire de l'administrateur</h3>
                    <div class="bg-red-50 dark:bg-red-900/30 p-4 rounded-lg border border-red-200 dark:border-red-800">
                        <p class="text-red-700 dark:text-red-300"><?php echo htmlspecialchars($character['admin_comment']); ?></p>
                    </div>
                </div>
                <?php endif; ?>
                
                <?php if ($character['status'] === 'approved'): ?>
                <div class="mt-6">
                    <div class="bg-green-50 dark:bg-green-900/30 p-4 rounded-lg border border-green-200 dark:border-green-800">
                        <h3 class="text-green-800 dark:text-green-300 font-medium">Personnage approuvé</h3>
                        <p class="text-green-700 dark:text-green-400 mt-1">Ce personnage a été validé par un administrateur et peut être utilisé.</p>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php if ($character['status'] === 'approved'): ?>
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden transition-colors duration-200 slide-in">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-medium text-gray-800 dark:text-white">Services disponibles</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <a href="#" class="bg-blue-50 dark:bg-blue-900/30 p-4 rounded-lg border border-blue-200 dark:border-blue-800 transition-all duration-200 hover:shadow-md">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-blue-500 rounded-md p-2">
                            <svg class="h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h4 class="text-base font-medium text-gray-900 dark:text-white">Banque</h4>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Gérer vos comptes bancaires</p>
                        </div>
                    </div>
                </a>
                
                <a href="#" class="bg-indigo-50 dark:bg-indigo-900/30 p-4 rounded-lg border border-indigo-200 dark:border-indigo-800 transition-all duration-200 hover:shadow-md">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-indigo-500 rounded-md p-2">
                            <svg class="h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h4 class="text-base font-medium text-gray-900 dark:text-white">Immobilier</h4>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Acheter ou louer des biens</p>
                        </div>
                    </div>
                </a>
                
                <a href="#" class="bg-green-50 dark:bg-green-900/30 p-4 rounded-lg border border-green-200 dark:border-green-800 transition-all duration-200 hover:shadow-md">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-green-500 rounded-md p-2">
                            <svg class="h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h4 class="text-base font-medium text-gray-900 dark:text-white">Investissements</h4>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Gérer votre portefeuille</p>
                        </div>
                    </div>
                </a>
                
                <a href="#" class="bg-purple-50 dark:bg-purple-900/30 p-4 rounded-lg border border-purple-200 dark:border-purple-800 transition-all duration-200 hover:shadow-md">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-purple-500 rounded-md p-2">
                            <svg class="h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h4 class="text-base font-medium text-gray-900 dark:text-white">Inventaire</h4>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Gérer vos objets</p>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>
    
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden transition-colors duration-200 slide-in">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-medium text-gray-800 dark:text-white">Statistiques</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-2 gap-4">
                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                    <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Solde bancaire</h4>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">$10,250</p>
                    <div class="flex items-center mt-1">
                        <span class="text-green-500 text-sm font-medium flex items-center">
                            <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
                            </svg>
                            3.2%
                        </span>
                        <span class="text-gray-500 dark:text-gray-400 text-sm ml-2">depuis le mois dernier</span>
                    </div>
                </div>
                
                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                    <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Propriétés</h4>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">2</p>
                    <div class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Valeur totale: $450,000
                    </div>
                </div>
                
                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                    <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Réputation</h4>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">78/100</p>
                    <div class="w-full bg-gray-200 dark:bg-gray-600 rounded-full h-2.5 mt-2">
                        <div class="bg-blue-600 h-2.5 rounded-full" style="width: 78%"></div>
                    </div>
                </div>
                
                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                    <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Niveau</h4>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">12</p>
                    <div class="flex items-center mt-1">
                        <span class="text-gray-500 dark:text-gray-400 text-sm">Prochain niveau: </span>
                        <div class="ml-2 flex-1 h-2 bg-gray-200 dark:bg-gray-600 rounded-full">
                            <div class="bg-green-500 h-2 rounded-full" style="width: 65%"></div>
                        </div>
                        <span class="ml-2 text-gray-500 dark:text-gray-400 text-sm">65%</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<div class="flex justify-between items-center">
    <a href="index.php?route=characters" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
        <svg class="mr-2 -ml-1 h-5 w-5 text-gray-500 dark:text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
        </svg>
        Retour à la liste
    </a>
</div>