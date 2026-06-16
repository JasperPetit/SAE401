<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fournisseurs - SAE Colis</title>
    <link rel="stylesheet" href="public/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php require_once VIEWS . '/navbar.php'; ?>

    <main class="main-content">
        <div class="content-header">
            <h1><i class="fas fa-handshake"></i> Registre des Fournisseurs</h1>
            <a href="index.php?action=ajouterFournisseur" class="btn"><i class="fas fa-plus"></i> Nouveau Fournisseur</a>
        </div>

        <input type="text" id="searchBar" onkeyup="filtrerFournisseurs()" class="search-box" placeholder="Rechercher une entreprise...">

        <div id="listeFournisseurs" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px;">
            <?php if (!empty($resFournisseurs)): ?>
                <?php foreach ($resFournisseurs as $fournisseur): ?>
                    <div class="stat-card section-fournisseur" style="border-top: 4px solid var(--primary-blue);">
                        <h3 style="color: var(--primary-blue); font-size: 1.2rem; margin-bottom: 15px; text-transform: none;">
                            <?= htmlspecialchars($fournisseur['NomFournisseur'] ?? 'Inconnu') ?>
                        </h3>
                        
                        <div style="color: var(--text-dark); font-size: 0.95rem; margin-bottom: 20px; line-height: 1.6;">
                            <p><i class="fas fa-envelope" style="color: var(--text-muted); width: 20px;"></i> <?= htmlspecialchars($fournisseur['Mail'] ?? 'Non renseigné') ?></p>
                            <p><i class="fas fa-phone" style="color: var(--text-muted); width: 20px;"></i> <?= htmlspecialchars($fournisseur['numeroTelephone'] ?? 'Non renseigné') ?></p>
                            <p><i class="fas fa-map-marker-alt" style="color: var(--text-muted); width: 20px;"></i> <?= htmlspecialchars($fournisseur['Adresse'] ?? 'Non renseignée') ?></p>
                        </div>

                        <div style="display: flex; gap: 10px; border-top: 1px solid var(--border-color); padding-top: 15px;">
                            <a href="index.php?action=ModifierFournisseur&modifier=<?= $fournisseur['IdFournisseur'] ?>" class="btn btn-blue" style="flex: 1; justify-content: center; font-size: 0.85rem; padding: 8px;">
                                <i class="fas fa-edit"></i> Modifier
                            </a>
                            <form action="index.php?action=SupprimerFournisseur" method="POST" style="flex: 1;" onsubmit="return confirmerSuppressionFournisseur('<?= addslashes($fournisseur['NomFournisseur']) ?>')">
                                <input type="hidden" name="id_fournisseur" value="<?= $fournisseur['IdFournisseur'] ?>">
                                <button type="submit" name="supprimer_fournisseur" class="btn" style="width: 100%; justify-content: center; font-size: 0.85rem; padding: 8px; background-color: var(--danger);">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div style="grid-column: 1 / -1; padding: 30px; text-align: center; color: var(--text-muted); background: white; border-radius: 8px; border: 1px solid var(--border-color);">
                    <p>Aucun fournisseur enregistré.</p>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <script>
        function filtrerFournisseurs() {
            let filter = document.getElementById("searchBar").value.toUpperCase();
            let cards = document.getElementsByClassName("section-fournisseur");
            for (let i = 0; i < cards.length; i++) {
                let txtValue = cards[i].innerText;
                cards[i].style.display = txtValue.toUpperCase().indexOf(filter) > -1 ? "" : "none";
            }
        }

        function confirmerSuppressionFournisseur(nomEntreprise) {
            return confirm("Êtes-vous sûr de vouloir supprimer le fournisseur '" + nomEntreprise + "' ?\n\nCette action est irréversible.");
        }
    </script>
</body>
</html>