<?php 
namespace App\Controllers;

use App\Models\DevisModel as ModelsDevisModel;
use App\Models\FournisseurModel;
use PDO;
use PDOException;
use Exception;

class DevisController{
    
    private $pdo;
    private $DevisModel;
    private $FournisseurModel;

    public function __construct($db)
    {
        $this->pdo = $db;
        $this->DevisModel = new ModelsDevisModel($db);
        $this->FournisseurModel = new FournisseurModel($db);
    }

    public function ajouterDevis(){

        try{
            $nomFichier = "";
            if (!empty($_FILES['ImageDevis']['name'])) {
                $nomFichier = $_FILES['ImageDevis']['name'];
                move_uploaded_file($_FILES['ImageDevis']['tmp_name'], "uploads/" . $nomFichier);
            }

            $this->DevisModel->addDevis(
                $_POST['NumeroDevis'] ?? '',
                date('Y-m-d'),
                $nomFichier,
                $_POST['prix'] ?? 0,
                $_SESSION['idUtilisateur'] ?? 1, // Par défaut 1 si non connecté (à ajuster)
                $_POST['idFournisseur'] ?? ''
            );

            if (isset($_SESSION['role']) && $_SESSION['role']=='ADMIN'){
                header('Location: pageInfosDevis?success=1');
            }
            elseif (isset($_SESSION['role']) && $_SESSION['role']=='Demandeur'){
                header('Location: PageInfosDevisDemandeur');
            } else {
                header('Location: pageInfosDevis?success=1');
            }
            exit();
        }
        catch (PDOException $e){
            if($e->getCode() == '23000'){
                header('Location: formulaireDevis?error=doublon');            
            }
            else{
                die("Erreur SQL inattendue : " . $e->getMessage());
            }
            exit();
        }
    }
    
    public function supprimerDevis(){
        // 1. Vérification sécurisée
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['supprimer_devis'])){
            
            // On utilise l'opérateur de coalescence (??) pour éviter les erreurs "Undefined index"
            $idDevis = $_POST['idDevis'] ?? null;

            if (!empty($idDevis)) {
                try {
                    $this->DevisModel->deleteDevis($idDevis);
                    header("Location: pageInfosDevis?success=suppression");
                    exit();
                } catch (Exception $e) {
                    // Erreur technique (ex: contrainte SQL)
                    header("Location: pageInfosDevis?error=technique");
                    exit();
                }
            } else {
                // L'ID est vide -> On redirige avec une erreur explicite
                header("Location: pageInfosDevis?error=id_manquant");
                exit();
            }
        }
        
        // Si on arrive ici (pas POST ou pas le bon bouton), on redirige vers la liste
        header("Location: pageInfosDevis");
        exit();
    
    }


   public function modifierDevis(){
            $erreur = null;
            $devi = null;
        if (isset($_GET['modifier'])) {
            $num = $_GET['modifier'];
            $query = $this->pdo->prepare("SELECT * FROM Devis LEFT JOIN Commandé_a_ USING (idDevis) WHERE idDevis = ?");
            $query->execute([$num]);
            $devi = $query->fetch(PDO::FETCH_ASSOC);
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST'){
            $nomFichier = $devi['ImageDevis'] ?? '';
            if (!empty($_FILES['ImageDevis']['name'])) {
                $nomFichier = $_FILES['ImageDevis']['name'];
            
                // On le déplace directement dans le dossier "uploads"
                move_uploaded_file($_FILES['ImageDevis']['tmp_name'], "uploads/" . $nomFichier);
            }

            if (!empty($_POST['idDevis'])) {
                try {
                    $success = $this->DevisModel->updateDevis(
                        $_POST['idDevis'],
                        $_POST['NumeroDevis'] ?? $devi['numeroDevis'],
                        $devi['Date_'], // On garde la date d'origine ou on met date('Y-m-d') ?
                        $nomFichier,
                        $_POST['prix'] ?? $devi['Prix'],
                        $_POST['idStatut'] ?? $devi['IdStatut'],
                        $_SESSION['idUtilisateur'] ?? $devi['IdUtilisateur'],
                        $_POST['idFournisseur'] ?? $devi['IdFournisseur']
                    );
                
                    if ($success) {
                        header("Location: pageInfosDevis");
                        exit();
                    } else {
                        $erreur = "La mise à jour a échoué.";
                    }
                
                } catch (PDOException $e) {
                    $erreur = "Erreur SQL : " . $e->getMessage();
                }
            } else {
                $erreur = "Veuillez remplir les champs obligatoires.";
            }
        }
        $resFournisseurs = $this->FournisseurModel->getAllFournisseurs();
        
        require_once __DIR__ . '/../views/pageModifierDevis.php';

   }

    public function afficherDevis(){
        $listeDevis = $this->DevisModel->getAllDevisDecroissant();

        require_once 'views/pageInfosDevisAdmin.php';
    }

    public function afficherDevisDepartement(){
        $listeDevis = $this->DevisModel->getDevisDepartement($_SESSION['departement'] ?? '');

        require_once 'views/pageInfosDevis.php';
    }

    public function afficherFormulaire(){
        $resFournisseurs = $this->FournisseurModel->getAllFournisseurs();
        require_once 'views/pageAjoutDevis.php';
    }

    // Action : Valider un devis
    public function validerDevis() {
        if (isset($_GET['id'])) {
            $idDevis = $_GET['id'];
            $this->financeModel->updateStatutDevis($idDevis, 1); // 1 = Accepté
        }
        // Redirection vers la liste
        header('Location: pageServiceFinancierDevis');
        exit();
    }

    // Action : Refuser un devis
    public function refuserDevis() {
        if (isset($_GET['id'])) {
            $idDevis = $_GET['id'];
            $this->financeModel->updateStatutDevis($idDevis, 0); // 0 = Refusé
        }
        // Redirection vers la liste
        header('Location: pageServiceFinancierDevis');
        exit();
    }
}

?>