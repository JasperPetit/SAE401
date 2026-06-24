<?php
namespace App\Controllers;

use App\Models\UtilisateurModel;
use App\Models\DepartementModel;
use Exception;

class UtilisateurController{

    private $pdo;
    private $UtilisateurModel;
    private $DepartementModel;

    public function __construct($db)
    {
        $this->pdo = $db;
        $this->UtilisateurModel = new UtilisateurModel($db);
        $this->DepartementModel = new DepartementModel($db);
    }

    public function supprimerUtilisateur(){
        // Vérification du POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['supprimer'])) {
            $id_utilisateur = $_POST['id_utilisateur'] ?? '';

            if (!empty($id_utilisateur)) {
                try {
                    // Suppression via le modèle
                    $this->UtilisateurModel->deleteUtilisateur($id_utilisateur);
                    
                    // Redirection CORRECTE vers la liste
                    header("Location: index.php?action=pageVoirUtilisateurs&success=1");
                    exit();

                } catch (Exception $e) {
                    // Erreur : Redirection avec message
                    header("Location: index.php?action=pageVoirUtilisateurs&error=" . urlencode($e->getMessage()));
                    exit();
                }
            }
        }

        // Si pas de POST, on renvoie à la liste
        header("Location: index.php?action=pageVoirUtilisateurs");
        exit();
    }

    public function ajouterUtilisateur(){
        $erreur = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $prenom = $_POST['prenom'] ?? '';
            $nom = $_POST['nom'] ?? '';
            $role = $_POST['Role'] ?? '';
            $mdp = password_hash($_POST['mdpCAS'],PASSWORD_DEFAULT) ?? '';
            $departement = $_POST['departement'] ?? null;
            $email = $_POST['email'] ?? '';

            if (!empty($prenom) && !empty($nom) && !empty($role) && !empty($mdp) && !empty($email)) {
                try {
                    $this->UtilisateurModel->ajouterUtilisateur($prenom, $nom, $role, $mdp, $departement, $email);
                    header("Location: index.php?action=pageVoirUtilisateurs");
                    exit();
                } catch (Exception $e) {
                    $erreur = "Erreur : " . $e->getMessage();
                }
            } else {
                $erreur = "Veuillez remplir tous les champs !";
            }
        }
        $ListeDepartement = $this->DepartementModel->getAllDepartements();
        require_once VIEWS . '/pageAjouterUtilisateur.php';
    }

    // À SUPPRIMER PEUT ETRE IL FAUT VOIR SI C'EST VRAIMENT NÉCESSAIRE
    public function afficherAdmin(){
        require_once VIEWS . '/pageAdmin.php';
    }

    // À SUPPRIMER PEUT ETRE IL FAUT VOIR SI C'EST VRAIMENT NÉCESSAIRE
    public function afficherListe() {
        $resListeUtilisateurs = $this->UtilisateurModel->getAllUtilisateurs();
        require_once VIEWS . '/pageVoirUtilisateurs.php';
    }
}
?>