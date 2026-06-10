<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un Utilisateur - Admin</title>
    <link rel="stylesheet" href="public/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; font-weight: 600; margin-bottom: 8px; color: var(--text-dark); }
        .form-control { width: 100%; max-width: 600px; padding: 10px 15px; border: 1px solid var(--border-color); border-radius: 6px; }
    </style>
</head>
<body>
    <?php require_once 'app/views/navbar.php'; ?>

    <main class="main-content">
        <div class="content-header">
            <h1><i class="fas fa-user-plus"></i> Nouvel Utilisateur</h1>
        </div>

        <?php if (isset($erreur) && $erreur): ?>
            <div style="background-color: #fee2e2; color: #991b1b; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #f87171;">
                <i class="fas fa-exclamation-triangle"></i> <?= htmlspecialchars($erreur) ?>
            </div>
        <?php endif; ?>

        <div class="data-card-container" style="padding: 30px;">
            <form method="POST" action="index.php?action=ajouterUtilisateur">
                <div class="form-group">
                    <label for="prenom">Prénom :</label>
                    <input type="text" id="prenom" name="prenom" class="form-control" placeholder="Ex: Jean" required>
                </div>

                <div class="form-group">
                    <label for="nom">Nom :</label>
                    <input type="text" id="nom" name="nom" class="form-control" placeholder="Ex: Dupont" required>
                </div>

                <div class="form-group">
                    <label for="role">Rôle principal :</label>
                    <select id="role" name="Role[]" class="form-control" onchange="afficherDepartement()" required>
                        <option value="">-- Sélectionnez un rôle --</option>
                        <option value="ADMIN">Administrateur</option>
                        <option value="Demandeur">Demandeur (Professeur / Département)</option>
                        <option value="Service_Postal">Service Postal (Logistique)</option>
                        <option value="Service_Financier">Service Financier</option>
                        <option value="Direction">Direction</option>
                    </select>
                </div>

                <div class="form-group" id="div-departement" style="display: none;">
                    <label for="departement">Département (Requis pour un Demandeur) :</label>
                    <select id="departement" name="departement[]" class="form-control">
                        <option value="">-- Sélectionnez un département --</option>
                        <?php if(!empty($ListeDepartement)): foreach($ListeDepartement as $dep): ?>
                            <option value="<?= htmlspecialchars($dep['IdDepartement'] ?? $dep['nomDepartement'] ?? '') ?>">
                                <?= htmlspecialchars($dep['nomDepartement'] ?? $dep['IdDepartement'] ?? '') ?>
                            </option>
                        <?php endforeach; else: ?>
                            <option value="Informatique">Informatique</option>
                            <option value="GEII">GEII</option>
                            <option value="Génie Mécanique">Génie Mécanique</option>
                        <?php endif; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="mdpCAS">Mot de passe provisoire :</label>
                    <input type="password" id="mdpCAS" name="mdpCAS" class="form-control" placeholder="Mot de passe sécurisé" required>
                </div>

                <div style="margin-top: 30px;">
                    <button type="submit" class="btn"><i class="fas fa-save"></i> Créer le compte</button>
                    <a href="index.php?action=pageVoirUtilisateurs" class="btn btn-blue" style="margin-left: 10px; background-color: var(--text-muted);">Annuler</a>
                </div>
            </form>
        </div>
    </main>

    <script>
        function afficherDepartement() {
            var roleSelect = document.getElementById("role");
            var divDepartement = document.getElementById("div-departement");
            var selectDepartement = document.getElementById("departement");

            if (roleSelect.value === "Demandeur") {
                divDepartement.style.display = "block";
                selectDepartement.required = true;
            } else {
                divDepartement.style.display = "none";
                selectDepartement.required = false;
                selectDepartement.value = "";
            }
        }
    </script>
</body>
</html>