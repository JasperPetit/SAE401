<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nouveau Fournisseur - SAE Colis</title>
    <link rel="stylesheet" href="public/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; font-weight: 600; margin-bottom: 8px; color: var(--text-dark); }
        .form-control { width: 100%; max-width: 600px; padding: 10px 15px; border: 1px solid var(--border-color); border-radius: 6px; font-size: 0.95rem; }
    </style>
</head>
<body>
    <?php require_once 'app/views/navbar.php'; ?>

    <main class="main-content">
        <div class="content-header">
            <h1><i class="fas fa-building"></i> Ajouter un nouveau Fournisseur</h1>
        </div>

        <div class="data-card-container" style="padding: 30px;">
            <form action="index.php?action=ajouterFournisseur" method="POST">
                
                <div class="form-group">
                    <label for="nom_entreprise">Nom de l'entreprise :</label>
                    <input type="text" id="nom_entreprise" name="nomEntreprise" class="form-control" placeholder="Entrez le nom..." required>
                </div>

                <div class="form-group">
                    <label for="adresse">Adresse postale :</label>
                    <input type="text" id="adresse" name="adresse" class="form-control" placeholder="Entrez l'adresse..." required>
                </div>

                <div class="form-group">
                    <label for="num_telephone">Numéro de téléphone :</label>
                    <input type="text" id="num_telephone" name="NumeroTelephone" class="form-control" placeholder="Ex: 01 23 45 67 89" required>
                </div>

                <div class="form-group">
                    <label for="mail_entrprise">Adresse mail :</label>
                    <input type="email" id="mail_entrprise" name="Mail" class="form-control" placeholder="contact@entreprise.com" required>
                </div>

                <div style="margin-top: 30px;">
                    <button type="submit" class="btn"><i class="fas fa-save"></i> Enregistrer le fournisseur</button>
                    <a href="index.php?action=afficherFournisseur" class="btn btn-blue" style="margin-left: 10px; background-color: var(--text-muted);">Annuler</a>
                </div>
            </form>
        </div>
    </main>
</body>
</html>