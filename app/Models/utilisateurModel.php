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

    function ajouterUtilisateur($prenom, $nom, $roles, $mdp, $departements){
        //On utilise une transaction car on fait plusieurs insert en meme temps
        // roles et departements sont des tableau qui contiennent la liste des roles et des departement a associé a l'utilisateur. Si le user n'a pas de departement alors la variable est un tableau vide. 
        // Si il y a un seul role ou departement mettre quand meme dans un tableau.
        try {
            $this->pdo->beginTransaction();
            $sqlUser = "INSERT INTO Utilisateur(Nom, Prenom, mdpCAS, Identifiant)
                        VALUES (:nom,:prenom,:mdp,:id)";


            $stmt = $this->pdo->prepare($sqlUser);

            $stmt->execute([
                ':nom' => $nom,
                ':prenom' => $prenom,
                ':mdp' => $mdp,
                ':id' => $Identifiant
            ]);

            foreach ($roles as $role) {
                $sqlRole = "INSERT INTO Possede(IdUtilisateur,IdRole)
                            VALUES(:id,:idrole)";
                $stmt = $this->pdo->prepare($sqlRole);

                $stmt->execute([
                    ':id' => $Identifiant,
                    ':idrole' => $role
                ]);
            }

            foreach ($departements as $dep) {
                $sqlDep = "INSERT INTO Appartient_a(IdUtilisateur,IdDepartement)
                            VALUES(:id,:iddep)";
                $stmt = $this->pdo->prepare($sqlDep);

                $stmt->execute([
                    ':id' => $Identifiant,
                    ':iddep' => $dep
                ]);
            }

            $this->pdo->commit();
            return true;
        }catch (\PDOException $e) {
            $this->pdo->rollBack();
            
            
            return false;
        }
    
    }



}
?>