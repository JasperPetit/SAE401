<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Suivi des colis - Service Postal</title>
    <link rel="stylesheet" href="public/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php require_once VIEWS . '/navbar.php'; ?>

    <main class="main-content">
        <div class="content-header">
            <h1><i class="fas fa-box-open"></i> Suivi des Colis en cours</h1>
        </div>

        <input type="text" id="searchBar" onkeyup="filtrerColis()" class="search-box" placeholder="Rechercher par n° de bon, nom ou destination...">

        <div class="data-card-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>N° Bon de Commande</th>
                        <th>Destinataire</th>
                        <th>Adresse / Bureau</th>
                        <th>Département</th>
                        <th>Date d'arrivée</th>
                        <th>Statut</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(!empty($resListeColis)): ?>
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
                        <tr class="colis-row">
                            <td><strong><?= htmlspecialchars($colis['NumeroBonDeCommande'] ?? '') ?></strong></td>
                            <td style="color: var(--text-muted);"><?= htmlspecialchars($colis['Prenom'] ?? '') . ' ' . htmlspecialchars($colis['Nom'] ?? '') ?></td>
                            <td><i class="fas fa-location-dot" style="color: var(--primary-blue);"></i> <?= htmlspecialchars($colis['AdresseArivee'] ?? '') ?></td>
                            <td><?= htmlspecialchars($colis['nomDepartement'] ?? '') ?></td>
                            <td><?= htmlspecialchars($colis['Date_'] ?? '') ?></td>
                            <td><span class="badge <?= $classe_badge ?>"><?= $statut_texte ?></span></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 20px; color: var(--text-muted);">Aucun colis en transit actuellement.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>

    <script>
        function filtrerColis() {
            let filter = document.getElementById("searchBar").value.toUpperCase();
            let rows = document.querySelectorAll(".colis-row");
            rows.forEach(row => {
                let txt = row.textContent || row.innerText;
                row.style.display = txt.toUpperCase().indexOf(filter) > -1 ? "" : "none";
            });
        }
    </script>
</body>
</html>