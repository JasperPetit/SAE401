<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter Commande - Suivi Colis</title>
    <link rel="stylesheet" href="public/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; font-weight: 600; margin-bottom: 8px; color: var(--text-dark); }
        .form-control { width: 100%; max-width: 600px; padding: 10px 15px; border: 1px solid var(--border-color); border-radius: 6px; }
    </style>
</head>
<body>
    <?php require_once 'app/views/navbar.php'; ?>

    <main class="main-content">
        <div class="content-header">
            <h1><i class="fas fa-plus-circle"></i> Enregistrer une Commande</h1>
        </div>

        <?php if (!empty($erreur)): ?>
            <div style="background-color: #fee2e2; color: #991b1b; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #f87171;">
                <i class="fas fa-exclamation-triangle"></i> <?= htmlspecialchars($erreur) ?>
            </div>
        <?php endif; ?>

        <div class="data-card-container" style="padding: 30px;">
            <form action="index.php?action=AjouterCommande" method="POST" enctype="multipart/form-data">
                
                <div class="form-group">
                    <label>Numéro de commande :</label>
                    <input type="text" name="NumeroBonDeCommande" class="form-control" placeholder="ex: CMD-2025-001" required>
                </div>
                
                <div class="form-group">
                    <label>Sélectionner le devis associé :</label>
                    <select name="idDevis" class="form-control" required>
                        <option value="">Choisir un devis</option>
                        <?php if(!empty($listeDevis)): foreach ($listeDevis as $devis): ?>
                            <option value="<?= $devis['idDevis'] ?>">Devis n°<?= htmlspecialchars($devis['idDevis']) ?></option>
                        <?php endforeach; endif; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Date de commande :</label>
                    <input type="date" name="DateArrivee" class="form-control" required>
                </div>

                <div class="form-group">
                    <label>Nombre de colis attendus :</label>
                    <input type="number" name="nbColis" min="1" value="1" class="form-control" required>
                </div>

                <div class="form-group">
                    <label>Fichier du bon de commande (PDF/JPG) :</label>
                    <input type="file" name="ImageCommande" class="form-control" accept=".pdf, .jpg, .jpeg" required>
                </div>

                <div class="form-group">
                    <label>Adresse de départ (Fournisseur) :</label>
                    <input type="text" name="AdresseDepart" class="form-control" required>
                </div>

                <div class="form-group">
                    <label>Adresse d'arrivée (IUT) :</label>
                    <input type="text" name="AdresseArivee" class="form-control" required>
                </div>

                <div style="margin-top: 30px;">
                    <button type="submit" class="btn"><i class="fas fa-save"></i> Enregistrer</button>
                    <a href="index.php?action=afficherCommande" class="btn btn-blue" style="margin-left: 10px; background-color: var(--text-muted);">Annuler</a>
                </div>
            </form>
        </div>
    </main>
</body>
</html>