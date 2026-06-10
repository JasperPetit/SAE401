<?php
namespace App\Controllers;
use \PDO;
use Exception;

class AuthController {
    private $pdo;

    public function __construct($db) {
        $this->pdo = $db;
    }

    public function afficherLogin() {
        $erreur = '';
        // CORRIGÉ : Le chemin est désormais parfait
        require_once 'app/views/pageLogin.php';
    }

    public function connecter() {
        $erreur = '';
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $identifiant = $_POST['identifiant'] ?? '';
            $mot_de_passe = $_POST['mot_de_passe'] ?? '';
            
            if ($identifiant !== '' && $mot_de_passe !== '') {
                try {
                    $sql = "SELECT U.*, R.nomRole
                            FROM Utilisateur U
                            JOIN Role R ON U.idRole = R.idRole
                            WHERE U.identifiantCAS = :id";
                            
                    $preparer = $this->pdo->prepare($sql);
                    $preparer->execute([':id' => $identifiant]);
                    $utilisateur = $preparer->fetch(PDO::FETCH_ASSOC);
                        
                    if ($utilisateur){
                        
                        $_SESSION['utilisateur_id'] = $utilisateur['identifiantCAS'];
                        $_SESSION['nom_complet'] = $utilisateur['Prenom'] . ' ' . $utilisateur['nom'];
                        $_SESSION['role'] = $utilisateur['nomRole']; 
                        
                        // --- CORRECTIONS DES REDIRECTIONS VERS TON ROUTEUR INDEX.PHP ---
                        if ($utilisateur['nomRole'] === 'ADMIN') {
                            header('Location: index.php?action=pageAdmin');
                        } 
                        elseif ($utilisateur['nomRole'] === 'Service_Postal') {
                            header('Location: index.php?action=pageTableauDeBord');
                        }
                        elseif ($utilisateur['nomRole'] === 'Service_Financier') {
                            // CORRIGÉ : Ajout de .php et redirection vers la vue financière unifiée
                            header('Location: index.php?action=pageServiceFinancierDevis'); 
                        }
                        elseif ($utilisateur['nomRole'] === 'Demandeur'){
                            $sql2 = "SELECT A.*
                            FROM Appartient_a A
                            JOIN Utilisateur U ON U.identifiantCAS = A.identifiantCAS
                            WHERE U.identifiantCAS = :id";
                            
                            $preparer2 = $this->pdo->prepare($sql2);
                            $preparer2->execute([':id' => $identifiant]);
                            $dpt = $preparer2->fetch(PDO::FETCH_ASSOC);

                            $_SESSION['departement'] = $dpt['idDepartement'] ?? '';
                            
                            // CORRIGÉ : Ajout de .php et redirection vers la vue demandeur unifiée
                            header('Location: index.php?action=accueil'); 
                        }
                        exit();
                    }     
                    else {
                        $erreur = 'Identifiant ou mot de passe incorrect';
                    }
                } catch (Exception $e) {
                    $erreur = 'Erreur technique';
                }
            } else {
                $erreur = 'Veuillez remplir tous les champs';
            }
        }
        // CORRIGÉ : Le chemin est désormais parfait
        require_once 'app/views/pageLogin.php';
    }

    public function deconnecter() {
        session_destroy();
        header('Location: index.php?action=login');     
        exit();
    }
}
?>