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
                move_uploaded_file($_FILES['ImageCommande']['tmp_name'], ROOT . "/uploads/" . $nomFichier);
            }

            // On vérifie aussi que le fournisseur est rempli (non, fournisseur n'est pas utilisé)
            if (!empty($NumeroBonDeCommande) && !empty($idDevis) && !empty($dateArrivee)) {
                try {
                    $dateDepart = date('Y-m-d');

                    if ($dateDepart) {
                        // Création de la commande
                        $this->CommandeModel->addCommande($NumeroBonDeCommande, $AdresseDepart, $AdresseArivee, $dateDepart, $nbColis, $idDevis, $dateArrivee, $nomFichier);
                        
                        // Création automatique des colis
                        $nomsColis = $_POST['nom_colis'] ?? [];
                        $commentairesColis = $_POST['commentaire'] ?? [];

                        for ($i = 0; $i < $nbColis; $i++) {
                            $nomColis = $nomsColis[$i] ?? null;
                            $commentaire = $commentairesColis[$i] ?? null;
                            $this->ColisModel->creerColis($NumeroBonDeCommande, $dateArrivee, $nomColis, $commentaire);
                        }

                        header("Location: index.php?action=afficherCommande&success=1");
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

        if (isset($_SESSION['role']) && ($_SESSION['role'] === 'Administrateur' || $_SESSION['role'] === 'Service_Postal')) {
            $listeDevis = $this->DevisModel->getAllDevisDecroissant();   
        } else {
            $listeDevis = $this->DevisModel->getDevisParUtilisateur($_SESSION['utilisateur_id']);
        }

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
        file_put_contents("/var/www/html/SAE401/debug.txt", "supprimerCommande called. METHOD: " . $_SERVER['REQUEST_METHOD'] . "\n", FILE_APPEND);
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['supprimer_commande'])) {
            $NumeroBonDeCommande = $_POST['NumeroBonDeCommande'] ?? '';
            file_put_contents("/var/www/html/SAE401/debug.txt", "NumeroBonDeCommande to delete: " . $NumeroBonDeCommande . "\n", FILE_APPEND);

            if (!empty($NumeroBonDeCommande)) {
                try {
                    $this->ColisModel->deleteColisParCommande($NumeroBonDeCommande);
                    $this->CommandeModel->deleteCommande($NumeroBonDeCommande);
                    file_put_contents("/var/www/html/SAE401/debug.txt", "Deletion successful for: " . $NumeroBonDeCommande . "\n", FILE_APPEND);
                } catch (Exception $e) {
                    file_put_contents("/var/www/html/SAE401/debug.txt", "Error deleting commande: " . $e->getMessage() . "\n", FILE_APPEND);
                }
            } else {
                file_put_contents("/var/www/html/SAE401/debug.txt", "NumeroBonDeCommande is empty.\n", FILE_APPEND);
            }
        } else {
            file_put_contents("/var/www/html/SAE401/debug.txt", "POST missing or supprimer_commande not set. POST: " . print_r($_POST, true) . "\n", FILE_APPEND);
        }
        header("Location: index.php?action=afficherCommande");
        exit();
    }

    public function modifierCommande(){
        $erreur = null;
        $commande = null;

        if (isset($_GET['modifier'])) {
            $num = $_GET['modifier'];
            $query = $this->pdo->prepare("SELECT * FROM Commande WHERE NumeroBonCommande = ?");
            $query->execute([$num]);
            $commande = $query->fetch(PDO::FETCH_ASSOC);
            $listeColisExistant = $this->ColisModel->getColisByCommande($num);
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $NumeroBonDeCommande = $_POST['NumeroBonDeCommande'] ?? '';
            $ancienNumeroBonDeCommande = $_POST['ancienNumeroBonDeCommande'] ?? $NumeroBonDeCommande;
            $idDevis = $_POST['idDevis'] ?? '';
            $AdresseDepart = $_POST['AdresseDepart'] ?? '';
            $AdresseArivee = $_POST['AdresseArivee'] ?? '';
            $dateArrivee = $_POST['DateArrivee'] ?? '';
            $nbColis = $_POST['nbColis'] ?? 1;
            $nomsColis = $_POST['nom_colis'] ?? [];
            $commentairesColis = $_POST['commentaire'] ?? [];

            $image = null;
            if (!empty($_FILES['ImageCommande']['name'])) {
                $nomFichier = $_FILES['ImageCommande']['name'];
                move_uploaded_file($_FILES['ImageCommande']['tmp_name'], ROOT . "/uploads/" . $nomFichier);
                $image = $nomFichier;
            }

            if (!empty($ancienNumeroBonDeCommande) && !empty($NumeroBonDeCommande) && !empty($idDevis) && !empty($AdresseArivee)) {
                try {
                    $success = $this->CommandeModel->updateCommande($ancienNumeroBonDeCommande, $NumeroBonDeCommande, $AdresseDepart, $AdresseArivee, $idDevis, $dateArrivee, $image);

                    if ($success) {
                        $this->ColisModel->deleteColisParCommande($NumeroBonDeCommande);
                        for ($i = 0; $i < $nbColis; $i++) {
                            $nomColis = $nomsColis[$i] ?? "Colis " . ($i + 1);
                            $commentaire = $commentairesColis[$i] ?? "";
                            $this->ColisModel->creerColis($NumeroBonDeCommande, $dateArrivee, $nomColis, $commentaire);
                        }
                        
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

    public function afficherEditionEtiquettes(){
        $terme = $_GET['champ_recherche'] ?? '';
        
        if (!empty($terme)) {
            $resultat = $this->CommandeModel->rechercherEtiquettes($terme);
        } else {
            // Par défaut, on affiche les 10 dernières commandes pour lesquelles on peut éditer une étiquette
            $sql = "SELECT NumeroBonCommande, AdresseArivee, DateAjout FROM Commande ORDER BY DateAjout DESC LIMIT 10";
            $resultat = $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        }

        require_once VIEWS . '/pageNouvelEnvoi.php';
    }
    public function validerLivraisonCommande() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['NumeroBonCommande'])) {
            $numeroBonCommande = $_POST['NumeroBonCommande'];
            $this->CommandeModel->marquerCommandeCommeLivree($numeroBonCommande);
        }
        header("Location: index.php?action=afficherCommande");
        exit();
    }
}
?>