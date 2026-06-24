<?php 
namespace app\Models;
use PDO;

class devisModel {

    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getAllDevis() {
        return $this->getAllDevisDecroissant();
    }

    public function RecupererIdDevis() {
        return $this->getDevisId();
    }

    public function getAllDevisDecroissant() {
        $sql = "SELECT d.*, sd.Statut AS StatutDevis, u.Nom AS NomUtilisateur, u.Prenom AS PrenomUtilisateur, dep.NomDepartement, f.NomFournisseur
                FROM Devis d
                INNER JOIN Utilisateur u ON d.IdUtilisateur = u.IdUtilisateur
                INNER JOIN Fournisseur f ON d.IdFournisseur = f.IdFournisseur
                INNER JOIN StatutDevis sd ON d.IdStatut = sd.IdStatut
                LEFT JOIN Appartient_a a ON u.IdUtilisateur = a.IdUtilisateur
                LEFT JOIN Departement dep ON a.IdDepartement = dep.IdDepartement
                ORDER BY d.Date_ DESC";

        
        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getDevisParUtilisateur($idUtilisateur) {
        $sql = "SELECT d.*, sd.Statut AS StatutDevis, u.Nom AS NomUtilisateur, u.Prenom AS PrenomUtilisateur, dep.NomDepartement, f.NomFournisseur
                FROM Devis d
                INNER JOIN Utilisateur u ON d.IdUtilisateur = u.IdUtilisateur
                INNER JOIN Fournisseur f ON d.IdFournisseur = f.IdFournisseur
                INNER JOIN StatutDevis sd ON d.IdStatut = sd.IdStatut
                LEFT JOIN Appartient_a a ON u.IdUtilisateur = a.IdUtilisateur
                LEFT JOIN Departement dep ON a.IdDepartement = dep.IdDepartement
                WHERE d.IdUtilisateur = :id
                ORDER BY d.Date_ DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $idUtilisateur]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateStatutDevis($IdDevis, $idStatut){
        //On utilise idStatut(int) au lieux de Statut(str) pour que l'insertion soit plus simple a faire. Si probleme => mettre en place un systeme pour inserer avec Statut.
        $sql = "UPDATE Devis SET IdStatut = ? WHERE IdDevis = ?";
        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([$idStatut,$IdDevis]);
    }

    public function getDateDepart($IdDevis){
        $sql = "SELECT d.Date_
                FROM Devis";
        
        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addDevis($numeroDevis, $date, $imageDevis, $prix, $idUtilisateur, $idFournisseur) {
        //On ajoute idStatut directement a 1 car un devis sera automoatiquement ajouter en "en attente".
            $sql = "INSERT INTO Devis (numeroDevis, Date_, ImageDevis, Prix, IdStatut, IdUtilisateur, IdFournisseur) 
                    VALUES (:numeroDevis, :date_, :imageDevis, :prix, :idStatut, :idUtilisateur, :idFournisseur)";
            
            $stmt = $this->pdo->prepare($sql);
            
            return $stmt->execute([
                ':numeroDevis'   => $numeroDevis, 
                ':date_'          => $date, 
                ':imageDevis'    => $imageDevis, 
                ':prix'          => $prix, 
                ':idStatut'      => 1, 
                ':idUtilisateur' => $idUtilisateur, 
                ':idFournisseur' => $idFournisseur
            ]);
    }

    public function deleteDevis($IdDevis){
        //Penser a gérer la suppression des commandes lié soit avec un ON DELETE CASCADE soit éviter la suppression de la commande.
        $sql = "DELETE FROM Devis WHERE IdDevis = :IdDevis";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([':IdDevis' => $IdDevis]);
    }

    public function getDevisId(){
        $sql = "SELECT IdDevis
                FROM Devis";
        
        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_COLUMN);
    }

    public function updateDevis($IdDevis, $numeroDevis, $date, $imageDevis, $prix, $idStatut, $idUtilisateur, $idFournisseur) {
        $sql = "UPDATE Devis 
                SET numeroDevis = :numeroDevis, 
                    Date_ = :date, 
                    ImageDevis = :imageDevis, 
                    Prix = :prix, 
                    IdStatut = :idStatut, 
                    IdUtilisateur = :idUtilisateur, 
                    IdFournisseur = :idFournisseur
                WHERE IdDevis = :IdDevis";
        
        $stmt = $this->pdo->prepare($sql);
        
        return $stmt->execute([
            ':numeroDevis'   => $numeroDevis,
            ':date'          => $date,
            ':imageDevis'    => $imageDevis,
            ':prix'          => $prix,
            ':idStatut'      => $idStatut,
            ':idUtilisateur' => $idUtilisateur,
            ':idFournisseur' => $idFournisseur,
            ':IdDevis'       => $IdDevis
        ]);
    }

    public function getDevisDepartement($dep){
        $sql = "SELECT d.IdDevis, d.numeroDevis, d.Date_, d.ImageDevis, d.Prix, d.IdStatut,
                       sd.Statut AS StatutDevis, u.Nom AS NomUtilisateur, u.Prenom AS PrenomUtilisateur, 
                       dep.NomDepartement, f.NomFournisseur
                FROM Devis d
                INNER JOIN Utilisateur u ON d.IdUtilisateur = u.IdUtilisateur
                INNER JOIN Fournisseur f ON d.IdFournisseur = f.IdFournisseur
                INNER JOIN StatutDevis sd ON d.IdStatut = sd.IdStatut
                LEFT JOIN Appartient_a a ON u.IdUtilisateur = a.IdUtilisateur
                LEFT JOIN Departement dep ON a.IdDepartement = dep.IdDepartement
                WHERE dep.NomDepartement = :dep
                ORDER BY d.Date_ DESC";

        
        $query = $this->pdo->prepare($sql);
        $query->execute([':dep' => $dep]);

        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

}
?>