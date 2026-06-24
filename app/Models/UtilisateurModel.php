<?php
namespace App\Models;
use \PDO;

class utilisateurModel {

    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getAllUtilisateurs(){
        //GOUP_COnCAT permet de joindre plusieurs ligne en une seul, la valeur associé a la clé Roles sera donc un tableau.
        $sql = "SELECT U.*, GROUP_CONCAT(R.Role, ', ') AS Roles, GROUP_CONCAT(R.Role, ', ')
                FROM Utilisateur U
                LEFT JOIN Possede P ON U.IdUtilisateur = P.IdUtilisateur
                LEFT JOIN Role R ON P.IdRole = R.IdRole
                GROUP BY U.IdUtilisateur";
                
        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function deleteUtilisateur($id){
        $sql = "DELETE FROM Utilisateur WHERE idUtilisateur = :id";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([':id' => $id]);
    }

    function ajouterUtilisateur($prenom, $nom, $roles, $mdp, $departements, $email){
        try {
            // Génération automatique de l'identifiant CAS (ex: amartin pour Alice Martin)
            $identifiant = strtolower(substr($prenom, 0, 1) . str_replace(' ', '', $nom));

            $this->pdo->beginTransaction();
            $sqlUser = "INSERT INTO Utilisateur(Nom, Prenom, mdpCas, Identifiant, Email)
                        VALUES (:nom,:prenom,:mdp,:id,:email)";

            $stmt = $this->pdo->prepare($sqlUser);
            $stmt->execute([
                ':nom' => $nom,
                ':prenom' => $prenom,
                ':mdp' => $mdp,
                ':id' => $identifiant,
                ':email' => $email
            ]);

            // Récupérer l'ID auto-incrémenté créé par SQLite
            $idUtilisateur = $this->pdo->lastInsertId();

            if (!empty($roles)) {
                if (!is_array($roles)) $roles = [$roles];
                foreach ($roles as $role) {
                    $sqlRole = "INSERT INTO Possede(IdUtilisateur,IdRole)
                                VALUES(:id,:idrole)";
                    $stmt = $this->pdo->prepare($sqlRole);
                    $stmt->execute([
                        ':id' => $idUtilisateur,
                        ':idrole' => $role
                    ]);
                }
            }

            if (!empty($departements)) {
                if (!is_array($departements)) $departements = [$departements];
                foreach ($departements as $dep) {
                    if (!empty($dep)) {
                        $sqlDep = "INSERT INTO Appartient_a(IdUtilisateur,IdDepartement)
                                    VALUES(:id,:iddep)";
                        $stmt = $this->pdo->prepare($sqlDep);
                        $stmt->execute([
                            ':id' => $idUtilisateur,
                            ':iddep' => $dep
                        ]);
                    }
                }
            }

            $this->pdo->commit();
            return true;
        } catch (\PDOException $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }



}
?>