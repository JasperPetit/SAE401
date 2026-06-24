<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gérer les Départements — Suivi Colis</title>
    <link rel="stylesheet" href="public/css/style.css">
</head>
<body class="service-admin">

    <?php require_once VIEWS . '/navbar.php'; ?>

    <main class="main">
        <div class="page-header" style="display:flex; justify-content:space-between; align-items:flex-start;">
            <div>
                <h1>Gestion des Départements</h1>
                <p>Départements enregistrés à l'IUT</p>
            </div>
            <a class="btn btn-gold" href="index.php?action=pageAjouterDepartement"><?= icon('plus', 14) ?> Nouveau Département</a>
        </div>

        <?php if (isset($_GET['success']) && $_GET['success'] == 'dep'): ?>
            <div style="background-color: #d1fae5; color: #065f46; padding: 12px; border-radius: 6px; margin-bottom: 20px;">✅ Le département a été ajouté avec succès !</div>
        <?php endif; ?>

        <div class="card">
            <table>
                <thead>
                    <tr>
                        <th>Nom du Département</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($resListeDepartements)): ?>
                        <?php foreach ($resListeDepartements as $dep): ?>
                        <tr>
                            <td style="font-weight:600; color:var(--navy);"><?= htmlspecialchars($dep['NomDepartement'] ?? 'Inconnu') ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td class="empty-state">Aucun département trouvé.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>

</body>
</html>