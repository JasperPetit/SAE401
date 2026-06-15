<?php
namespace App\Controllers;
use App\Models\CommandeModel;
use App\Models\ColisModel;
class AccueilController{
    private $commande;
    private $colis;

    public function __construct($db){
        $this->commande = new CommandeModel($db);
        $this->colis = new ColisModel($db);
    }

    public function afficherAccueil(){
        $colisEnAttente = $this->colis->getColisByStatut('en_cours');
        $commandesEnCours = $this->commande->getCommandesNonConfirmees();
        $commandesEnRetard = $this->commande->getCommandesByStatut('retard');
        $dernierColis = $this->colis->getDernierColisLivre();
    
        $nbAttente = count($colisEnAttente);
        $nbEnCours = count($commandesEnCours);
        $nbRetard = count($commandesEnRetard);
        require_once __DIR__ . '/../views/pageAccueil.php';
    }


    public function rechercherRapide() {
        $texte = $_GET['champ_recherche'] ?? null;
        $commande_trouvee = null;

        if ($texte) {
            $commande_trouvee = $this->commande->recupereToutesLesInfosParCommandes($texte);
        }
        return $commande_trouvee;
    }


}
?>

    


