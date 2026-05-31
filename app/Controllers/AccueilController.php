<?php
namespace App\Controllers;
use App\Models\CommandeModel;
class AccueilController{
    private $commande;
    public function __construct($db){
        $this->commande = new CommandeModel($db);
    }
    public function afficherAccueil(){
        $colisEnAttente = $this->commande->getCommandesByStatut('en_cours');
        $commandesEnCours = $this->commande->getCommandesNonConfirmees();
        $commandesEnRetard = $this->commande->getCommandesByStatut( 'retard');
        $dernierColis = $this->commande->getDernierColisLivre();
    
        $nbAttente = count($colisEnAttente);
        $nbEnCours = count($commandesEnCours);
        $nbRetard = count($commandesEnRetard);
        require_once __DIR__ . '/../views/pageAccueil.php';
    }


    public function rechercherRapide() {
        $texte = $_GET['champ_recherche'] ?? null;
        $commande_trouvee = null;

        if ($texte) {
            $commande_trouvee = $this->commande->chercherCommande($texte);
        }
        return $commande_trouvee;
    }


}
?>

    


