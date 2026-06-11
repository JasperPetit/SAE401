<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Suivi Colis - Accueil</title>
    <link rel="stylesheet" href="public/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
    <?php require_once VIEWS . '/navbar.php'; ?>

    <main class="main-content">
        <div class="content-header">
            <h1>Tableau de Bord - Opérations Logistiques</h1>
        </div>

        <div class="dashboard-grid">
            <div class="stat-card blue">
                <h3>Colis en attente</h3>
                <div class="stat-number"><?= htmlspecialchars($nbAttente ?? 0) ?></div>
            </div>

            <div class="stat-card orange">
                <h3>Commandes en cours</h3>
                <div class="stat-number"><?= htmlspecialchars($nbEnCours ?? 0) ?></div>
            </div>

            <div class="stat-card red">
                <h3>Commandes en retard</h3>
                <div class="stat-number"><?= htmlspecialchars($nbRetard ?? 0) ?></div>
            </div>
        </div>

        <div class="content-header" style="border:none; margin-bottom: 15px; padding:0;">
            <h2>Dernier colis livré</h2>
        </div>

        <div class="data-card-container">
            <?php if (!empty($dernierColis)): ?>
                <div style="padding: 25px;">
                    <p style="font-size: 1.2rem; color: var(--success); font-weight: bold; margin-bottom: 10px;">
                        <i class="fas fa-check-circle"></i> Livré le <?= htmlspecialchars($dernierColis["Date_"] ?? 'Date inconnue') ?>
                    </p>
                    <p style="font-size: 1rem; color: var(--text-dark);">
                        Bon de commande : <strong>nº <?= htmlspecialchars($dernierColis["NumeroBonDeCommande"] ?? '') ?></strong>
                    </p>
                </div>
            <?php else: ?>
                <div style="padding: 25px; color: var(--text-muted); font-size: 1rem;">
                    <i class="fas fa-info-circle"></i> Aucun colis n'a été livré récemment.
                </div>
            <?php endif; ?>
        </div>

    </main>
</body>
</html>