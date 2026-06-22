<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Utilisateurs — Suivi Colis IUT</title>
    <link rel="stylesheet" href="public/css/style.css">
</head>
<body class="service-admin">

    <?php require_once VIEWS . '/navbar.php'; ?>

    <main class="main">
        <div class="page-header" style="display:flex; justify-content:space-between; align-items:flex-start;">
            <div>
                <h1>Gestion des Utilisateurs</h1>
                <p>Comptes utilisateurs enregistrés et droits d'accès associés</p>
            </div>
            <a class="btn btn-gold" href="index.php?action=pageAjouterUtilisateur"><?= icon('plus', 14) ?> Nouvel utilisateur</a>
        </div>

        <div class="card">
            <table>
                <thead>
                    <tr>
                        <th>Nom du role</th>
                        <th>Identifiant / CAS</th>
                        <th>Nombre de personne</th>
                        <th style="text-align:right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($resListeUtilisateurs)): ?>
                        <?php foreach ($resListeUtilisateurs as $u): ?>
                        <tr>
                            <td style="font-weight:600; color:var(--navy);"><?= htmlspecialchars(($u['Prenom'] ?? '') . ' ' . ($u['Nom'] ?? '')) ?></td>
                            <td><?= htmlspecialchars($u['Identifiant'] ?? 'cas@uspn.fr') ?></td>
                            <td><span class="badge badge-gray"><?= htmlspecialchars(strtoupper($u['Roles'] ?? 'Demandeur')) ?></span></td>
                            <td style="text-align:right;">
                                <form method="POST" action="index.php?action=SupprimerUtilisateur" style="display:inline;" onsubmit="return confirm('Supprimer cet utilisateur ?');">
                                    <input type="hidden" name="id_utilisateur" value="<?= $u['IdUtilisateur'] ?>">
                                    <button type="submit" name="supprimer" class="btn btn-outline btn-sm" style="color:var(--red); border-color:var(--border); cursor:pointer;">
                                       <?= icon('trash', 12) ?> Supprimer
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="empty-state">Aucun utilisateur enregistré pour le moment.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>

</body>
</html>