<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Suivi Global des Commandes — Admin</title>
    <link rel="stylesheet" href="public/css/style.css">
</head>
<body class="service-admin">

    <?php require_once VIEWS . '/navbar.php'; ?>

    <main class="main">
        <div class="page-header">
            <h1>Supervision & Nouvel Envoi</h1>
            <p>Régularisez une commande ou enregistrez un nouveau flux entrant</p>
        </div>

        <div class="card">
            <div class="card-title" style="margin-bottom:14px;">Enregistrer une nouvelle commande</div>
            <form action="index.php?action=AjouterCommande" method="POST" enctype="multipart/form-data">
                
                <div class="form-grid">
                    <div>
                        <label class="f-label">Numéro de commande <span class="req">*</span></label>
                        <input type="text" name="NumeroBonDeCommande" class="f-input" placeholder="ex: CMD-2025-001" required>
                    </div>
                    <div>
                        <label class="f-label">Date de commande <span class="req">*</span></label>
                        <input type="date" name="DateArrivee" class="f-input" required>
                    </div>
                </div>

                <div class="form-grid" style="margin-top:14px;">
                    <div>
                        <label class="f-label">Devis associé <span class="req">*</span></label>
                        <select name="idDevis" class="f-input" required>
                            <option value="">Choisir un devis</option>
                            <?php if(!empty($listeDevis)): foreach ($listeDevis as $devis): ?>
                                <?php 
                                    $statutDevis = $devis['StatutDevis'] ?? ''; 
                                    if ($devis['IdStatut'] == 2 || stripos($statutDevis, 'validé') !== false || stripos($statutDevis, 'accepté') !== false): 
                                        $selected = (isset($_GET['idDevis']) && $_GET['idDevis'] == $devis['IdDevis']) ? 'selected' : '';
                                ?>
                                    <option value="<?= $devis['IdDevis'] ?>" <?= $selected ?>>Devis n°<?= htmlspecialchars($devis['IdDevis']) ?> (<?= htmlspecialchars($devis['Prix'] ?? '') ?>€)</option>
                                <?php endif; ?>
                            <?php endforeach; endif; ?>
                        </select>
                    </div>
                    <div>
                        <label class="f-label">Fournisseur <span class="req">*</span></label>
                        <select name="idFournisseur" class="f-input" required>
                            <option value="">Choisir un fournisseur</option>
                            <?php if(!empty($resNomEntreprise)): foreach ($resNomEntreprise as $fournisseur): ?>
                                <option value="<?= $fournisseur['IdFournisseur'] ?>"><?= htmlspecialchars($fournisseur['NomFournisseur']) ?></option>
                            <?php endforeach; endif; ?>
                        </select>
                    </div>
                </div>

                <div class="form-grid" style="margin-top:14px;">
                    <div>
                        <label class="f-label">Adresse de départ (Fournisseur) <span class="req">*</span></label>
                        <input type="text" name="AdresseDepart" class="f-input" placeholder="Ville, Pays" required>
                    </div>
                    <div>
                        <label class="f-label">Adresse d'arrivée (IUT) <span class="req">*</span></label>
                        <input type="text" name="AdresseArivee" class="f-input" value="IUT Villetaneuse, 99 Av. Jean Baptiste Clément, 93430 Villetaneuse" required>
                    </div>
                </div>

                <div class="form-grid" style="margin-top:14px;">
                    <div>
                        <label class="f-label">Nombre de colis attendus <span class="req">*</span></label>
                        <input type="number" name="nbColis" id="nbColis" min="1" value="1" class="f-input" required>
                    </div>
                    <div>
                        <label class="f-label">Bon de commande (PDF/JPG) <span class="req">*</span></label>
                        <input type="file" name="ImageCommande" class="f-input" accept=".pdf, .jpg, .jpeg" required>
                    </div>
                </div>

                <div id="conteneur-colis" style="margin-top: 14px;"></div>

                <div class="actions-row" style="margin-top:20px;">
                    <button type="submit" class="btn btn-gold"><?= icon('send', 14) ?> Enregistrer la commande</button>
                    <a href="index.php?action=afficherCommande" class="btn btn-outline">Annuler</a>
                </div>
            </form>
        </div>
    </main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const inputNbColis = document.getElementById('nbColis');
    const conteneurColis = document.getElementById('conteneur-colis');

    function genererBlocsColis() {
        conteneurColis.innerHTML = '';
        const nb = parseInt(inputNbColis.value) || 1;
        
        for (let i = 1; i <= nb; i++) {
            const bloc = document.createElement('div');
            bloc.style.marginTop = '14px';
            bloc.style.padding = '14px';
            bloc.style.border = '1px solid #e0e0e0';
            bloc.style.borderRadius = '6px';
            bloc.style.backgroundColor = '#f9f9f9';
            
            bloc.innerHTML = `
                <div class="card-title" style="margin-bottom:10px; font-size: 14px;">Colis ${i}</div>
                <div class="form-grid">
                    <div>
                        <label class="f-label">Nom du colis <span class="req">*</span></label>
                        <input type="text" name="nom_colis[]" class="f-input" placeholder="Ex: Ordinateur Dell XPS" required>
                    </div>
                    <div>
                        <label class="f-label">Commentaire</label>
                        <input type="text" name="commentaire[]" class="f-input" placeholder="Ex: Bien emballé...">
                    </div>
                </div>
            `;
            conteneurColis.appendChild(bloc);
        }
    }

    inputNbColis.addEventListener('input', genererBlocsColis);
    genererBlocsColis();
});
</script>
</body>
</html>