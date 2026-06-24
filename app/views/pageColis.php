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
                        <th>Nom du colis</th>
                        <th>Destinataire</th>
                        <th>Département</th>
                        <th>Date Prévue</th>
                        <th>Statut</th>
                        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'Service_Postal'): ?>
                        <th>Action</th>
                        <?php endif; ?>
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
                            <td><?= htmlspecialchars($colis['nom_colis'] ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars(($colis['Prenom'] ?? '') . ' ' . ($colis['Nom'] ?? '')) ?></td>
                            <td>
                                <span class="badge badge-blue" style="font-size:10.5px;">
                                    <?= htmlspecialchars($colis['NomDepartement'] ?? 'N/A') ?>
                                </span>
                            </td>
                            <td><?= htmlspecialchars($colis['date_arrivee_prevu'] ?? 'N/A') ?></td>
                            <td><span class="badge <?= $classe_badge ?>"><?= $statut_texte ?></span></td>
                            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'Service_Postal'): ?>
                            <td>
                                <?php if ($statut !== 'livré' && $statut !== 'livre'): ?>
                                <form action="index.php?action=validerLivraison" method="POST" style="display:inline;">
                                    <input type="hidden" name="id" value="<?= htmlspecialchars($colis['IdColis'] ?? '') ?>">
                                    <input type="hidden" name="idCommande" value="<?= htmlspecialchars($colis['NumeroBonCommande'] ?? '') ?>">
                                    <button type="submit" class="btn btn-green" style="padding: 4px 8px; font-size: 0.8rem; background-color: #10b981; border-color: #10b981; color: white;">
                                        <i class="fas fa-check"></i> Livrer
                                    </button>
                                </form>
                                <?php endif; ?>
                            </td>
                            <?php endif; ?>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="empty-state">Aucun colis en transit ou enregistré.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>

</body>
</html>