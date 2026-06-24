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

            if (!empty($prenom) && !empty($nom) && !empty($role) && !empty($mdp)) {
                try {
                    $this->UtilisateurModel->ajouterUtilisateur($prenom, $nom, $role, $mdp, $departement);
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

        // Ces deux lignes vont nous permettre de récupérer la liste des rôles 
        $varRoles = $this->pdo->query("SELECT * FROM Role"); 
        $listeRoles = $varRoles->fetchAll(\PDO::FETCH_ASSOC);

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

    public function afficherListeRoles() {
        $stmt = $this->pdo->query("SELECT * FROM Role");
        $resListeRoles = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        require_once VIEWS . '/pageVoirRoles.php';
    }

    public function afficherListeDepartements() {
        $resListeDepartements = $this->DepartementModel->getAllDepartements();
        require_once VIEWS . '/pageVoirDepartements.php';
    }

    public function ajouterDepartement(){
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['nomDepartement'])){
            $sql = $this->pdo->prepare("INSERT INTO Departement (NomDepartement) VALUES (?)");
            $sql->execute([trim($_POST['nomDepartement'])]);
            header("Location: index.php?action=pageAdmin&success=dep");
            exit();
        }
        require_once VIEWS . '/pageAjouterDepartement.php';
    }

    public function ajouterRole(){
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['nomRole'])){
            $sql = $this->pdo->prepare("INSERT INTO Role (Role) VALUES (?)");
            $sql->execute([trim($_POST['nomRole'])]);
            header("Location: index.php?action=pageAdmin&success=role");
            exit();

        }
        require_once VIEWS . '/pageAjouterRole.php';
    }
}

    
?>