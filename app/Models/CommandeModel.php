<?php
namespace App\Models;
use \PDO;

class CommandeModel {

    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function recupererTroisDernieresCommandes() {
        $sql = "SELECT C.NumeroBonCommande, C.AdresseDepart, sc.Statut, C.AdresseArivee, D.nomDepartement
                FROM Commande C
                JOIN StatutCommande sc ON C.IdStatut = sc.IdStatut
                JOIN Devis De ON C.IdDevis = De.IdDevis
                JOIN Utilisateur U ON De.IdUtilisateur = U.IdUtilisateur
                LEFT JOIN Appartient_a A ON U.IdUtilisateur = A.IdUtilisateur
                LEFT JOIN Departement D ON A.IdDepartement = D.IdDepartement
                ORDER BY C.NumeroBonCommande ASC LIMIT 3";
        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function rechercherEtiquettes($terme) {
        $sql = "SELECT C.NumeroBonCommande, C.AdresseArivee, C.DateAjout
                FROM Commande C
                WHERE C.AdresseArivee LIKE :texte
                OR C.NumeroBonCommande = :num
                ORDER BY C.DateAjout DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':texte' => '%' . $terme . '%', ':num' => $terme]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getListeCommandesCompletes() {
        $sql = "SELECT C.*, F.NomFournisseur, De.Date_ AS DateDepart
                FROM Commande C
                INNER JOIN Devis De ON C.IdDevis = De.IdDevis
                JOIN Fournisseur F ON De.IdFournisseur = F.IdFournisseur
                ORDER BY De.Date_ DESC";
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

    public function recupererToutesLesCommandesParUtilisateur($identifiant_cas) {
        $sql = "SELECT C.*, De.Prix
                FROM Commande C
                INNER JOIN Devis De ON C.IdDevis = De.IdDevis
                INNER JOIN Utilisateur U ON De.IdUtilisateur = U.IdUtilisateur
                WHERE U.Identifiant = :cas";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':cas' => $identifiant_cas]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCommandesByStatut($statut) {
        $query = $this->pdo->prepare(
            "SELECT C.* FROM Commande C
             JOIN StatutCommande sc ON C.IdStatut = sc.IdStatut
             WHERE sc.Statut = ?"
        );
        $query->execute([$statut]);
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCommandesNonConfirmees() {
        $query = $this->pdo->query(
            "SELECT C.* FROM Commande C
             JOIN StatutCommande sc ON C.IdStatut = sc.IdStatut
             WHERE sc.Statut = 'non_confirmé'"
        );
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function deleteCommande($numeroBonCommande) {
        $query = $this->pdo->prepare("DELETE FROM Commande WHERE NumeroBonCommande = ?");
        return $query->execute([$numeroBonCommande]);
    }

    public function ajouterCommande($numero, $adresseDepart, $adresseArivee, $dateDepartDossier, $nbColis, $idDevis, $dateArriveeSaisie) {
        $sql = "INSERT INTO Commande (NumeroBonCommande, AdresseDepart, AdresseArivee, DateAjout, IdStatut, IdDevis)
                VALUES (?, ?, ?, ?, (SELECT IdStatut FROM StatutCommande WHERE Statut = 'en_cours'), ?)";
        $query = $this->pdo->prepare($sql);
        return $query->execute([$numero, $adresseDepart, $adresseArivee, $dateArriveeSaisie, $idDevis]);
    }

    public function modifierCommande($numero, $adresseDepart, $adresseArivee, $idDevis, $dateArriveeSaisie) {
        $sql = "UPDATE Commande SET
                IdDevis = ?,
                AdresseDepart = ?,
                AdresseArivee = ?,
                DateAjout = ?
                WHERE NumeroBonCommande = ?";
        $query = $this->pdo->prepare($sql);
        return $query->execute([$idDevis, $adresseDepart, $adresseArivee, $dateArriveeSaisie, $numero]);
    }

}
?>
