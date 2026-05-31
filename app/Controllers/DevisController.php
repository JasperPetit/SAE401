<?php 
namespace App\Controllers;

use App\Models\DevisService as ModelsDevisService;
use App\Models\FournisseurModel;
use PDO;
use PDOException;
use Exception;

class DevisController{
    
    private $pdo;
    //  Vérifier si DevisService existe encore
    private $DevisService;
    private $FournisseurModel;

    public function __construct($db)
    {
        $this->pdo = $db;
        $this->DevisService = new ModelsDevisService($db);
        $this->FournisseurModel = new FournisseurModel($db);
    }

    public function ajouterDevis(){

        try{
        $this->DevisService->ajouterDevis($_POST,$_FILES,$_SESSION);
        if ($_SESSION['role']=='ADMIN'){
            header('Location: pageInfosDevis?success=1');
        }
        elseif ($_SESSION['role']=='Demandeur'){
            header('Location: PageInfosDevisDemandeur');
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
                    $this->DevisService->supprimerDevis($idDevis);
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
            $nomFichier = $devi['ImageDevis'];
            if (!empty($_FILES['ImageDevis']['name'])) {
                $nomFichier = $_FILES['ImageDevis']['name'];
            
                // On le déplace directement dans le dossier "uploads"
             // ATTENTION : Le dossier "uploads" doit exister physiquement !
                move_uploaded_file($_FILES['ImageDevis']['tmp_name'], "uploads/" . $nomFichier);
                }
            $tab = [
            'name' => $_POST['name'],
            'prix' => $_POST['prix'],
            'idFournisseur' => $_POST['idFournisseur'],
            'details' => $_POST['details'],
            'nomFichier' => $nomFichier,

            'signatureParDefaut' => $devi['SignatureOuiOuNon'],
            'idDevi' => $devi['idDevis']
            ];
        
            if (!empty($_POST['idDevis'])) {
                try {
                    $success = $this->DevisService->modifierDevis($tab);
                
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
        $listeDevis = $this->DevisService->getAllDevis();

        require_once 'views/pageInfosDevisAdmin.php';
    }

    public function afficherDevisDepartement(){
        $listeDevis = $this->DevisService->getDevisDepartement();

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