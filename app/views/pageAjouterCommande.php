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
                                <option value="<?= $devis['IdDevis'] ?>">Devis n°<?= htmlspecialchars($devis['numeroDevis']) ?> (<?= htmlspecialchars($devis['Prix'] ?? '') ?>€)</option>
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
                        <input type="number" name="nbColis" min="1" value="1" class="f-input" required>
                    </div>
                    <div>
                        <label class="f-label">Bon de commande (PDF/JPG) <span class="req">*</span></label>
                        <input type="file" name="ImageCommande" class="f-input" accept=".pdf, .jpg, .jpeg" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="details" class="f-label">Détails sur le projet :</label>
                    <textarea id="details" name="details" rows="4" class="form-control" placeholder="details ..."></textarea>
                </div>

                <div class="actions-row" style="margin-top:20px;">
                    <button type="submit" class="btn btn-gold"><?= icon('send', 14) ?> Enregistrer la commande</button>
                    <a href="index.php?action=ajouterCommande" class="btn btn-outline">Annuler</a>
                </div>
            </form>
        </div>
    </main>

</body>
</html>