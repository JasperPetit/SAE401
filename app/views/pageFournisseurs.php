<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registre Fournisseurs — Admin</title>
    <link rel="stylesheet" href="public/css/style.css">
</head>
<body class="service-admin">

    <?php require_once VIEWS . '/navbar.php'; ?>

    <main class="main">
        <div class="page-header" style="display:flex; justify-content:space-between; align-items:flex-start;">
            <div>
                <h1>Registre des Fournisseurs</h1>
                <p>Prestataires et entreprises référencés pour les achats de l'IUT</p>
            </div>
            <a class="btn btn-gold" href="index.php?action=ajouterFournisseur"><?= icon('plus', 14) ?> Ajouter un fournisseur</a>
        </div>

        <div class="fournisseur-grid">
            <?php if (!empty($resFournisseurs)): ?>
                <?php foreach ($resFournisseurs as $f): ?>
                <div class="fournisseur-card">
                    <div class="four-header">
                        <div>
                            <div class="four-name"><?= htmlspecialchars($f['NomFournisseur'] ?? 'Inconnu') ?></div>
                            <div style="font-size:11.5px; color:var(--text-light);"><?= htmlspecialchars($f['NomCategorie'] ?? 'Fournitures / Services') ?></div>
                        </div>
                    </div>
                    
                    <div class="four-info"><?= icon('phone', 13) ?> <?= htmlspecialchars($f['numeroTelephone'] ?? '-') ?></div>
                    <div class="four-info"><?= icon('mail', 13) ?> <?= htmlspecialchars($f['Mail'] ?? '-') ?></div>
                    <div class="four-info"><?= icon('pin', 13) ?> <?= htmlspecialchars($f['Adresse'] ?? '-') ?></div>
                    
                    <div class="four-footer">
                        <div style="display:flex; gap:6px; width: 100%;">
                            <a class="btn btn-outline btn-sm" href="index.php?action=ModifierFournisseur&modifier=<?= $f['IdFournisseur'] ?>" style="flex:1; justify-content:center;">Modifier</a>
                            <form action="index.php?action=SupprimerFournisseur" method="POST" style="flex:1;" onsubmit="return confirm('Supprimer ce fournisseur ?');">
                                <input type="hidden" name="id_fournisseur" value="<?= $f['IdFournisseur'] ?>">
                                <button type="submit" name="supprimer_fournisseur" class="btn btn-outline btn-sm" style="width:100%; justify-content:center; color:var(--red); border-color:var(--border);">Supprimer</button>
                            </form>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="empty-state" style="grid-column: span 2;">Aucun fournisseur enregistré.</p>
            <?php endif; ?>
        </div>
    </main>

</body>
</html>