<div class="container max-w-md mx-auto slide-up">
    <div class="text-center mb-8">
        <h1 class="text-4xl font-bold text-gray-800 dark:text-white mb-2">Inscription</h1>
        <p class="text-gray-600 dark:text-gray-400">Créez votre compte pour accéder au système</p>
    </div>
    
    <?php if (!empty($success)): ?>
        <div class="bg-green-100 dark:bg-green-900 border-l-4 border-green-500 text-green-700 dark:text-green-300 p-4 mb-6 rounded fade-in" role="alert">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-500" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p><?php echo $success; ?></p>
                </div>
            </div>
        </div>
    <?php endif; ?>
    
    <?php if (!empty($errors)): ?>
        <div class="bg-red-100 dark:bg-red-900 border-l-4 border-red-500 text-red-700 dark:text-red-300 p-4 mb-6 rounded fade-in" role="alert">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-500" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <?php foreach ($errors as $error): ?>
                        <p><?php echo $error; ?></p>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>
    
    <div class="bg-white dark:bg-gray-800 shadow-xl rounded-lg p-8 border border-gray-200 dark:border-gray-700 fade-in">
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>?route=register" data-validate>
            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
            
            <div class="mb-6">
                <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2" for="name">
                    Nom complet
                </label>
                <input 
                    class="shadow-sm appearance-none border rounded-lg w-full py-3 px-4 text-gray-700 dark:text-gray-300 dark:bg-gray-700 dark:border-gray-600 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 input-focus-effect" 
                    id="name" 
                    type="text" 
                    name="name" 
                    placeholder="Votre nom complet"
                    value="<?php echo htmlspecialchars($name); ?>"
                    required
                    pattern="^[a-zA-Z-' ]*$"
                    data-error-pattern="Seuls les lettres et les espaces sont autorisés"
                >
            </div>
            
            <div class="mb-6">
                <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2" for="email">
                    Email
                </label>
                <input 
                    class="shadow-sm appearance-none border rounded-lg w-full py-3 px-4 text-gray-700 dark:text-gray-300 dark:bg-gray-700 dark:border-gray-600 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 input-focus-effect" 
                    id="email" 
                    type="email" 
                    name="email" 
                    placeholder="Votre email"
                    value="<?php echo htmlspecialchars($email); ?>"
                    required
                >
            </div>
            
            <div class="mb-6">
                <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2" for="password">
                    Mot de passe
                </label>
                <input 
                    class="shadow-sm appearance-none border rounded-lg w-full py-3 px-4 text-gray-700 dark:text-gray-300 dark:bg-gray-700 dark:border-gray-600 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 input-focus-effect" 
                    id="password" 
                    type="password" 
                    name="password" 
                    placeholder="Votre mot de passe"
                    required
                    minlength="6"
                >
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Le mot de passe doit contenir au moins 6 caractères.</p>
            </div>
            
            <div class="mb-6">
                <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2" for="confirm_password">
                    Confirmer le mot de passe
                </label>
                <input 
                    class="shadow-sm appearance-none border rounded-lg w-full py-3 px-4 text-gray-700 dark:text-gray-300 dark:bg-gray-700 dark:border-gray-600 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 input-focus-effect" 
                    id="confirm_password" 
                    type="password" 
                    name="confirm_password" 
                    placeholder="Confirmez votre mot de passe"
                    required
                    data-match="password"
                    data-error-match="Les mots de passe ne correspondent pas"
                >
            </div>
            
            <div class="flex items-center justify-between mb-6">
                <button class="gradient-background hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg focus:outline-none focus:shadow-outline transition-all duration-200 hover-scale" type="submit">
                    S'inscrire
                </button>
                <a class="inline-block align-baseline font-bold text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 transition-colors duration-200" href="index.php?route=login">
                    Déjà inscrit ?
                </a>
            </div>
        </form>
    </div>
</div>