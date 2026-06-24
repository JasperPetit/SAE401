<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un Utilisateur — Suivi Colis IUT</title>
    <link rel="stylesheet" href="public/css/style.css">
</head>
<body class="service-admin">

    <?php require_once VIEWS . '/navbar.php'; ?>

    <main class="main">
        <div class="page-header">
            <h1>Créer un nouvel accès</h1>
            <p>Enregistrez un nouveau membre du personnel et affectez-lui un rôle spécifique</p>
        </div>

        <?php if (!empty($erreur)): ?>
            <div class="flash flash-error" style="margin-bottom: 20px;">
                <?= htmlspecialchars($erreur) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="index.php?action=ajouterUtilisateur">
            <div class="card">
                <div class="card-title" style="margin-bottom:14px;">Informations d'identité</div>
                
                <div class="form-grid">
                    <div>
                        <label class="f-label">Prénom <span class="req">*</span></label>
                        <input class="f-input" name="prenom" placeholder="Ex: Jean" required>
                    </div>
                    <div>
                        <label class="f-label">Nom <span class="req">*</span></label>
                        <input class="f-input" name="nom" placeholder="Ex: Dupont" required>
                    </div>
                </div>

                <div class="form-grid" style="margin-top:14px;">
                    <div>
                        <label class="f-label">Adresse Email <span class="req">*</span></label>
                        <input type="email" class="f-input" name="email" placeholder="Ex: jean.dupont@iut.fr" required>
                    </div>
                    <div>
                        <label class="f-label">Mot de passe provisoire <span class="req">*</span></label>
                        <input type="password" name="mdpCAS" class="f-input" placeholder="Mot de passe sécurisé" required>
                    </div>
                </div>

                <div class="form-grid" style="margin-top:14px;">
                    <div>
                        <label class="f-label">Rôle principal <span class="req">*</span></label>
                        <select id="role" name="Role" class="f-input" onchange="afficherDepartement()" required>
                            <option value="">-- Sélectionnez un rôle --</option>
                            <?php if(!empty($listeRoles)): foreach($listeRoles as $r): ?>
                            <option value="<?= htmlspecialchars($r['IdRole'] ?? $r['Role']) ?>">
                            <?= htmlspecialchars($r['Role']) ?>
                            </option>
                            <?php endforeach; endif; ?>
                        </select>
                    </div>
                    <div>
                        <!-- Espace vide pour garder la grille alignée si besoin, ou on peut le laisser vide -->
                    </div>
                </div>

                <div id="div-departement" style="display: none; margin-top:14px;">
                    <label class="f-label">Département (Requis pour un Demandeur) <span class="req">*</span></label>
                    <select id="departement" name="departement" class="f-input">
                        <option value="">-- Sélectionnez un département --</option>
                        <?php if(!empty($ListeDepartement)): foreach($ListeDepartement as $dep): ?>
                            <option value="<?= htmlspecialchars($dep['IdDepartement']) ?>">
                                <?= htmlspecialchars($dep['NomDepartement']) ?>
                            </option>
                        <?php endforeach; endif; ?>
                    </select>
                </div>
            </div>

            <div class="actions-row">
                <button class="btn btn-gold" type="submit"><?= icon('send', 14) ?> Créer le compte utilisateur</button>
                <a class="btn btn-outline" href="index.php?action=pageVoirUtilisateurs">Annuler</a>
            </div>
        </form>
    </main>

    <script>
        function afficherDepartement() {
        var roleSelect = document.getElementById("role");
        var divDepartement = document.getElementById("div-departement");
        var selectDepartement = document.getElementById("departement");

       
        var selectedText = roleSelect.options[roleSelect.selectedIndex].text.toLowerCase();

        if (selectedText.includes("demandeur")) {
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