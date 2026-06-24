<?php
namespace App\Controllers;
use App\Models\ColisModel;

class ColisController{

    private $pdo;
    private $ColisModel;

    public function __construct($db)
    {
        $this->pdo = $db;
        $this->ColisModel = new ColisModel($db);
    }

    public function afficherColis(){
        // À utiliser pour les pages de suivis de colis
        $resListeColis = $this->ColisModel->getListeColisComplete();
        $fournisseursFiltre = $this->ColisModel->getFournisseursAyantColis();
        require_once VIEWS . '/pageColis.php';
    }

    public function modifierColis() {
        $erreur = null;
        $colis = null;

        // Récupération de l'ID depuis l'URL
        if (isset($_GET['idColis'])) {
            $idColis = $_GET['idColis'];
            $colis = $this->ColisModel->getColisById($idColis);
        }

        // Traitement du formulaire
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $idColis = $_GET['idColis'] ?? $_POST['idColis'];
            $nom_colis = $_POST['nom_colis'] ?? '';
            $commentaire = $_POST['commentaire'] ?? '';

            if ($idColis) {
                try {
                    $succes = $this->ColisModel->updateColis($idColis, $nom_colis, $commentaire);
                    if ($succes) {
                        // Redirection vers la liste des colis après succès
                        header("Location: index.php?action=afficherColis");
                        exit();
                    } else {
                        $erreur = "Erreur lors de la mise à jour.";
                    }
                } catch (Exception $e) {
                    $erreur = "Erreur SQL : " . $e->getMessage();
                }
            }
        }

        // Affichage de la vue
        require_once VIEWS . '/pageModifierColis.php';
    }

    public function validerLivraison() {
            // On vérifie que la requête est bien en POST
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                
                // On récupère les données proprement
                $id = $_POST['id'] ?? null;
                $idCommande = $_POST['idCommande'] ?? null;

                if ($id && $idCommande) {
                    // On appelle le modèle
                    $this->ColisModel->marquerCommeLivre($id, $idCommande);
                }
            }

            // Redirection
            header('Location: index.php?action=afficherColis');
            exit();
        }

    public function imprimer() {
        $numBon = $_GET['id'] ?? '';
        if ($numBon) {
            $resultat_infos = $this->ColisModel->recupereToutesLesInfosParCommandes($numBon);
            require_once VIEWS . '/pageEtiquette.php';
        } else {
            header('Location: index.php?action=nouveau');
            exit();
        }
    }
    public function supprimerColis() {
        error_log("supprimerColis called. METHOD: " . $_SERVER['REQUEST_METHOD'] . "\n", 3, "/var/www/html/SAE401/debug.log");
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $idColis = $_POST['idColis'] ?? null;
            error_log("idColis to delete: " . $idColis . "\n", 3, "/var/www/html/SAE401/debug.log");
            if ($idColis) {
                try {
                    $this->ColisModel->deleteColis($idColis);
                    error_log("Colis $idColis deleted successfully.\n", 3, "/var/www/html/SAE401/debug.log");
                } catch (Exception $e) {
                    error_log("Error deleting colis: " . $e->getMessage() . "\n", 3, "/var/www/html/SAE401/debug.log");
                }
            } else {
                error_log("idColis is empty in POST.\n", 3, "/var/www/html/SAE401/debug.log");
            }
        }

        header("Location: index.php?action=afficherColis");
        exit();
    }

}
?>