<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un Rôle — Suivi Colis IUT</title>
    <link rel="stylesheet" href="public/css/style.css">
</head>
<body class="service-admin">

    <?php require_once VIEWS . '/navbar.php'; ?>

    <main class="main">
        <div class="page-header">
            <h1>Créer un nouveau rôle</h1>
            <p>Définissez un nouveau niveau d'accès pour le personnel de l'IUT</p>
        </div>

        <form method="POST" action="index.php?action=ajouterRole">
            <div class="card">
                <div class="card-title" style="margin-bottom:14px;">Paramètres du rôle</div>
                
                <div class="form-grid">
                    <div>
                        <label class="f-label">Intitulé du rôle <span class="req">*</span></label>
                        <input class="f-input" name="nomRole" placeholder="Ex: Service_RH" required>
                    </div>
                </div>
            </div>

            <div class="actions-row">
                <button class="btn btn-primary" type="submit">Créer le rôle</button>
                <a class="btn btn-outline" href="index.php?action=pageVoirRoles">Annuler</a>
            </div>
        </form>
    </main>

</body>
</html>