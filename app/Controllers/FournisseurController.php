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
                    header("Location: afficherFournisseur");
                    exit();
                } catch (Exception $e) {
                    $_SESSION['error'] = "Erreur lors de l'ajout";
                    header('Location: ajouterFournisseur');
                    exit();
                }
            }
        }
        require_once 'views/pageAjouterFournisseur.php';
    }

    public function supprimerFournisseur(){
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['supprimer_fournisseur'])) {
            $id_fournisseur = $_POST['id_fournisseur'] ?? '';

            if (!empty($id_fournisseur)) {
                try {
                    $this->FournisseurModel->deleteFournisseur($id_fournisseur);
                    $_SESSION['success'] = 'Le fournisseur a été supprimé avec succès.';
                    header('Location: afficherFournisseur');
                    exit();
                } catch (Exception $e) {
                    $_SESSION['error'] = 'Impossible de supprimer ce fournisseur car il est lié à des commandes.';
                    header('Location: afficherFournisseur');
                    exit();
                }
            }
        }

        // Récupération pour affichage (uniquement en GET)
        $resFournisseurs = $this->FournisseurModel->getAllFournisseurs();
        return $resFournisseurs; // ou include de la vue
        }


    public function modifierFournisseur(){
        $erreur = null;
        $fournisseur = null;

        // On récupère les infos du fournisseur si l'ID est dans l'URL
        if (isset($_GET['modifier'])) {
            $id = $_GET['modifier'];
            $fournisseur = $this->FournisseurModel->getFournisseurById($id);
            require_once 'views/pageModifierFournisseur.php';
        }

        // Traitement de la modification
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['idFournisseur'] ?? '';
            $nom = $_POST['nomEntreprise'] ?? '';
            $adresse = $_POST['adresse'] ?? '';
            $tel = $_POST['NumeroTelephone'] ?? '';
            $mail = $_POST['Mail'] ?? '';

            if (!empty($id) && !empty($nom)) {
                try {
                    $this->FournisseurModel->updateFournisseur( $id, $nom, $adresse, $tel, $mail);
                    header("Location: afficherFournisseur");
                    exit();
                } catch (Exception $e) {
                    $erreur = "Erreur lors de la modification";
                    require_once 'views/pageModifierFournisseur.php';
                }
            } else {
                $erreur = "Le nom de l'entreprise est obligatoire.";
                require_once 'views/pageModifierFournisseur.php';
            }
        }
        require_once __DIR__ . '/../views/pageModifierFournisseur.php';
    }

    public function afficherFournisseur(){
        $resFournisseurs = $this->FournisseurModel->getAllFournisseurs();
        require 'views/pageFournisseurs.php';
    }
}
?>