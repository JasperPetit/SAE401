<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gérer les Rôles — Suivi Colis</title>
    <link rel="stylesheet" href="public/css/style.css">
</head>
<body class="service-admin">

    <?php require_once VIEWS . '/navbar.php'; ?>

    <main class="main">
        <div class="page-header" style="display:flex; justify-content:space-between; align-items:flex-start;">
            <div>
                <h1>Gestion des Rôles</h1>
                <p>Définissez les niveaux d'accès disponibles</p>
            </div>
            <a class="btn btn-primary" href="index.php?action=pageAjouterRole"><?= icon('plus', 14) ?> Nouveau Rôle</a>
        </div>

        <?php if (isset($_GET['success']) && $_GET['success'] == 'role'): ?>
            <div style="background-color: #d1fae5; color: #065f46; padding: 12px; border-radius: 6px; margin-bottom: 20px;">✅ Le rôle a été ajouté avec succès !</div>
        <?php endif; ?>

        <div class="card">
            <table>
                <thead>
                    <tr>
                        <th>Nom du Rôle</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($resListeRoles)): ?>
                        <?php foreach ($resListeRoles as $role): ?>
                        <tr>
                            <td style="font-weight:600; color:var(--navy);"><span class="badge badge-gray"><?= htmlspecialchars(strtoupper($role['Role'] ?? 'Inconnu')) ?></span></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td class="empty-state">Aucun rôle trouvé.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>

</body>
</html>