<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Réimpression Étiquette - Service Postal</title>
    <link rel="stylesheet" href="public/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php require_once VIEWS . '/navbar.php'; ?>

    <main class="main-content">
        <div class="content-header" style="justify-content: flex-start; gap: 20px;">
            <h1><i class="fas fa-print"></i> Édition d'Étiquettes</h1>
            <?php @include 'app/views/rechercheColis.php'; ?>
        </div>

        <?php if (!empty($message)): ?>
            <div style="background-color: #fee2e2; color: #991b1b; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                <i class="fas fa-info-circle"></i> <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <div class="data-card-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>N° Commande</th>
                        <th>Destination</th>
                        <th>Date</th>
                        <th>Infos Colis</th>
                        <th style="text-align: right;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(!empty($resultat)): foreach ($resultat as $res): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($res['NumeroBonCommande'] ?? '') ?></strong></td>
                        <td style="color: var(--text-muted);"><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($res['AdresseArivee']) ?></td>
                        <td><?= htmlspecialchars($res['DateAjout'] ?? '') ?></td>
                        <td><span class="badge badge-success"><?= htmlspecialchars($res['Poids'] ?? 'N/A') ?> kg</span></td>
                        <td style="text-align: right;">
                            <a href="index.php?action=imprimer&id=<?= htmlspecialchars($res['NumeroBonCommande'] ?? '') ?>" target="_blank" class="btn btn-blue" style="padding: 6px 12px; font-size: 0.85rem;">
                                <i class="fas fa-print"></i> Imprimer
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; else: ?>
                    <tr><td colspan="5" style="text-align:center; padding:20px;">Aucun colis trouvé.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>