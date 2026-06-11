<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Devis - Espace Demandeur</title>
    <link rel="stylesheet" href="public/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php require_once VIEWS . '/navbar.php'; ?>

    <main class="main-content">
        <div class="content-header">
            <h1>Mes Demandes de Devis & Projets</h1>
            <a href="index.php?action=formulaireDevis" class="btn"><i class="fas fa-plus-circle"></i> Déposer un nouveau devis</a>
        </div>

        <input type="text" id="searchBar" onkeyup="filtrerCommandes()" class="search-box" placeholder="Rechercher un projet par nom...">

        <div id="listeDevis" style="display: flex; flex-direction: column; gap: 15px;">
            <?php if (!empty($listeDevis)): ?>
                <?php foreach ($listeDevis as $devi) { ?>
                    
                    <div class="stat-card" style="display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; border-left: 4px solid var(--primary-blue); border-top: 1px solid var(--border-color); padding: 20px;">
                        
                        <div class="commande-info" style="flex: 1; min-width: 300px;">
                            <h3 style="color: var(--primary-blue); font-size: 1.15rem; font-weight: bold; margin-bottom: 8px;">
                                <?= htmlspecialchars($devi['name'] ?? 'Devis n°'.$devi['idDevis']) ?>
                            </h3>
                            <p style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 5px;">
                                <i class="fas fa-hashtag"></i> Réf : <?= htmlspecialchars($devi['idDevis'] ?? 'N/A') ?> | 
                                <i class="fas fa-building"></i> Fournisseur : <strong><?= htmlspecialchars($devi['nomEntreprise'] ?? 'Non spécifié') ?></strong>
                            </p>
                            <p style="font-size: 0.95rem; color: var(--text-dark);">
                                Estimation : <strong><?= htmlspecialchars($devi['prix'] ?? '0') ?> €</strong> 
                                <span style="color: var(--text-muted); margin-left: 15px;"><i class="far fa-calendar-alt"></i> <?= htmlspecialchars($devi['Date_'] ?? 'Inconnue') ?></span>
                            </p>
                        </div>
                        
                        <div style="display: flex; flex-direction: column; align-items: flex-end; gap: 10px;">
                            <?php 
                                // Gestion dynamique des couleurs de badges selon le statut
                                $statut = $devi['StatutDevis'] ?? 'En attente';
                                $badgeClass = 'badge-warning'; // Orange par défaut
                                if (stripos($statut, 'validé') !== false || stripos($statut, 'accepté') !== false) $badgeClass = 'badge-success';
                                if (stripos($statut, 'refusé') !== false) $badgeClass = 'badge-danger';
                            ?>
                            <span class="badge <?= $badgeClass ?>"><?= htmlspecialchars($statut) ?></span>
                            
                            <div style="display: flex; gap: 8px;">
                                <?php if (!empty($devi['imageDevis'])): ?>
                                    <a href="uploads/<?= htmlspecialchars($devi['imageDevis']) ?>" target="_blank" class="btn btn-blue" style="padding: 6px 12px; font-size: 0.8rem;">
                                        <i class="fas fa-file-pdf"></i> PDF
                                    </a>
                                <?php endif; ?>
                                <button onclick="toggleDetails(<?= htmlspecialchars($devi['idDevis']) ?>)" class="btn btn-blue" style="padding: 6px 12px; font-size: 0.8rem; background-color: var(--secondary-blue);">
                                    <i class="fas fa-eye"></i> Détails
                                </button>
                            </div>
                        </div>
                        
                        <div id="details-<?= htmlspecialchars($devi['idDevis']) ?>" style="display: none; width: 100%; margin-top: 15px; padding-top: 15px; border-top: 1px solid var(--border-color); color: var(--text-dark);">
                            <strong>Description du besoin :</strong><br>
                            <?= nl2br(htmlspecialchars($devi['details'] ?? 'Aucun détail fourni.')) ?>
                        </div>

                    </div>
                <?php } ?>
            <?php else: ?>
                <div style="padding: 30px; text-align: center; color: var(--text-muted); background: white; border-radius: 8px; border: 1px solid var(--border-color);">
                    <i class="fas fa-folder-open" style="font-size: 2.5rem; margin-bottom: 10px; color: #cbd5e1;"></i>
                    <p>Aucun devis n'est enregistré pour le moment.</p>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <script>
        function filtrerCommandes() {
            var input = document.getElementById("searchBar");
            var filter = input.value.toUpperCase();
            var container = document.getElementById("listeDevis");
            var cards = container.getElementsByClassName("stat-card");

            for (var i = 0; i < cards.length; i++) {
                var h3 = cards[i].getElementsByTagName("h3")[0];
                var txtValue = h3.textContent || h3.innerText;
                cards[i].style.display = txtValue.toUpperCase().indexOf(filter) > -1 ? "flex" : "none";
            }
        }

        function toggleDetails(id) {
            var div = document.getElementById('details-' + id);
            if (div.style.display === "none") {
                div.style.display = "block";
            } else {
                div.style.display = "none";
            }
        }
    </script>
</body>
</html>