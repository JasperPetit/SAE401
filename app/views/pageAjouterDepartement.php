<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un Département — Suivi Colis IUT</title>
    <link rel="stylesheet" href="public/css/style.css">
</head>
<body class="service-admin">

    <?php require_once VIEWS . '/navbar.php'; ?>

    <main class="main">
        <div class="page-header">
            <h1>Créer un Département</h1>
            <p>Enregistrez un nouveau département pour référencer les demandes de l'IUT</p>
        </div>

        <form method="POST" action="index.php?action=ajouterDepartement">
            <div class="card">
                <div class="card-title" style="margin-bottom:14px;">Informations du département</div>
                
                <div class="form-grid">
                    <div>
                        <label class="f-label">Nom du département <span class="req">*</span></label>
                        <input class="f-input" name="nomDepartement" placeholder="Ex: Informatique" required>
                    </div>
                </div>
            </div>

            <div class="actions-row">
                <button class="btn btn-gold" type="submit">Créer le département</button>
                <a class="btn btn-outline" href="index.php?action=pageAdmin">Annuler</a>
            </div>
        </form>
    </main>

</body>
</html>