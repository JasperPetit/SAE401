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
        require_once 'app/views/pageColis.php';
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
            $taille = $_POST['Taille'] ?? 0;
            $poids = $_POST['Poids'] ?? 0;

            if ($idColis) {
                $succes = $this->ColisModel->updateColis($idColis, $taille, $poids);
                if ($succes) {
                    // Redirection vers la liste des colis après succès
                    header("Location: index.php?action=afficherColis");
                    exit();
                } else {
                    $erreur = "Erreur lors de la mise à jour.";
                }
            }
        }

        // Affichage de la vue
        require_once 'app/views/pageModifierColis.php';
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
            header('Location: index.php?action=afficherColisPostale');
            exit();
        }

}
?>