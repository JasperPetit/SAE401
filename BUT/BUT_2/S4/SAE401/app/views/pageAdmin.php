<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administration - SAE Colis</title>
    <link rel="stylesheet" href="public/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php require_once 'app/views/navbar.php'; ?>
    
    <main class="main-content">
        <div class="content-header">
            <h1><i class="fas fa-cogs"></i> Panneau d'Administration</h1>
        </div>

        <div class="dashboard-grid">
            <div class="stat-card blue" style="text-align: center; padding: 40px 20px;">
                <i class="fas fa-users" style="font-size: 3rem; color: var(--primary-blue); margin-bottom: 15px;"></i>
                <h3 style="font-size: 1.2rem; color: var(--text-dark); margin-bottom: 10px;">Gestion des Utilisateurs</h3>
                <p style="color: var(--text-muted); margin-bottom: 20px;">Gérez les comptes, les rôles et les permissions d'accès au système.</p>
                <a href="index.php?action=pageVoirUtilisateurs" class="btn"><i class="fas fa-list"></i> Voir les utilisateurs</a>
            </div>

            <div class="stat-card orange" style="text-align: center; padding: 40px 20px;">
                <i class="fas fa-user-plus" style="font-size: 3rem; color: var(--accent-orange); margin-bottom: 15px;"></i>
                <h3 style="font-size: 1.2rem; color: var(--text-dark); margin-bottom: 10px;">Nouvel Accès</h3>
                <p style="color: var(--text-muted); margin-bottom: 20px;">Créez un nouveau compte pour un membre du personnel ou un service.</p>
                <a href="index.php?action=pageAjouterUtilisateur" class="btn" style="background-color: var(--accent-orange);"><i class="fas fa-plus"></i> Ajouter un compte</a>
            </div>
        </div>
    </main>
</body>
</html>