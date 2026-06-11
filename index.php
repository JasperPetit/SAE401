<?php
/**
 * ==========================================================================
 * ROUTEUR PRINCIPAL GLOBAL - SAE 401 COLIS
 * ==========================================================================
 */

// 1. Démarrage sécurisé de la session globale
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

define('ROOT', __DIR__);

// 2. On définit les chemins vers les dossiers vitaux en utilisant ROOT
define('APP', ROOT . '/app');
define('VIEWS', ROOT . '/app/views');
define('MODELS', ROOT . '/app/Models');
define('CONTROLLERS', ROOT . '/app/Controllers');

// --- SUITE DE VOTRE CODE ---
// 2. Chargement de l'Autoloader pour instancier les classes automatiquement

require_once __DIR__ . '/app/autoload.php';

// 3. Connexion centralisée à la Base de Données (PDO)
try {
    $db = new PDO('sqlite:' . __DIR__ . '/data/database.db');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("<div style='padding:20px; background:#fee2e2; color:#991b1b; font-family:sans-serif;'>
            <h3>⚠️ Erreur de connexion à la base de données</h3>
            <p>" . htmlspecialchars($e->getMessage()) . "</p>
         </div>");
}

// 4. Interception de l'action demandée dans l'URL (Login par défaut)
$action = $_GET['action'] ?? 'login';

// 5. Restriction d'accès : Si l'utilisateur n'est pas connecté, on le force à rester sur le login
$actions_publiques = ['login', 'connexion'];
if (!isset($_SESSION['utilisateur_id']) && !in_array($action, $actions_publiques)) {
    header('Location: index.php?action=login');
    exit();
}

// 6. --- ROUTAGE DYNAMIQUE VERS LES CONTRÔLEURS ---
switch ($action) {

    // === AUTHENTIFICATION ===
    case 'login':
        $controller = new \App\Controllers\AuthController($db);
        $controller->afficherLogin();
        break;

    case 'connexion':
        $controller = new \App\Controllers\AuthController($db);
        $controller->connecter();
        break;

    case 'deconnexion':
        $controller = new \App\Controllers\AuthController($db);
        $controller->deconnecter();
        break;

    // === ACCUEIL / TABLEAU DE BORD ===
    case 'accueil':
        // Redirection intelligente selon le profil pour charger les bonnes fonctions d'accueil
        if (isset($_SESSION['role']) && $_SESSION['role'] === 'Utilisateur') {
            $controller = new \App\Controllers\DevisController($db);
            $controller->afficherDevisDepartement();
        } elseif (isset($_SESSION['role']) && $_SESSION['role'] === 'Service_Postal') {
            $controller = new \App\Controllers\AccueilController($db);
            $controller->afficherAccueil();
        } else {
            $controller = new \App\Controllers\AccueilController($db);
            $controller->afficherAccueil();
        }
        break;

    // === GESTION DES DEVIS ===
    case 'pageInfosDevis':
        $controller = new \App\Controllers\DevisController($db);
        if (isset($_SESSION['role']) && $_SESSION['role'] === 'Administrateur') {
            $controller->afficherDevis();
        } else {
            $controller->afficherDevisDepartement();
        }
        break;

    case 'formulaireDevis':
        $controller = new \App\Controllers\DevisController($db);
        $controller->afficherFormulaire();
        break;

    case 'ajouter_devis':
        $controller = new \App\Controllers\DevisController($db);
        $controller->ajouterDevis();
        break;

    case 'validerDevis':
        $controller = new \App\Controllers\DevisController($db);
        $controller->validerDevis();
        break;

    case 'refuserDevis':
        $controller = new \App\Controllers\DevisController($db);
        $controller->refuserDevis();
        break;

    case 'pageServiceFinancierDevis':
        $controller = new \App\Controllers\DevisController($db);
        $controller->afficherDevis(); // Géré par le DevisController
        break;

    // === GESTION DES COMMANDES ===
    case 'afficherCommande':
        $controller = new \App\Controllers\CommandeController($db);
        // Utilise la méthode native de consultation de ton CommandeController
        if (method_exists($controller, 'afficherCommandes')) {
            $controller->afficherCommandes();
        }
        break;

    case 'AjouterCommande':
        $controller = new \App\Controllers\CommandeController($db);
        $controller->ajouterCommande();
        break;

    case 'ModifierCommande':
        $controller = new \App\Controllers\CommandeController($db);
        $controller->modifierCommande();
        break;

    // === GESTION DES COLIS ===
    case 'afficherColis':
    case 'suivi':
        $controller = new \App\Controllers\ColisController($db);
        $controller->afficherColis();
        break;

    case 'modifierColis':
        $controller = new \App\Controllers\ColisController($db);
        $controller->modifierColis();
        break;

    case 'validerLivraison':
        $controller = new \App\Controllers\ColisController($db);
        $controller->validerLivraison();
        break;

    case 'nouveau':
        // Gère la réimpression ou l'édition d'étiquettes
        require_once VIEWS . '/pageNouvelEnvoi.php';
        break;

    // === GESTION DES FOURNISSEURS ===
    case 'afficherFournisseur':
        $controller = new \App\Controllers\FournisseurController($db);
        $controller->afficherFournisseur();
        break;

    case 'ajouterFournisseur':
        $controller = new \App\Controllers\FournisseurController($db);
        $controller->ajouterFournisseur();
        break;

    case 'ModifierFournisseur':
        $controller = new \App\Controllers\FournisseurController($db);
        $controller->modifierFournisseur();
        break;

    case 'SupprimerFournisseur':
        $controller = new \App\Controllers\FournisseurController($db);
        $controller->supprimerFournisseur();
        break;

    // === ESPACE ADMINISTRATION (UTILISATEURS) ===
    case 'pageAdmin':
        $controller = new \App\Controllers\UtilisateurController($db);
        $controller->afficherAdmin();
        break;

    case 'pageVoirUtilisateurs':
        $controller = new \App\Controllers\UtilisateurController($db);
        if (method_exists($controller, 'afficherListe')) {
            $controller->afficherListe();
        }
        break;

    case 'pageAjouterUtilisateur':
    case 'ajouterUtilisateur':
        $controller = new \App\Controllers\UtilisateurController($db);
        $controller->ajouterUtilisateur();
        break;

    case 'SupprimerUtilisateur':
        $controller = new \App\Controllers\UtilisateurController($db);
        $controller->supprimerUtilisateur();
        break;

    // === ERREUR 404 PAR DÉFAUT ===
    default:
        http_response_code(404);
        echo "<div style='padding:40px; text-align:center; font-family:sans-serif;'>
                <h1 style='color:var(--primary-blue); font-size:3rem;'>404</h1>
                <p style='color:#64748b;'>La page ou l'action demandée n'existe pas.</p>
                <a href='index.php?action=accueil' style='color:#1e3a5f; font-weight:bold;'>Retourner à l'accueil</a>
              </div>";
        break;
}