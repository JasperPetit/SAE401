<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier le colis — Admin</title>
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
            <div class="card-title" style="margin-bottom:14px;">Modifier le colis n°<?= htmlspecialchars($colis['IdColis'] ?? '') ?></div>
            <form action="index.php?action=modifierColis" method="POST">
                <input type="hidden" name="idColis" value="<?= htmlspecialchars($colis['IdColis'] ?? '') ?>">

                <div class="form-grid">
                    <div style="grid-column: span 2;">
                        <label class="f-label">Nom du colis</label>
                        <input type="text" name="nom_colis" class="f-input" value="<?= htmlspecialchars($colis['nom_colis'] ?? '') ?>">
                    </div>
                </div>

                <div class="form-grid" style="margin-top:14px;">
                    <div style="grid-column: span 2;">
                        <label class="f-label">Commentaire</label>
                        <input type="text" name="commentaire" class="f-input" value="<?= htmlspecialchars($colis['Commentaire'] ?? '') ?>">
                    </div>
                </div>

                <div class="actions-row" style="margin-top:20px;">
                    <button type="submit" class="btn btn-gold"><?= icon('edit', 14) ?> Mettre à jour le colis</button>
                    <a href="index.php?action=afficherColis" class="btn btn-outline">Annuler</a>
                </div>
            </form>
        </div>
    </main>
</body>
</html>
