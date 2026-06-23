<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administration — Suivi Colis IUT</title>
    <link rel="stylesheet" href="public/css/style.css">
</head>
<body class="service-admin">

    <?php require_once VIEWS . '/navbar.php'; ?>

    <main class="main">
        <div class="page-header">
            <h1><i class="fas fa-cogs"></i> Administration</h1>
            <p>Gérez les paramètres et configurations du système</p>
        </div>

        <div class="admin-grid">
            <div class="admin-block">
                <div class="ab-header">
                    <div class="ab-icon"><?= icon('db', 16) ?></div>
                    <div class="ab-title">Base de données</div>
                </div>
                <div class="ab-desc">Sauvegarde et maintenance de la base de données du système de suivi.</div>
                <div class="ab-actions">
                    <a class="btn btn-gold btn-sm" href="index.php?action=pageAdmin&backup=1">Sauvegarder maintenant</a>
                </div>
            </div>

            <div class="admin-block">
                <div class="ab-header">
                    <div class="ab-icon"><?= icon('users', 16) ?></div>
                    <div class="ab-title">Gestion des utilisateurs</div>
                </div>
                <div class="ab-desc">Consultez, ajoutez ou révoquez les comptes et les rôles d'accès des services.</div>
                <div class="ab-actions">
                    <a class="btn btn-primary btn-sm" href="index.php?action=pageVoirUtilisateurs">Voir les utilisateurs</a>
                    <a class="btn btn-outline btn-sm" href="index.php?action=pageAjouterUtilisateur">Ajouter un compte</a>
                </div>
            </div>

            <div class="admin-block">
                <div class="ab-header">
                    <div class="ab-icon"><?= icon('plus', 16) ?></div>
                    <div class="ab-title">Structures & Accès</div>
                </div>
                <div class="ab-desc">Gérez la création de nouveaux départements ou de nouveaux rôles.</div>
                <div class="ab-actions">
                    <a class="btn btn-primary btn-sm" href="index.php?action=pageVoirRoles">Voir les Rôles</a>
                    <a class="btn btn-gold btn-sm" href="index.php?action=pageVoirDepartements">Voir les Dépts</a>
                </div>
            </div>
            
        </div>

            
        </div>

        <div class="card">
            <div class="ab-header" style="margin-bottom:4px;">
                <div class="ab-icon"><?= icon('bell', 16) ?></div>
                <div class="ab-title">Paramètres de notifications</div>
            </div>
            <div class="ab-desc" style="margin-bottom:12px;">Cliquez sur un interrupteur pour activer / désactiver (simulé).</div>
            
            <div class="toggle-row">
                <span class="toggle-label">Notifications par email</span>
                <button class="toggle on" style="border:none;" title="Basculer"></button>
            </div>
            <div class="toggle-row">
                <span class="toggle-label">Notifications par SMS</span>
                <button class="toggle off" style="border:none;" title="Basculer"></button>
            </div>
        </div>

        <div class="card">
            <div class="ab-header" style="margin-bottom:4px;">
                <div class="ab-icon"><?= icon('file', 16) ?></div>
                <div class="ab-title">Rapports et statistiques de l'IUT</div>
            </div>
            <div class="ab-desc" style="margin-bottom:12px;">Accédez aux données globales consolidées par le Service Financier.</div>
            <div class="ab-actions">
                <a class="btn btn-outline" href="index.php?action=afficherCommande">État des commandes</a>
                <a class="btn btn-outline" href="index.php?action=afficherColis">Suivi général des colis</a>
            </div>
        </div>
    </main>

</body>
</html>