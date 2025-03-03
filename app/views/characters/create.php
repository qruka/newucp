<div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Création de personnage</h2>
    <p class="mt-1 text-gray-600 dark:text-gray-400">Créez un nouveau personnage pour votre compte</p>
</div>

<div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden transition-colors duration-200 slide-in">
    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
        <h2 class="text-xl font-medium text-gray-800 dark:text-white">Créer un nouveau personnage</h2>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            Veuillez remplir tous les champs ci-dessous. Votre personnage sera examiné par un administrateur avant d'être validé.
        </p>
    </div>
    
    <div class="p-6">
        <?php if (isset($errors) && !empty($errors)): ?>
        <div class="bg-red-100 dark:bg-red-900 border-l-4 border-red-500 text-red-700 dark:text-red-300 p-4 mb-6 rounded-md fade-in" role="alert">
            <p class="font-bold">Erreurs :</p>
            <ul class="mt-1 ml-4 list-disc list-inside">
                <?php foreach ($errors as $field => $error): ?>
                <li><?php echo is_string($field) ? $error : $field; ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>
        
        <form method="POST" action="index.php?route=create_character&action=store" data-validate>
            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label for="first_last_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Prénom et Nom <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="first_last_name" 
                        name="first_last_name" 
                        class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 dark:border-gray-700 dark:bg-gray-700 dark:text-white rounded-md py-2 px-3"
                        value="<?php echo isset($input['first_last_name']) ? htmlspecialchars($input['first_last_name']) : ''; ?>"
                        placeholder="Ex: Jean Dupont"
                        required
                    >
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                        Le nom complet de votre personnage. Entre 3 et 100 caractères.
                    </p>
                </div>
                
                <div>
                    <label for="age" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Âge <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="number" 
                        id="age" 
                        name="age" 
                        min="1" 
                        max="120" 
                        class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 dark:border-gray-700 dark:bg-gray-700 dark:text-white rounded-md py-2 px-3"
                        value="<?php echo isset($input['age']) ? htmlspecialchars($input['age']) : ''; ?>"
                        placeholder="Ex: 30"
                        required
                    >
                </div>
                
                <div>
                    <label for="ethnicity" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Ethnie <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="ethnicity" 
                        name="ethnicity" 
                        class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 dark:border-gray-700 dark:bg-gray-700 dark:text-white rounded-md py-2 px-3"
                        value="<?php echo isset($input['ethnicity']) ? htmlspecialchars($input['ethnicity']) : ''; ?>"
                        placeholder="Ex: Caucasien, Afro-américain, Asiatique, etc."
                        required
                    >
                </div>
                
                <div class="md:col-span-2">
                    <label for="background" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Background / Histoire <span class="text-red-500">*</span>
                    </label>
                    <textarea 
                        id="background" 
                        name="background" 
                        rows="10" 
                        class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 dark:border-gray-700 dark:bg-gray-700 dark:text-white rounded-md py-2 px-3"
                        placeholder="Décrivez l'histoire et le contexte de votre personnage..."
                        required
                    ><?php echo isset($input['background']) ? htmlspecialchars($input['background']) : ''; ?></textarea>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                        Soyez détaillé et précis. Cela aidera l'administrateur à comprendre votre personnage. Minimum 100 caractères.
                    </p>
                </div>
            </div>
            
            <div class="mt-6 flex justify-end space-x-3">
                <a href="index.php?route=characters" class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                    Annuler
                </a>
                <button 
                    type="submit" 
                    class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200 hover-scale"
                >
                    Soumettre le personnage
                </button>
            </div>
        </form>
    </div>
</div>

<div class="mt-8 bg-blue-50 dark:bg-blue-900/30 rounded-lg p-6 border border-blue-200 dark:border-blue-800 slide-in delay-100">
    <h3 class="text-lg font-medium text-blue-800 dark:text-blue-300 mb-4">Conseils pour la création de personnage</h3>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <h4 class="text-md font-medium text-blue-700 dark:text-blue-400 mb-2">Background</h4>
            <ul class="list-disc list-inside text-blue-600 dark:text-blue-300 space-y-2">
                <li>Incluez l'enfance et l'éducation de votre personnage</li>
                <li>Mentionnez les événements importants qui ont façonné sa personnalité</li>
                <li>Décrivez sa famille et ses relations personnelles</li>
                <li>Expliquez ses motivations et aspirations</li>
            </ul>
        </div>
        
        <div>
            <h4 class="text-md font-medium text-blue-700 dark:text-blue-400 mb-2">Conseils généraux</h4>
            <ul class="list-disc list-inside text-blue-600 dark:text-blue-300 space-y-2">
                <li>Créez un personnage réaliste avec des forces et des faiblesses</li>
                <li>Évitez les clichés et les stéréotypes</li>
                <li>Assurez-vous que votre personnage correspond au contexte du jeu</li>
                <li>N'hésitez pas à inclure des détails qui rendront votre personnage unique</li>
            </ul>
        </div>
    </div>
</div>