
<?php
// autoload.php

spl_autoload_register(function ($className) {
    // 1. On définit le préfixe de notre projet (Namespace racine)
    $prefix = 'App\\';

    // 2. On définit le dossier de base (la racine du site : __DIR__)
    $base_dir = __DIR__ . '/';

    // 3. Vérifie si la classe utilise bien notre préfixe "App\"
    // Si la classe demandée est "PDO" ou "Exception", on ne fait rien.
    $len = strlen($prefix);
    if (strncmp($prefix, $className, $len) !== 0) {
        return;
    }

    // 4. On récupère le nom de la classe sans le préfixe "App\"
    // Exemple : "App\Controllers\FournisseurController" devient "Controllers\FournisseurController"
    $relative_class = substr($className, $len);

    // 5. On prépare le chemin du fichier
    // On remplace les antislashes du namespace (\) par des slashes de dossier (/)
    $path = str_replace('\\', '/', $relative_class);
    
    // ASTUCE : Gestion des minuscules pour les dossiers
    // Si tes dossiers s'appellent "controllers" (minuscule) mais que le namespace est "Controllers" (Majuscule)
    // On force la première partie du chemin en minuscule.
    $pathParts = explode('/', $path);
    if (count($pathParts) > 0) {
        $pathParts[0] = strtolower($pathParts[0]); 
    }
    $finalPath = implode('/', $pathParts);

    // 6. On construit le chemin complet
    // Ex: C:/wamp/www/monProjet/controllers/FournisseurController.php
    $file = $base_dir . $finalPath . '.php';

    // 7. Si le fichier existe, on le charge
    if (file_exists($file)) {
        require_once $file;
    }
});