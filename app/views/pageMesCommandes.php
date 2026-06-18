<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Commandes - Suivi Colis</title>
    <link rel="stylesheet" href="public/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
    <?php require_once VIEWS . '/navbar.php'; ?>

    <main class="main">
        <div class="content-header">
            <h1>Suivi de mes Commandes</h1>
            <a href="index.php?action=AjouterCommande" class="btn btn-gold"><?= icon('plus', 14) ?> Nouvelle commande</a>
        </div>

        <div class="search-row">
            <div class="search-wrap">
                <?= icon('search') ?>
                <input type="text" id="searchBar" onkeyup="filtrerCommandes()" class="search-input" placeholder="Rechercher une commande par numéro, adresse...">
            </div>
        </div>
        
        <div style="margin-bottom: 15px; font-size: 12px; font-weight: 600; color: var(--text-light);" id="compteurResultats"></div>

        <div class="card" style="padding: 0; overflow: hidden;">
            <table>
                <thead>
                    <tr>
                        <th>N° Bon Commande</th>
                        <th>Adresse de Départ</th>
                        <th>Adresse d'Arrivée</th>
                        <th>Date d'Ajout</th>
                        <th>Statut Livraison</th>
                        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'Service_Postal'): ?>
                        <th>Action</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody id="listeCommandes">
                    <?php if (!empty($resListeCommandes)): ?>
                        <?php foreach ($resListeCommandes as $commande): ?>
                            <?php 
                                // Correspondance dynamique des badges selon ton modèle
                                $statut = strtolower($commande['Statut'] ?? 'en_cours');
                                $badgeClass = 'badge-warning';
                                $texteStatut = 'En cours';
                                
                                if ($statut === 'livré' || $statut === 'livre') {
                                    $badgeClass = 'badge-success';
                                    $texteStatut = 'Livré';
                                } elseif ($statut === 'retard') {
                                    $badgeClass = 'badge-danger';
                                    $texteStatut = 'En retard';
                                }
                            ?>
                            <tr class="commande-row" 
                                data-date="<?= htmlspecialchars($commande['DateAjout'] ?? '') ?>" 
                                data-statut="<?= htmlspecialchars($statut) ?>">
                                
                                <td><strong>#<?= htmlspecialchars($commande['NumeroBonCommande'] ?? $commande['NumeroBonDeCommande'] ?? 'Inconnu') ?></strong></td>
                                <td style="color: var(--text-muted);"><?= htmlspecialchars($commande['AdresseDepart'] ?? 'Non spécifiée') ?></td>
                                <td><i class="fas fa-location-dot" style="color: var(--primary-blue); margin-right: 5px;"></i> <?= htmlspecialchars($commande['AdresseArivee'] ?? '') ?></td>
                                <td><?= htmlspecialchars($commande['DateAjout'] ?? 'N/A') ?></td>
                                <td><span class="badge <?= $badgeClass ?>"><?= $texteStatut ?></span></td>
                                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'Service_Postal'): ?>
                                <td>
                                    <?php if ($statut !== 'livré' && $statut !== 'livre'): ?>
                                    <form action="index.php?action=validerLivraisonCommande" method="POST" style="display:inline;">
                                        <input type="hidden" name="NumeroBonCommande" value="<?= htmlspecialchars($commande['NumeroBonCommande'] ?? $commande['NumeroBonDeCommande'] ?? '') ?>">
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
                            <td colspan="5" style="text-align: center; padding: 30px; color: var(--text-muted);">
                                <i class="fas fa-box-open" style="font-size: 2rem; margin-bottom: 10px;"></i><br>
                                Aucune commande enregistrée pour le moment.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>

    <script>
        function filtrerCommandes() {
            const input = document.getElementById("searchBar");
            const filter = input.value.toUpperCase();
            const rows = document.querySelectorAll(".commande-row");
            let compteur = 0;

            rows.forEach(row => {
                const text = row.textContent || row.innerText;
                if (text.toUpperCase().indexOf(filter) > -1) {
                    row.style.display = "";
                    compteur++;
                } else {
                    row.style.display = "none";
                }
            });

            document.getElementById("compteurResultats").textContent = compteur + " commande(s) trouvée(s)";
        }

        // Exécution automatique au chargement
        window.onload = function() {
            const rows = document.querySelectorAll(".commande-row").length;
            document.getElementById("compteurResultats").textContent = rows + " commande(s) trouvée(s)";
        };
    </script>
</body>
</html>