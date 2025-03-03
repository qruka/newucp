<div class="mb-6 flex justify-between items-center">
    <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Vos personnages</h2>
    <a href="index.php?route=create_character" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
        <svg class="mr-2 -ml-1 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
        </svg>
        Créer un personnage
    </a>
</div>

<?php if (empty($characters)): ?>
<div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 text-center transition-colors duration-200 slide-in">
    <svg class="w-16 h-16 mx-auto text-gray-400 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
    </svg>
    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Aucun personnage</h3>
    <p class="mt-1 text-gray-500 dark:text-gray-400">Vous n'avez pas encore créé de personnage.</p>
    <div class="mt-6">
        <a href="index.php?route=create_character" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
            Créer mon premier personnage
        </a>
    </div>
</div>
<?php else: ?>
    
<!-- Filtres de statut -->
<div class="mb-6 flex flex-wrap space-x-2">
    <button type="button" class="mb-2 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200" data-filter="all">
        Tous
    </button>
    <button type="button" class="mb-2 inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200" data-filter="approved">
        Approuvés
    </button>
    <button type="button" class="mb-2 inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200" data-filter="pending">
        En attente
    </button>
    <button type="button" class="mb-2 inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200" data-filter="rejected">
        Rejetés
    </button>
</div>

<!-- Liste des personnages -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
    <?php foreach ($characters as $index => $character): ?>
    <div class="character-card bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden transition-colors duration-200 hover-card slide-in" 
         data-status="<?php echo $character['status']; ?>" 
         style="animation-delay: <?php echo $index * 0.1; ?>s">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white truncate"><?php echo htmlspecialchars($character['first_last_name']); ?></h3>
            
            <?php 
            $status_class = '';
            $status_text = '';
            
            switch ($character['status']) {
                case 'pending':
                    $status_class = 'status-pending';
                    $status_text = 'En attente';
                    break;
                case 'approved':
                    $status_class = 'status-approved';
                    $status_text = 'Approuvé';
                    break;
                case 'rejected':
                    $status_class = 'status-rejected';
                    $status_text = 'Rejeté';
                    break;
            }
            ?>
            
            <span class="status-badge <?php echo $status_class; ?>">
                <?php echo $status_text; ?>
            </span>
        </div>
        
        <div class="px-6 py-4">
            <div class="mb-2">
                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Âge:</span>
                <span class="ml-2 text-gray-700 dark:text-gray-300"><?php echo htmlspecialchars($character['age']); ?> ans</span>
            </div>
            <div class="mb-2">
                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Ethnie:</span>
                <span class="ml-2 text-gray-700 dark:text-gray-300"><?php echo htmlspecialchars($character['ethnicity']); ?></span>
            </div>
            <div class="mt-4">
                <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Background:</h4>
                <p class="mt-1 text-gray-700 dark:text-gray-300 line-clamp-3"><?php echo htmlspecialchars(substr($character['background'], 0, 150)) . '...'; ?></p>
            </div>
            
            <?php if ($character['status'] === 'rejected' && !empty($character['admin_comment'])): ?>
            <div class="mt-4 p-3 bg-red-50 dark:bg-red-900/30 rounded border border-red-200 dark:border-red-800">
                <h4 class="text-sm font-medium text-red-800 dark:text-red-300">Commentaire de l'administrateur:</h4>
                <p class="mt-1 text-red-700 dark:text-red-400 text-sm line-clamp-2"><?php echo htmlspecialchars($character['admin_comment']); ?></p>
            </div>
            <?php endif; ?>
        </div>
        
        <div class="px-6 py-3 bg-gray-50 dark:bg-gray-700 flex justify-between items-center">
            <a href="index.php?route=view_character&id=<?php echo $character['id']; ?>" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 font-medium text-sm">
                Voir les détails
            </a>
            
            <?php if ($character['status'] === 'pending' || $character['status'] === 'rejected'): ?>
            <a href="index.php?route=edit_character&id=<?php echo $character['id']; ?>" class="text-yellow-600 hover:text-yellow-800 dark:text-yellow-400 dark:hover:text-yellow-300 font-medium text-sm">
                Modifier
            </a>
            <?php endif; ?>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Character filtering
    const filterButtons = document.querySelectorAll('[data-filter]');
    const characterCards = document.querySelectorAll('.character-card');
    
    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Update active button styles
            filterButtons.forEach(btn => {
                if (btn === button) {
                    btn.classList.remove('bg-white', 'dark:bg-gray-700', 'text-gray-700', 'dark:text-gray-300', 'border-gray-300');
                    btn.classList.add('bg-blue-600', 'text-white', 'border-transparent');
                } else {
                    btn.classList.remove('bg-blue-600', 'text-white', 'border-transparent');
                    btn.classList.add('bg-white', 'dark:bg-gray-700', 'text-gray-700', 'dark:text-gray-300', 'border-gray-300');
                }
            });
            
            const filter = this.dataset.filter;
            
            characterCards.forEach(card => {
                if (filter === 'all' || card.dataset.status === filter) {
                    card.classList.remove('hidden');
                } else {
                    card.classList.add('hidden');
                }
            });
        });
    });
});
</script>