<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des utilisateurs - Admin</title>
    <link rel="stylesheet" href="public/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php require_once 'app/views/navbar.php'; ?>

    <main class="main-content">
        <div class="content-header">
            <h1><i class="fas fa-users-cog"></i> Liste des Utilisateurs</h1>
            <div>
                <a href="index.php?action=pageAdmin" class="btn btn-blue" style="background-color: var(--text-muted); margin-right: 10px;"><i class="fas fa-arrow-left"></i> Retour</a>
                <a href="index.php?action=pageAjouterUtilisateur" class="btn"><i class="fas fa-user-plus"></i> Nouvel utilisateur</a>
            </div>
        </div>

        <?php if (isset($_GET['success'])): ?>
            <div style="background-color: #d1fae5; color: #065f46; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #10b981;">
                <i class="fas fa-check-circle"></i> L'utilisateur a été supprimé avec succès.
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['error'])): ?>
            <div style="background-color: #fee2e2; color: #991b1b; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #f87171;">
                <i class="fas fa-exclamation-triangle"></i> <?= htmlspecialchars($_GET['error']) ?>
            </div>
        <?php endif; ?>

        <input type="text" id="searchBar" onkeyup="filtrerUsers()" class="search-box" placeholder="Rechercher par nom, rôle ou identifiant...">

        <div class="data-card-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Identifiant CAS</th>
                        <th>Nom Complet</th>
                        <th>Rôle(s)</th>
                        <th>Département</th>
                        <th style="text-align: right;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(!empty($resListeUtilisateurs)): foreach ($resListeUtilisateurs as $utilisateur): ?>
                    <tr class="user-row">
                        <td><strong><?= htmlspecialchars($utilisateur['identifiantCAS'] ?? $utilisateur['Identifiant'] ?? '') ?></strong></td>
                        <td><?= htmlspecialchars($utilisateur['Prenom'] ?? '') ?> <span style="text-transform: uppercase;"><?= htmlspecialchars($utilisateur['Nom'] ?? '') ?></span></td>
                        <td><span class="badge badge-warning" style="background-color: rgba(30,58,95,0.1); color: var(--primary-blue);"><?= htmlspecialchars($utilisateur['Roles'] ?? 'Aucun') ?></span></td>
                        <td><?= htmlspecialchars($utilisateur['nomDepartement'] ?? 'N/A') ?></td>
                        <td style="text-align: right;">
                            <form action="index.php?action=SupprimerUtilisateur" method="POST" style="display: inline;" onsubmit="return confirmerSuppression('<?= addslashes($utilisateur['Nom'] ?? '') ?>')">
                                <input type="hidden" name="id_utilisateur" value="<?= htmlspecialchars($utilisateur['identifiantCAS'] ?? $utilisateur['IdUtilisateur'] ?? '') ?>">
                                <button type="submit" name="supprimer" class="btn" style="background-color: var(--danger); padding: 6px 12px; font-size: 0.85rem;">
                                    <i class="fas fa-trash-alt"></i> Supprimer
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; else: ?>
                    <tr><td colspan="5" style="text-align:center; padding:20px;">Aucun utilisateur trouvé.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>

    <script>
        function confirmerSuppression(nomAttendu) {
            var saisie = prompt("Veuillez saisir le nom de l'utilisateur (" + nomAttendu.toUpperCase() + ") pour confirmer la suppression :");
            if (saisie === null) return false; 
            if (saisie.trim().toUpperCase() === nomAttendu.toUpperCase()) return true; 
            alert("Le nom ne correspond pas. Suppression annulée.");
            return false; 
        }

        function filtrerUsers() {
            let filter = document.getElementById('searchBar').value.toUpperCase();
            let rows = document.querySelectorAll('.user-row');
            rows.forEach(row => {
                let txt = row.textContent || row.innerText;
                row.style.display = txt.toUpperCase().indexOf(filter) > -1 ? "" : "none";
            });
        }
    </script>
</body>
</html>