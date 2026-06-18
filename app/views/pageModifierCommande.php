<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supervision des Commandes — Admin</title>
    <link rel="stylesheet" href="public/css/style.css">
</head>
<body class="service-admin">

    <?php require_once VIEWS . '/navbar.php'; ?>

    <main class="main">
        <div class="page-header">
            <h1>Supervision des Commandes</h1>
            <p>Rôle ADMIN : vue globale et résolution des flux de commandes de l'IUT</p>
        </div>

        <?php if (!empty($resListeCommandes)): ?>
            <?php foreach ($resListeCommandes as $c): ?>
            <div class="card" style="padding:16px 20px;">
                <div style="display:flex; align-items:center; gap:14px; flex-wrap:wrap;">
                    <div class="list-item" style="padding:0; border:none; flex:1; min-width:220px;">
                        <div class="li-icon" style="background:#dbeafe; color:var(--blue);"><?= icon('box', 16) ?></div>
                        <div class="li-info">
                            <div class="li-title"><?= htmlspecialchars($c['NumeroBonCommande'] ?? $c['NumeroBonDeCommande'] ?? '') ?></div>
                            <div class="li-meta">
                                Fournisseur : <?= htmlspecialchars($c['NomFournisseur'] ?? '-') ?> &nbsp;·&nbsp;
                                <strong style="color:var(--navy);"><?= htmlspecialchars($c['DateAjout'] ?? '') ?></strong>
                            </div>
                        </div>
                    </div>
                    
                    <form method="POST" action="index.php?action=ModifierCommande" style="display:flex; gap:6px;">
                        <input type="hidden" name="NumeroBonDeCommande" value="<?= htmlspecialchars($c['NumeroBonCommande'] ?? $c['NumeroBonDeCommande'] ?? '') ?>">
                        <select class="f-input" name="statut" style="width:200px; padding:6px 9px; font-size:12px;">
                            <?php 
                            $statuts = ['en_cours', 'livré', 'retard'];
                            foreach ($statuts as $s): 
                                $selected = (($c['Statut'] ?? '') === $s) ? 'selected' : '';
                            ?>
                                <option value="<?= $s ?>" <?= $selected ?>><?= ucfirst($s) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <button class="btn btn-outline btn-sm" type="submit">Appliquer</button>
                    </form>
                </div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="card">
                <p class="empty-state">Aucune commande n'est actuellement enregistrée dans le système.</p>
            </div>
        <?php endif; ?>
    </main>

</body>
</html>