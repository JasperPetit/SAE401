<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un Devis - Suivi Colis</title>
    <link rel="stylesheet" href="public/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; font-weight: 600; margin-bottom: 8px; color: var(--text-dark); }
        .form-control { width: 100%; max-width: 600px; padding: 10px 15px; border: 1px solid var(--border-color); border-radius: 6px; font-size: 0.95rem; }
        .form-control:focus { outline: none; border-color: var(--primary-blue); }
        textarea.form-control { resize: vertical; }
    </style>
</head>
<body>
    <?php require_once VIEWS . '/navbar.php'; ?>

    <main class="main-content">
        <div class="content-header">
            <h1><i class="fas fa-file-invoice"></i> Ajouter un nouveau Devis</h1>
        </div>

        <?php if (isset($_GET['error']) && $_GET['error'] == 'doublon') : ?>
            <div style="background-color: #fee2e2; color: #991b1b; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #f87171;">
                <i class="fas fa-exclamation-triangle"></i> <strong>Erreur :</strong> Ce numéro de devis existe déjà ! Veuillez en choisir un autre.
            </div>
        <?php endif; ?>

        <div class="data-card-container" style="padding: 30px;">
            <form action="index.php?action=ajouter_devis" method="POST" enctype="multipart/form-data">
                
                <div class="form-group">
                    <label for="num_devis">Numéro de devis :</label>
                    <input type="text" id="num_devis" name="NumeroDevis" class="form-control" placeholder="ex: CMD-2025-001" required>
                </div>

                <div class="form-group">
                    <label for="nom">Nom du projet / besoin :</label>
                    <input type="text" id="nom" name="name" class="form-control" placeholder="ex: Renouvellement PC" required>
                </div>

                <div class="form-group">
                    <label for="prix">Prix estimé (€) :</label>
                    <input type="number" id="prix" name="prix" step="any" class="form-control" placeholder="0.00" required>
                </div>

                <div class="form-group">
                    <label for="fournisseur">Fournisseur :</label>
                    <select id="fournisseur" name="idFournisseur" class="form-control" required>
                        <option value="">-- Sélectionnez un fournisseur --</option>
                        <?php if (!empty($resFournisseurs)): ?>
                            <?php foreach ($resFournisseurs as $entreprise): ?>
                                <option value="<?= htmlspecialchars($entreprise['IdFournisseur']) ?>"><?= htmlspecialchars($entreprise['NomFournisseur']) ?></option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Fichier du devis (PDF, JPG, JPEG) :</label>
                    <input type="file" name="ImageDevis" class="form-control" accept=".pdf, .jpg, .jpeg" required>
                </div>

                <div class="form-group">
                    <label for="details">Détails sur le projet :</label>
                    <textarea id="details" name="details" rows="4" class="form-control" placeholder="Justifiez votre besoin ici..."></textarea>
                </div>

                <div style="margin-top: 30px;">
                    <button type="submit" class="btn"><i class="fas fa-save"></i> Soumettre le devis</button>
                    <a href="index.php?action=pageInfosDevis" class="btn btn-blue" style="margin-left: 10px; background-color: var(--text-muted);">Annuler</a>
                </div>
            </form>
        </div>
    </main>
</body>
</html>