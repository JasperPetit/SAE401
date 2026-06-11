<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Suivi Colis USPN</title>
    <link rel="stylesheet" href="public/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="login-body">

    <div class="login-container">
        <div class="login-header">
            <img src="public/images/logo.jpeg" alt="Logo Université">
            <h1>Ouvrir une session</h1>
            <p>Université Sorbonne Paris Nord</p>
        </div>

        <div class="login-content">
            <?php if (!empty($erreur)): ?>
                <div class="alert-error">
                    <i class="fas fa-circle-exclamation"></i>
                    <span><?= htmlspecialchars($erreur) ?></span>
                </div>
            <?php endif; ?>

            <form method="POST" action="index.php?action=connexion">
                <div class="login-form-group">
                    <label for="identifiant">Identifiant CAS</label>
                    <input type="text" id="identifiant" name="identifiant" placeholder="Entrez votre identifiant" required>
                </div>

                <div class="login-form-group">
                    <label for="mot_de_passe">Mot de passe</label>
                    <input type="password" id="mot_de_passe" name="mot_de_passe" placeholder="Entrez votre mot de passe" required>
                </div>

                <button type="submit" class="btn" style="width: 100%; justify-content: center; padding: 12px;">
                    <i class="fas fa-sign-in-alt"></i> Se connecter
                </button>
            </form>
        </div>
    </div>

</body>
</html>