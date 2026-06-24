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
        </div>

        <?php if (!empty($erreur)): ?>
            <div class="flash flash-error" style="margin-bottom: 20px;">
                <?= htmlspecialchars($erreur) ?>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-title" style="margin-bottom:14px;">Modifier la commande <?= htmlspecialchars($commande['NumeroBonCommande'] ?? $commande['NumeroBonDeCommande'] ?? '') ?></div>
            <form action="index.php?action=ModifierCommande" method="POST" enctype="multipart/form-data">
                
                <div class="form-grid">
                    <div>
                        <label class="f-label">Numéro de commande <span class="req">*</span></label>
                        <input type="hidden" name="ancienNumeroBonDeCommande" value="<?= htmlspecialchars($commande['NumeroBonCommande'] ?? $commande['NumeroBonDeCommande'] ?? '') ?>">
                        <input type="text" name="NumeroBonDeCommande" class="f-input" value="<?= htmlspecialchars($commande['NumeroBonCommande'] ?? $commande['NumeroBonDeCommande'] ?? '') ?>" required>
                    </div>
                    <div>
                        <label class="f-label">Date de commande <span class="req">*</span></label>
                        <input type="date" name="DateArrivee" class="f-input" value="<?= htmlspecialchars($commande['DateAjout'] ?? '') ?>" required>
                    </div>
                </div>

                <div class="form-grid" style="margin-top:14px;">
                    <div>
                        <label class="f-label">Devis associé <span class="req">*</span></label>
                        <select name="idDevis" class="f-input" required>
                            <option value="">Choisir un devis</option>
                            <?php if(!empty($listeDevis)): foreach ($listeDevis as $devis): ?>
                                <option value="<?= $devis['IdDevis'] ?>" <?= ($commande['IdDevis'] == $devis['IdDevis']) ? 'selected' : '' ?>>Devis n°<?= htmlspecialchars($devis['numeroDevis']) ?> (<?= htmlspecialchars($devis['Prix'] ?? '') ?>€)</option>
                            <?php endforeach; endif; ?>
                        </select>
                    </div>
                </div>

                <div class="form-grid" style="margin-top:14px;">
                    <div>
                        <label class="f-label">Adresse de départ (Fournisseur) <span class="req">*</span></label>
                        <input type="text" name="AdresseDepart" class="f-input" value="<?= htmlspecialchars($commande['AdresseDepart'] ?? '') ?>" required>
                    </div>
                    <div>
                        <label class="f-label">Adresse d'arrivée (IUT) <span class="req">*</span></label>
                        <input type="text" name="AdresseArivee" class="f-input" value="<?= htmlspecialchars($commande['AdresseArivee'] ?? 'IUT Villetaneuse, 99 Av. Jean Baptiste Clément, 93430 Villetaneuse') ?>" required>
                    </div>
                </div>

                <div class="form-grid" style="margin-top:14px;">
                    <div>
                        <label class="f-label">Nombre de colis attendus <span class="req">*</span></label>
                        <input type="number" name="nbColis" id="nbColis" min="1" value="<?= count($listeColisExistant ?? []) ?: 1 ?>" class="f-input" required>
                    </div>
                    <div>
                        <label class="f-label">Bon de commande (PDF/JPG)
                            <?php if (!empty($commande['ImageBonDeCommande'])): ?>
                                <small style="color:var(--gray);">(Fichier actuel : <?= htmlspecialchars($commande['ImageBonDeCommande']) ?>)</small>
                            <?php endif; ?>
                        </label>
                        <input type="file" name="ImageCommande" class="f-input" accept=".pdf, .jpg, .jpeg">
                    </div>
                </div>
                <div class="form-group">
                    <label for="details" class="f-label">Détails sur le projet :</label>
                    <textarea id="details" name="details" rows="4" class="form-control" placeholder="details ..."></textarea>
                </div>

                <div id="conteneur-colis" style="margin-top: 14px;"></div>

                <div class="actions-row" style="margin-top:20px;">
                    <button type="submit" class="btn btn-gold"><?= icon('send', 14) ?> Mettre à jour la commande</button>
                    <a href="index.php?action=afficherCommande" class="btn btn-outline">Annuler</a>
                </div>
            </form>
        </div>
    </main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const inputNbColis = document.getElementById('nbColis');
    const conteneurColis = document.getElementById('conteneur-colis');
    const colisExistants = <?= json_encode($listeColisExistant ?? []) ?>;

    function genererBlocsColis() {
        conteneurColis.innerHTML = '';
        const nb = parseInt(inputNbColis.value) || 1;
        
        for (let i = 1; i <= nb; i++) {
            const colis = colisExistants[i-1] || {nom_colis: '', Commentaire: ''};
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
                        <label class="f-label">Nom du colis</label>
                        <input type="text" name="nom_colis[]" class="f-input" value="${colis.nom_colis || ''}" placeholder="Ex: Ordinateur Dell XPS">
                    </div>
                    <div>
                        <label class="f-label">Commentaire</label>
                        <input type="text" name="commentaire[]" class="f-input" value="${colis.Commentaire || ''}" placeholder="Ex: Bien emballé...">
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