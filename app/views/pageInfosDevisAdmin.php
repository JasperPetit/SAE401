<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Direction - Signature Devis</title>
    <link rel="stylesheet" href="public/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php require_once VIEWS . '/navbar.php'; ?>

    <main class="main-content">
        <div class="content-header">
            <h1><i class="fas fa-signature"></i> Espace Direction - Validation</h1>
        </div>

        <input type="text" id="searchBar" onkeyup="filtrer()" class="search-box" placeholder="Rechercher un document...">

        <div class="data-card-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>N° Devis</th>
                        <th>Projet / Besoin</th>
                        <th>Département</th>
                        <th>Montant</th>
                        <th>Pièce jointe</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(!empty($listeDevis)): foreach ($listeDevis as $devi): ?>
                    <tr class="devis-row">
                        <td><strong><?= htmlspecialchars($devi['idDevis'] ?? 'N/A') ?></strong></td>
                        <td>
                            <strong><?= htmlspecialchars($devi['name'] ?? 'Non spécifié') ?></strong><br>
                            <span style="font-size: 0.85rem; color: var(--text-muted);">Fournisseur: <?= htmlspecialchars($devi['nomEntreprise'] ?? '') ?></span>
                        </td>
                        <td><?= htmlspecialchars($devi['NomDepartement'] ?? 'N/A') ?></td>
                        <td style="font-weight: bold; font-size: 1.05rem;"><?= htmlspecialchars($devi['prix'] ?? '0') ?> €</td>
                        <td>
                            <?php if (!empty($devi['imageDevis'])): ?>
                                <a href="uploads/<?= htmlspecialchars($devi['imageDevis']) ?>" target="_blank" style="color: var(--primary-blue); font-weight: bold; text-decoration: none;">
                                    <i class="fas fa-file-pdf"></i> PDF
                                </a>
                            <?php else: ?>
                                <span style="color: var(--text-muted); font-size: 0.85rem;">Aucun fichier</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <button class="btn" style="background-color: var(--primary-blue);"><i class="fas fa-pen-fancy"></i> Signer</button>
                        </td>
                    </tr>
                    <?php endforeach; else: ?>
                    <tr><td colspan="6" style="text-align:center; padding:30px; color:var(--text-muted);">Aucun document en attente de signature.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>

    <script>
        function filtrer() {
            let filter = document.getElementById('searchBar').value.toUpperCase();
            let rows = document.querySelectorAll('.devis-row');
            rows.forEach(row => {
                let txt = row.textContent || row.innerText;
                row.style.display = txt.toUpperCase().indexOf(filter) > -1 ? "" : "none";
            });
        }
    </script>
</body>
</html>