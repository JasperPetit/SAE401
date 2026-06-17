<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>État des Colis — Admin</title>
    <link rel="stylesheet" href="public/css/style.css">
</head>
<body class="service-admin">

    <?php require_once VIEWS . '/navbar.php'; ?>

    <main class="main">
        <div class="page-header">
            <h1>État Général des Colis</h1>
            <p>Vue d'ensemble et traçabilité des flux de livraison de l'IUT</p>
        </div>

        <div class="card">
            <table>
                <thead>
                    <tr>
                        <th>Référence</th>
                        <th>Destinataire</th>
                        <th>Département</th>
                        <th>Date Prévue</th>
                        <th>Statut</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($resListeColis)): ?>
                        <?php foreach ($resListeColis as $colis): 
                            $statut = strtolower($colis['Statut'] ?? 'en_cours');
                            $classe_badge = 'badge-warning';
                            $statut_texte = 'En transit';

                            if ($statut == 'livré' || $statut == 'livre') {
                                $classe_badge = 'badge-success';
                                $statut_texte = 'Livré';
                            } elseif ($statut == 'retard') {
                                $classe_badge = 'badge-danger';
                                $statut_texte = 'En retard';
                            }
                        ?>
                        <tr>
                            <td style="font-weight:600; color:var(--navy);">#<?= htmlspecialchars($colis['NumeroBonCommande'] ?? '') ?></td>
                            <td><?= htmlspecialchars(($colis['Prenom'] ?? '') . ' ' . ($colis['Nom'] ?? '')) ?></td>
                            <td>
                                <span class="badge badge-blue" style="font-size:10.5px;">
                                    <?= htmlspecialchars($colis['NomDepartement'] ?? 'N/A') ?>
                                </span>
                            </td>
                            <td><?= htmlspecialchars($colis['date_arrivee_prevu'] ?? 'N/A') ?></td>
                            <td><span class="badge <?= $classe_badge ?>"><?= $statut_texte ?></span></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="empty-state">Aucun colis en transit ou enregistré.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>

</body>
</html>