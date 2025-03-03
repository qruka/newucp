<div class="container max-w-md mx-auto slide-up">
    <div class="text-center mb-8">
        <h1 class="text-4xl font-bold text-gray-800 dark:text-white mb-2">Connexion</h1>
        <p class="text-gray-600 dark:text-gray-400">Bienvenue sur notre système d'authentification</p>
    </div>
    
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
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>?route=login" data-validate>
            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
            
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
                <div class="flex justify-between items-center mb-2">
                    <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold" for="password">
                        Mot de passe
                    </label>
                    <a href="index.php?route=auth&action=forgotPassword" class="text-xs text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                        Mot de passe oublié?
                    </a>
                </div>
                <input 
                    class="shadow-sm appearance-none border rounded-lg w-full py-3 px-4 text-gray-700 dark:text-gray-300 dark:bg-gray-700 dark:border-gray-600 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 input-focus-effect" 
                    id="password" 
                    type="password" 
                    name="password" 
                    placeholder="Votre mot de passe"
                    required
                >
            </div>
            
            <div class="mb-6">
                <label class="flex items-center">
                    <input type="checkbox" name="remember" class="form-checkbox h-5 w-5 text-blue-600 transition duration-150 ease-in-out">
                    <span class="ml-2 text-gray-700 dark:text-gray-300">Se souvenir de moi</span>
                </label>
            </div>
            
            <div class="flex items-center justify-between mb-6">
                <button class="gradient-background hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg focus:outline-none focus:shadow-outline transition-all duration-200 hover-scale" type="submit">
                    Se connecter
                </button>
                <a class="inline-block align-baseline font-bold text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 transition-colors duration-200" href="index.php?route=register">
                    Créer un compte
                </a>
            </div>
        </form>
    </div>
</div>