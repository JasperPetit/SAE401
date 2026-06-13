<?php
namespace App\Models;
use \PDO;

class FournisseurModel {

    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getAllFournisseurs() {
        $sql = "SELECT f.*, c.NomCategorie
        FROM Fournisseur f
        LEFT JOIN categorise_dans USING (IdFournisseur)
        LEFT JOIN CategorieFournisseur c USING (IdCategorie)";

        
        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function deleteFournisseur( $id) {
        $sql = "DELETE FROM Fournisseur WHERE IdFournisseur = :Id";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([':Id' => $id]);
    }

    public function getFournisseurById($id){
        $sql = "SELECT f.*, c.NomCategorie
            FROM Fournisseur f
            LEFT JOIN categorise_dans USING (IdFournisseur)
            LEFT JOIN CategorieFournisseur c USING (IdCategorie)
            WHERE f.IdFournisseur = :id";

        $query = $this->pdo->prepare($sql);
        $query->execute([':id' => $id]);

        return $query->fetch(PDO::FETCH_ASSOC);
    }

    public function addFournisseur($nomFournisseur, $adresse, $numeroTelephone, $mail) {
        $sql = "INSERT INTO Fournisseur (NomFournisseur, Adresse, numeroTelephone, Mail) 
                VALUES (:nomFournisseur, :adresse, :numeroTelephone, :mail)";
        
        $stmt = $this->pdo->prepare($sql);
        
        return $stmt->execute([
            ':nomFournisseur'  => $nomFournisseur,
            ':adresse'         => $adresse,
            ':numeroTelephone' => $numeroTelephone,
            ':mail'            => $mail
        ]);
    }

    public function updateFournisseur($idFournisseur, $nomFournisseur, $adresse, $numeroTelephone, $mail) {
        $sql = "UPDATE Fournisseur 
                SET NomFournisseur = :nomFournisseur, 
                    Adresse = :adresse, 
                    numeroTelephone = :numeroTelephone, 
                    Mail = :mail 
                WHERE IdFournisseur = :idFournisseur";
        
        $stmt = $this->pdo->prepare($sql);
        
        return $stmt->execute([
            ':nomFournisseur'  => $nomFournisseur,
            ':adresse'         => $adresse,
            ':numeroTelephone' => $numeroTelephone,
            ':mail'            => $mail,
            ':idFournisseur'   => $idFournisseur 
        ]);
    }

}

?>