<?php
namespace App\Controllers;
use App\Models\CommandeModel;
use App\Models\DevisModel as ModelsDevisModel;
use App\Models\FournisseurModel;
use App\Models\ColisModel;

use Exception;
use PDO;

class CommandeController{

    private $pdo;
    private $CommandeModel;
    private $DevisModel;
    private $FournisseurModel;
    private $ColisModel; 

    function __construct($db)
    {
        $this->pdo = $db;
        $this->CommandeModel = new CommandeModel($db);
        $this->DevisModel = new ModelsDevisModel($db);
        $this->FournisseurModel = new FournisseurModel($db);
        $this->ColisModel = new ColisModel($db); 
    }

    function ajouterCommande(){
        
        $erreur = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $NumeroBonDeCommande = $_POST['NumeroBonDeCommande'] ?? '';
            $idDevis = $_POST['idDevis'] ?? '';
            $idFournisseur = $_POST['idFournisseur'] ?? '';
            $AdresseDepart = $_POST['AdresseDepart'] ?? '';
            $nbColis = (int) ($_POST['nbColis'] ?? 1); 
            $AdresseArivee = $_POST['AdresseArivee'] ?? '';
            $dateArrivee = $_POST['DateArrivee'] ?? ''; 
            $nomFichier = "";
        
            if (!empty($_FILES['ImageCommande']['name'])) {
                $nomFichier = $_FILES['ImageCommande']['name'];
                move_uploaded_file($_FILES['ImageCommande']['tmp_name'], "uploads/" . $nomFichier);
            }

            // On vérifie aussi que le fournisseur est rempli
            if (!empty($NumeroBonDeCommande) && !empty($idDevis) && !empty($dateArrivee) && !empty($idFournisseur)) {
                try {
                    $dateDepart = date('Y-m-d');

                    if ($dateDepart) {
                        // Création de la commande
                        $this->CommandeModel->addCommande($NumeroBonDeCommande, $AdresseDepart, $AdresseArivee, $dateDepart, $nbColis, $idDevis, $dateArrivee);
                        
                        // Création automatique des colis
                        for ($i = 0; $i < $nbColis; $i++) {
                            $this->ColisModel->creerColis($NumeroBonDeCommande, $dateArrivee);
                        }

                        // On supprime l'ancienne liaison pour ce devis s'il y en avait une (nettoyage)
                        $stmtDel = $this->pdo->prepare("DELETE FROM Commandé_a_ WHERE idDevis = ?");
                        $stmtDel->execute([$idDevis]);

                        // On insère la nouvelle liaison
                        $stmtAdd = $this->pdo->prepare("INSERT INTO Commandé_a_ (idDevis, idFournisseur) VALUES (?, ?)");
                        $stmtAdd->execute([$idDevis, $idFournisseur]);
                        // --------------------------------------

                        header("Location: index.php?action=afficherCommande&sucess=1");
                        exit();
                    }
                } catch (Exception $e) {
                    if (strpos($e->getMessage(), 'UNIQUE constraint failed') !== false) {
                        $erreur = "Le numéro de commande n°$NumeroBonDeCommande existe déjà.";
                    } else {
                        $erreur = "Erreur SQL : " . $e->getMessage();
                    }
                }
            } else {
                $erreur = "Veuillez remplir tous les champs, y compris le fournisseur.";
            }
        }
        $listeDevis = $this->DevisModel->getAllDevisDecroissant();   
        $resNomEntreprise = $this->FournisseurModel->getAllFournisseurs();

        require_once VIEWS . '/pageAjouterCommande.php';
    }

    public function afficherCommandes(){
        // Pour les Administrateurs et le Service Postal, on affiche toutes les commandes
        if (isset($_SESSION['role']) && ($_SESSION['role'] === 'Administrateur' || $_SESSION['role'] === 'Service_Postal')) {
            $resListeCommandes = $this->CommandeModel->getListeCommandesCompletes();
        } else {
            // Pour les simples Utilisateurs, on n'affiche que leurs commandes
            $resListeCommandes = $this->CommandeModel->getToutesLesCommandesParUtilisateur($_SESSION['utilisateur_id']);
        }

        // On utilise la seule vue de commande qui existe réellement
        require_once VIEWS . '/pageMesCommandes.php';
    }
    

    public function supprimerCommande(){
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['supprimer_commande'])) {
            $NumeroBonDeCommande = $_POST['NumeroBonDeCommande'] ?? '';

            if (!empty($NumeroBonDeCommande)) {
                try {
                    $this->CommandeModel->deleteCommande($NumeroBonDeCommande);
                    header("Location: index.php?action=afficherCommande");
                    exit();
                } catch (Exception $e) {
                    echo "<script>alert('Erreur : Impossible de supprimer cette commande.');</script>";
                }
            }
        }
    }

    public function modifierCommande(){
        $erreur = null;
        $commande = null;

        if (isset($_GET['modifier'])) {
            $num = $_GET['modifier'];
            $query = $this->pdo->prepare("SELECT * FROM Commande WHERE NumeroBonDeCommande = ?");
            $query->execute([$num]);
            $commande = $query->fetch(PDO::FETCH_ASSOC);
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $NumeroBonDeCommande = $_POST['NumeroBonDeCommande'] ?? '';
            $idDevis = $_POST['idDevis'] ?? '';
            $AdresseDepart = $_POST['AdresseDepart'] ?? '';
            $nbColis = $_POST['nbColis'] ?? '';
            $AdresseArivee = $_POST['AdresseArivee'] ?? '';
            $dateArrivee = $_POST['DateArrivee'] ?? '';

            if (!empty($NumeroBonDeCommande) && !empty($idDevis) && !empty($AdresseArivee)) {
                try {
                    $success = $this->CommandeModel->updateCommande( $NumeroBonDeCommande, $AdresseDepart, $AdresseArivee, $nbColis, $idDevis, $dateArrivee);

                    if ($success) {
                        header("Location: index.php?action=afficherCommande");
                        exit();
                    } else {
                        $erreur = "La mise à jour a échoué.";
                    }
                } catch (Exception $e) {
                    $erreur = "Erreur SQL : " . $e->getMessage();
                }
            } else {
                $erreur = "Veuillez remplir les champs obligatoires.";
            }
        }

        $listeDevis = $this->DevisModel->getAllDevisDecroissant();
        $resNomEntreprise = $this->FournisseurModel->getAllFournisseurs();
        require_once VIEWS . '/pageModifierCommande.php';   
    }
}
?>