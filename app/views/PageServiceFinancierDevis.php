<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Service Financier - Validation</title>
    <link rel="stylesheet" href="public/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
    <?php require_once VIEWS . '/navbar.php'; ?>

    <main class="main">
        <div class="content-header">
            <h1>Validation Financière des Devis</h1>
        </div>

        <?php if (isset($_GET['error']) && $_GET['error'] == 'budget'): ?>
            <div style="background-color: #fee2e2; color: #991b1b; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #f87171;">
                <i class="fas fa-exclamation-triangle"></i> Erreur : Budget du département insuffisant !
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['msg']) && $_GET['msg'] == 'valide'): ?>
            <div style="background-color: #d1fae5; color: #065f46; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #10b981;">
                <i class="fas fa-check-circle"></i> Devis validé et budget débité avec succès.
            </div>
        <?php endif; ?>

        <input type="text" id="searchBar" onkeyup="filtrer()" class="search-box" placeholder="Rechercher (nom, département, fournisseur...)">

        <div class="data-card-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Département</th>
                        <th>Demandeur</th>
                        <th>Besoin / Projet</th>
                        <th>Montant</th>
                        <th>Fichier</th>
                        <th>Arbitrage</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(!empty($listeDevis)): ?>
                        <?php foreach ($listeDevis as $d): ?>
                        <tr class="commande-row">
                            <td><strong><?= htmlspecialchars($d['NomDepartement'] ?? 'N/A') ?></strong></td>
                            <td style="color: var(--text-muted);">
                                <?= htmlspecialchars($d['PrenomUtilisateur'] ?? '') ?> <?= htmlspecialchars($d['NomUtilisateur'] ?? '') ?>
                            </td>
                            <td>
                                <strong><?= htmlspecialchars($d['numeroDevis'] ?? 'Non spécifié') ?></strong><br>
                                <span style="font-size: 0.85rem; color: var(--text-muted);">Fournisseur: <?= htmlspecialchars($d['NomFournisseur'] ?? '') ?></span>
                            </td>
                            <td style="font-weight: bold; font-size: 1.05rem;"><?= htmlspecialchars($d['Prix'] ?? '0') ?> €</td>
                            <td>
                                <?php if (!empty($d['ImageDevis'])): ?>
                                    <a href="uploads/<?= htmlspecialchars($d['ImageDevis']) ?>" target="_blank" style="color: var(--primary-blue); font-weight: bold; text-decoration: none;">
                                        <i class="fas fa-file-pdf"></i> PDF
                                    </a>
                                <?php else: ?>
                                    <span style="color: var(--text-muted); font-size: 0.85rem;">Aucun</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (!isset($d['IdStatut']) || $d['IdStatut'] == 1): ?>
                                    <div style="display: flex; gap: 8px;">
                                        <a href="index.php?action=validerDevis&id=<?= htmlspecialchars($d['IdDevis']) ?>" class="btn" style="background-color: var(--success); padding: 8px 12px; font-size:0.85rem;">
                                            <i class="fas fa-check"></i> Accorder
                                        </a>
                                        <a href="index.php?action=refuserDevis&id=<?= htmlspecialchars($d['IdDevis']) ?>" class="btn" style="background-color: var(--danger); padding: 8px 12px; font-size:0.85rem;">
                                            <i class="fas fa-ban"></i> Refuser
                                        </a>
                                    </div>
                                <?php else: ?>
                                    <?php 
                                        $class = ($d['IdStatut'] == 2) ? 'badge-success' : 'badge-danger';
                                        $text = ($d['IdStatut'] == 2) ? 'VALIDÉ' : 'REFUSÉ';
                                    ?>
                                    <span class="badge <?= $class ?>"><?= $text ?></span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 30px; color: var(--text-muted);">
                                <i class="fas fa-check-circle" style="font-size: 2rem; margin-bottom: 10px; color: var(--success);"></i><br>
                                Aucun devis en attente d'arbitrage.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>

    <script>
        function filtrer() {
            let filter = document.getElementById('searchBar').value.toUpperCase();
            let rows = document.querySelectorAll('.commande-row');
            rows.forEach(row => {
                let txt = row.textContent || row.innerText;
                row.style.display = txt.toUpperCase().indexOf(filter) > -1 ? "" : "none";
            });
        }
    </script>
</body>
</html>