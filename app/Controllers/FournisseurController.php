<?php
namespace App\Controllers;
use App\Models\FournisseurModel;

use Exception;
use PDO;
class FournisseurController{

    private $pdo;
    private $FournisseurModel;

    public function __construct($db)
    {
        $this->pdo = $db;
        $this->FournisseurModel = new FournisseurModel($db);
    }


    // Si le formulaire a été envoyé (méthode POST) on traite les données 
    public function ajouterFournisseur(){
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nomEntreprise = $_POST['nomEntreprise'] ?? '';
            $adresse = $_POST['adresse'] ?? '';
            $NumeroTelephone = $_POST['NumeroTelephone'] ?? '';
            $Mail = $_POST['Mail'] ?? '';

            if (!empty($nomEntreprise)) {
                try {
                    $this->FournisseurModel->addFournisseur($nomEntreprise, $adresse, $NumeroTelephone, $Mail);

                    // Redirection vers la liste après succès
                    header("Location: index.php?action=afficherFournisseur");
                    exit();
                } catch (Exception $e) {
                    $_SESSION['error'] = "Erreur lors de l'ajout";
                    header('Location: index.php?action=ajouterFournisseur');
                    exit();
                }
            }
        }
        require_once VIEWS . '/pageAjouterFournisseur.php';
    }

    public function supprimerFournisseur(){
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['supprimer_fournisseur'])) {
            $id_fournisseur = $_POST['id_fournisseur'] ?? '';

            if (!empty($id_fournisseur)) {
                try {
                    $this->FournisseurModel->deleteFournisseur($id_fournisseur);
                    $_SESSION['success'] = 'Le fournisseur a été supprimé avec succès.';
                    header('Location: index.php?action=afficherFournisseur');
                    exit();
                } catch (Exception $e) {
                    $_SESSION['error'] = 'Impossible de supprimer ce fournisseur car il est lié à des commandes.';
                    header('Location: index.php?action=afficherFournisseur');
                    exit();
                }
            }
        }

        // Récupération pour affichage (uniquement en GET)
        $resFournisseurs = $this->FournisseurModel->getAllFournisseurs();
        return $resFournisseurs; 
    }


    public function modifierFournisseur(){
        $erreur = null;
        $fournisseur = null;

        // 1. Traitement de la modification (POST)
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['idFournisseur'] ?? '';
            $nom = $_POST['nomEntreprise'] ?? '';
            $adresse = $_POST['adresse'] ?? '';
            $tel = $_POST['NumeroTelephone'] ?? '';
            $mail = $_POST['Mail'] ?? '';

            if (!empty($id) && !empty($nom)) {
                try {
                    $this->FournisseurModel->updateFournisseur($id, $nom, $adresse, $tel, $mail);
                    header("Location: index.php?action=afficherFournisseur");
                    exit();
                } catch (Exception $e) {
                    $erreur = "Erreur lors de la modification : " . $e->getMessage();
                }
            } else {
                $erreur = "Le nom de l'entreprise est obligatoire.";
            }
        }

        // 2. Affichage du formulaire (GET ou après erreur POST)
        $id = $_GET['modifier'] ?? ($_POST['idFournisseur'] ?? null);
        if ($id) {
            $fournisseur = $this->FournisseurModel->getFournisseurById($id);
            if (!$fournisseur) {
                header("Location: index.php?action=afficherFournisseur");
                exit();
            }
            require_once VIEWS . '/pageModifierFournisseur.php';
        } else {
            header("Location: index.php?action=afficherFournisseur");
            exit();
        }
    }

    public function afficherFournisseur(){
        $resFournisseurs = $this->FournisseurModel->getAllFournisseurs();
        require VIEWS . '/pageFournisseurs.php';
    }
}
?>