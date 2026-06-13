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
        require_once VIEWS . '/pageLogin.php';
    }

public function connecter() {
    $erreur = '';
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $identifiant = $_POST['identifiant'] ?? '';
        $mot_de_passe = $_POST['mot_de_passe'] ?? '';
        
        if ($identifiant !== '' && $mot_de_passe !== '') {
            try {
                $sql = "SELECT U.*, R.Role AS nomRole, D.NomDepartement
                        FROM Utilisateur U
                        JOIN Possede P ON U.IdUtilisateur = P.IdUtilisateur
                        JOIN Role R ON P.IdRole = R.IdRole
                        LEFT JOIN Appartient_a A ON U.IdUtilisateur = A.IdUtilisateur
                        LEFT JOIN Departement D ON A.IdDepartement = D.IdDepartement
                        WHERE U.Identifiant = :id
                        AND U.mdpCas = :mdp";
                        
                $preparer = $this->pdo->prepare($sql);
                $preparer->execute([':id' => $identifiant, ':mdp' => $mot_de_passe]);
                $utilisateur = $preparer->fetch(PDO::FETCH_ASSOC);
                    
                if ($utilisateur) {
                    $_SESSION['utilisateur_id'] = $utilisateur['IdUtilisateur'];
                    $_SESSION['nom_complet'] = $utilisateur['Prenom'] . ' ' . $utilisateur['Nom'];
                    $_SESSION['role'] = $utilisateur['nomRole'];
                    $_SESSION['departement'] = $utilisateur['NomDepartement'];
                    
                    if ($utilisateur['nomRole'] === 'Administrateur') {
                        header('Location: index.php?action=pageAdmin');
                    } elseif ($utilisateur['nomRole'] === 'Service_Postal') {
                        header('Location: index.php?action=accueil');
                    } elseif ($utilisateur['nomRole'] === 'Service_Financier') {
                        header('Location: index.php?action=accueil');
                    } else {
                        header('Location: index.php?action=accueil');
                    }
                    exit();
                } else {
                    $erreur = 'Identifiant ou mot de passe incorrect';
                }
            } catch (Exception $e) {
                $erreur = 'Erreur technique : ' . $e->getMessage();
            }
        } else {
            $erreur = 'Veuillez remplir tous les champs';
        }
    }
    require_once VIEWS . '/pageLogin.php';
}

    public function deconnecter() {
        session_destroy();
        header('Location: index.php?action=login');     
        exit();
    }
}
?>