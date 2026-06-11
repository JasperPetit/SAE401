<?php 
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 1. On remonte d'un dossier pour trouver l'autoload à la racine
require_once __DIR__ . '/../autoload.php';

// 2. On remonte d'un dossier pour trouver le dossier config
$pdo = require_once __DIR__ . '/../config/DBconnect.php';

$action = $_GET['action'] ?? 'accueil';

// 3. On remonte d'un dossier pour trouver auth.php (s'il est bien à la racine)
require_once __DIR__ . '/../auth.php';

$routes = [
    'accueil' => ['AccueilController','AfficherAccueil'],

    'login'            => ['AuthController', 'afficherLogin'], 
    'connexion'        => ['AuthController', 'connecter'],
    'pageTableauDeBord' => ['ExpeditionController', 'afficherTableauDeBord'],
    
    'suivi'            => ['ExpeditionController', 'afficherSuiviColis'],
    'nouveau'          => ['ExpeditionController', 'afficherNouvelEnvoi'],
    'recherche_rapide' => ['ExpeditionController', 'rechercherRapide'],
    'imprimer'         => ['ExpeditionController', 'imprimerEtiquette'],
    'deconnexion'      => ['ExpeditionController', 'deconnecter'],


    //Service Financier
    'pageServiceFinancierDevis' => ['FinanceController','afficherListeFinance'],
    'validerDevis'              => ['FinanceController','validerDevis'],
    'refuserDevis'              => ['FinanceController','refuserDevis'],

    //actions sur les commandes
    'afficherCommande'        => ['CommandeController','afficherCommande'],
    'pageMesCommandesAdmin'   => ['CommandeController','afficherCommandeAdmin'],
    'afficherCommandeAdmin'   => ['CommandeController','afficherCommandeAdmin'],
    'pageMesCommandesPostale' => ['CommandeController','afficherCommandePostale'],
    'SupprimerCommande'       => ['CommandeController','SupprimerCommande'],
    'AjouterCommande'         => ['CommandeController','ajouterCommande'],
    'ModifierCommande'        => ['CommandeController','ModifierCommande'],

    //devis
    'ajouter_devis'           => ['DevisController','AjouterDevis'],
    'formulaireDevis'         => ['DevisController','AfficherFormulaire'],
    'pageInfosDevis'          => ['DevisController','AfficherDevis'],
    'SupprimerDevis'          => ['DevisController','SupprimerDevis'],
    'ModifierDevis'           => ['DevisController','ModifierDevis'],
    'PageInfosDevisDemandeur' => ['DevisController','AfficherDevisDepartement'],

    //colis
    'afficherColis' => ['ColisController', 'afficherColis'],
    'ModifierColis' => ['ColisController', 'ModifierColis'],
    'valider_livraison' => ['ColisController', 'validerLivraison'],
    'afficherColisPostale' => ['ColisController','afficherColisPostale'],

    //fournisseur
    'afficherFournisseur'  => ['FournisseurController','AfficherFournisseur'],
    'pageFournisseurAdmin' => ['FournisseurController','AfficherFournisseurAdmin'],
    'ajouterFournisseur'   => ['FournisseurController','ajouterFournisseur'],
    'ModifierFournisseur'  => ['FournisseurController','ModifierFournisseur'],
    'SupprimerFournisseur' => ['FournisseurController','SupprimerFournisseur'],

    //admin
    'pageAdmin'              => ['UtilisateurController','AfficherAdmin'],
    'pageVoirUtilisateurs'   => ['UtilisateurController','AfficherListe'],
    'pageAjouterUtilisateur' => ['UtilisateurController','AjouterUtilisateur'],
    'SupprimerUtilisateur'   => ['UtilisateurController', 'SupprimerUtilisateur'],
];


if (array_key_exists($action, $routes)) {
    
    $simpleControllerName = $routes[$action][0];
    $methodName           = $routes[$action][1];

    // On reconstitue le Namespace complet : App\Controllers\NomController
    $controllerClass = "App\\Controllers\\" . $simpleControllerName;

    // L'autoload va se déclencher automatiquement ici grâce au class_exists !
    if (class_exists($controllerClass)) {
        
        // Instanciation
        $controller = new $controllerClass($pdo);
        
        // Appel de la méthode
        if (method_exists($controller, $methodName)) {
            $controller->$methodName();
        } else {
            die("Erreur : La méthode $methodName n'existe pas.");
        }

    } else {
        die("Erreur : Le contrôleur $controllerClass est introuvable (Vérifie le namespace ou le nom du fichier).");
    }

} else {
    // Gestion 404 simple
    echo "Action demandée : " . htmlspecialchars($action) . "<br>";
    echo "<h1>Page introuvable</h1>";
}
?>