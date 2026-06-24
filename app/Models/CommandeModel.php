<?php
namespace App\Models;
use \PDO;

class CommandeModel {

    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getTroisDernieresCommandes() {
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
        $sql = "SELECT C.*, F.NomFournisseur, De.Date_ AS DateDepart, sc.Statut
                FROM Commande C
                INNER JOIN Devis De ON C.IdDevis = De.IdDevis
                JOIN Fournisseur F ON De.IdFournisseur = F.IdFournisseur
                JOIN StatutCommande sc ON C.IdStatut = sc.IdStatut
                ORDER BY De.Date_ DESC";
        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getToutesLesInfosParCommandes($NumeroBonCommande) {
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

    public function getToutesLesCommandesParUtilisateur($id_utilisateur) {
        $sql = "SELECT C.*, De.Prix, sc.Statut
                FROM Commande C
                INNER JOIN Devis De ON C.IdDevis = De.IdDevis
                INNER JOIN StatutCommande sc ON C.IdStatut = sc.IdStatut
                WHERE De.IdUtilisateur = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id_utilisateur]);
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

    public function addCommande($numero, $adresseDepart, $adresseArivee, $dateDepartDossier, $nbColis, $IdDevis, $dateArriveeSaisie, $image = "") {
        $sql = "INSERT INTO Commande (NumeroBonCommande, AdresseDepart, AdresseArivee, DateAjout, IdStatut, IdDevis, ImageBonDeCommande)
                VALUES (?, ?, ?, ?, (SELECT IdStatut FROM StatutCommande WHERE Statut = 'en_cours'), ?, ?)";
        $query = $this->pdo->prepare($sql);
        return $query->execute([$numero, $adresseDepart, $adresseArivee, $dateArriveeSaisie, $IdDevis, $image]);
    }

    public function updateCommande($ancienNumero, $nouveauNumero, $adresseDepart, $adresseArivee, $IdDevis, $dateArriveeSaisie, $image = null) {
        if ($image !== null) {
            $sql = "UPDATE Commande SET
                    NumeroBonCommande = ?,
                    IdDevis = ?,
                    AdresseDepart = ?,
                    AdresseArivee = ?,
                    DateAjout = ?,
                    ImageBonDeCommande = ?
                    WHERE NumeroBonCommande = ?";
            $query = $this->pdo->prepare($sql);
            return $query->execute([$nouveauNumero, $IdDevis, $adresseDepart, $adresseArivee, $dateArriveeSaisie, $image, $ancienNumero]);
        } else {
            $sql = "UPDATE Commande SET
                    NumeroBonCommande = ?,
                    IdDevis = ?,
                    AdresseDepart = ?,
                    AdresseArivee = ?,
                    DateAjout = ?
                    WHERE NumeroBonCommande = ?";
            $query = $this->pdo->prepare($sql);
            return $query->execute([$nouveauNumero, $IdDevis, $adresseDepart, $adresseArivee, $dateArriveeSaisie, $ancienNumero]);
        }
    }

    public function marquerCommandeCommeLivree($numeroBonCommande) {
        $sql = "UPDATE Commande SET IdStatut = (SELECT IdStatut FROM StatutCommande WHERE Statut = 'livré' OR Statut = 'livre') WHERE NumeroBonCommande = ?";
        $this->pdo->prepare($sql)->execute([$numeroBonCommande]);
        
        $sqlColis = "UPDATE Colis SET IdStatut = (SELECT IdStatut FROM StatutColis WHERE Statut = 'livré' OR Statut = 'livre') 
                     WHERE IdColis IN (SELECT IdColis FROM Compose_une WHERE IdBonCommande = 
                         (SELECT IdBonCommande FROM Commande WHERE NumeroBonCommande = ?))";
        $this->pdo->prepare($sqlColis)->execute([$numeroBonCommande]);

        $stmtEmail = $this->pdo->prepare("SELECT U.Email, U.Prenom, C.NumeroBonCommande FROM Commande C JOIN Devis D ON C.IdDevis = D.IdDevis JOIN Utilisateur U ON D.IdUtilisateur = U.IdUtilisateur WHERE C.NumeroBonCommande = ?");
        $stmtEmail->execute([$numeroBonCommande]);
        $info = $stmtEmail->fetch(PDO::FETCH_ASSOC);

        if ($info && !empty($info['Email'])) {
            \App\Services\EmailService::sendEmail(
                $info['Email'],
                "Commande Livrée : " . $info['NumeroBonCommande'],
                "Bonjour " . htmlspecialchars($info['Prenom']) . ",<br><br>Bonne nouvelle ! Votre commande <strong>" . htmlspecialchars($info['NumeroBonCommande']) . "</strong> a été intégralement réceptionnée par le service postal et est désormais marquée comme <strong>livrée</strong>."
            );
        }
    }

}
?>
