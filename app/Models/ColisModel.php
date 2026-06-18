<?php
namespace App\Models;
use \PDO;

class ColisModel {

    private $pdo;

    public function __construct($db) {
        $this->pdo = $db;
    }

    public function getFournisseursAyantColis() {
        $sql = "SELECT DISTINCT F.NomFournisseur
                FROM Fournisseur F
                JOIN Devis D ON F.IdFournisseur = D.IdFournisseur
                JOIN Commande C ON D.IdDevis = C.IdDevis
                JOIN Compose_une CU ON C.IdBonCommande = CU.IdBonCommande
                JOIN Colis co ON CU.IdColis = co.IdColis
                WHERE F.NomFournisseur IS NOT NULL
                ORDER BY F.NomFournisseur";
        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function creerColis($numeroBonCommande, $dateArrivee) {
    $stmtMax = $this->pdo->query("SELECT MAX(IdColis) FROM Colis");
    $maxId   = $stmtMax->fetchColumn();
    $newId   = $maxId ? ((int)$maxId + 1) : 1;


    $sql  = "INSERT INTO Colis (IdColis, date_arrivee_prevu, IdStatut) VALUES (?, ?, (SELECT IdStatut FROM StatutColis WHERE Statut = 'en_cours'))";
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([$newId, $dateArrivee]);

    $liaisonRequest = "INSERT INTO Compose_une (IdColis, IdBonCommande)
                   SELECT ?, IdBonCommande FROM Commande WHERE NumeroBonCommande = ?";
    $stmtLiaison = $this->pdo->prepare($liaisonRequest);
    return $stmtLiaison->execute([$newId, $numeroBonCommande]);
}

    public function marquerCommeLivre($idColis, $numeroBonCommande) {
        $stmt = $this->pdo->prepare(
            "UPDATE Colis SET IdStatut = (SELECT IdStatut FROM StatutColis WHERE Statut = 'livré')
             WHERE IdColis = :idColis"
        );
        $stmt->execute([':idColis' => $idColis]);

        $stmtCheck = $this->pdo->prepare(
            "SELECT co.IdStatut, sc.Statut
             FROM Colis co
             JOIN StatutColis sc ON co.IdStatut = sc.IdStatut
             JOIN Compose_une CU ON co.IdColis = CU.IdColis
             JOIN Commande C ON CU.IdBonCommande = C.IdBonCommande
             WHERE C.NumeroBonCommande = :numeroBonCommande"
        );
        $stmtCheck->execute([':numeroBonCommande' => $numeroBonCommande]);
        $tousLesColis = $stmtCheck->fetchAll(PDO::FETCH_ASSOC);

        $tousLivres = true;
        foreach ($tousLesColis as $colis) {
            if (strtolower($colis['Statut']) !== 'livré' && strtolower($colis['Statut']) !== 'livre') {
                $tousLivres = false;
                break;
            }
        }

        $nouveauStatut = $tousLivres ? 'livré' : 'en_cours';
        $stmtUpdate = $this->pdo->prepare(
            "UPDATE Commande SET IdStatut = (SELECT IdStatut FROM StatutCommande WHERE Statut = :statut)
             WHERE NumeroBonCommande = :numeroBonCommande"
        );
        $stmtUpdate->execute([':statut' => $nouveauStatut, ':numeroBonCommande' => $numeroBonCommande]);

        if ($tousLivres) {
            $stmtEmail = $this->pdo->prepare("SELECT U.Email, U.Prenom, C.NumeroBonCommande FROM Commande C JOIN Devis D ON C.IdDevis = D.IdDevis JOIN Utilisateur U ON D.IdUtilisateur = U.IdUtilisateur WHERE C.NumeroBonCommande = ?");
            $stmtEmail->execute([$numeroBonCommande]);
            $info = $stmtEmail->fetch(PDO::FETCH_ASSOC);

            if ($info && !empty($info['Email'])) {
                \App\Services\EmailService::sendEmail(
                    $info['Email'],
                    "Commande Livrée : " . $info['NumeroBonCommande'],
                    "Bonjour " . htmlspecialchars($info['Prenom']) . ",<br><br>Tous les colis ont été réceptionnés ! Votre commande <strong>" . htmlspecialchars($info['NumeroBonCommande']) . "</strong> est désormais marquée comme <strong>livrée</strong> par le service postal."
                );
            }
        }
    }

    public function getColisById($idColis) {
        $sql = "SELECT * FROM Colis WHERE IdColis = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$idColis]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateColis($idColis, $nomColis, $commentaire) {
        $sql  = "UPDATE Colis SET nom_colis = ?, Commentaire = ? WHERE IdColis = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$nomColis, $commentaire, $idColis]);
    }

    public function getListeColisComplete() {
        $sql = "SELECT co.IdColis, co.nom_colis, co.date_arrivee_prevu, sc.Statut,
                F.NomFournisseur, C.NumeroBonCommande, C.DateAjout AS DateCommande,
                C.AdresseArivee, U.Nom, U.Prenom, dep.NomDepartement, D.Date_
                FROM Colis co
                JOIN StatutColis sc ON co.IdStatut = sc.IdStatut
                JOIN Compose_une CU ON co.IdColis = CU.IdColis
                JOIN Commande C ON CU.IdBonCommande = C.IdBonCommande
                JOIN Devis D ON C.IdDevis = D.IdDevis
                JOIN Fournisseur F ON D.IdFournisseur = F.IdFournisseur
                JOIN Utilisateur U ON D.IdUtilisateur = U.IdUtilisateur
                LEFT JOIN Appartient_a A ON U.IdUtilisateur = A.IdUtilisateur
                LEFT JOIN Departement dep ON A.IdDepartement = dep.IdDepartement
                ORDER BY co.IdColis DESC";
        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function recupereToutesLesInfosParCommandes($NumeroBonCommande) {
        $requete_sql = "SELECT cmd.NumeroBonCommande, cmd.AdresseArivee,
                               co.date_arrivee_reel, co.date_arrivee_prevu
                        FROM Colis co
                        JOIN Compose_une CU ON co.IdColis = CU.IdColis
                        INNER JOIN Commande cmd ON CU.IdBonCommande = cmd.IdBonCommande
                        WHERE cmd.NumeroBonCommande = :numBon";

        $resultat_infos = $this->pdo->prepare($requete_sql);
        $resultat_infos->execute([':numBon' => $NumeroBonCommande]);

        return $resultat_infos->fetchAll(PDO::FETCH_ASSOC);
    }

    public function recupNbDeColis() {
        return $this->pdo->query("SELECT COUNT(*) FROM Commande")->fetchColumn();
    }

    public function nbColisRetard() {
        return $this->pdo->query(
            "SELECT COUNT(*) FROM Commande
             JOIN StatutCommande ON Commande.IdStatut = StatutCommande.IdStatut
             WHERE StatutCommande.Statut = 'retard'"
        )->fetchColumn();
    }

    public function nbColisLivré() {
        return $this->pdo->query(
            "SELECT COUNT(*) FROM Commande
             JOIN StatutCommande ON Commande.IdStatut = StatutCommande.IdStatut
             WHERE StatutCommande.Statut = 'livré'"
        )->fetchColumn();
    }

    public function nbColisEnTransit() {
        return $this->pdo->query(
            "SELECT COUNT(*) FROM Commande
             JOIN StatutCommande ON Commande.IdStatut = StatutCommande.IdStatut
             WHERE StatutCommande.Statut = 'en_cours'"
        )->fetchColumn();
    }

    public function getDernierColisLivre() {
        $query = $this->pdo->query(
            "SELECT Commande.* FROM Commande
             JOIN StatutCommande ON Commande.IdStatut = StatutCommande.IdStatut
             WHERE StatutCommande.Statut = 'livré'
             ORDER BY Commande.DateAjout DESC, Commande.NumeroBonCommande DESC LIMIT 1"
        );
        return $query->fetch(PDO::FETCH_ASSOC);
    }

    public function getColisByStatut($statut){
        $sql = " SELECT Colis.*, statutColis.statut
        FROM Colis
        JOIN statutColis USING (idStatut)
        WHERE statutColis.statut = :statut;
        ";

        $query = $this->pdo->prepare($sql);
        $query->execute([':statut' => $statut]);

        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

}
?>
