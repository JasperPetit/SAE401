<?php
/* ============================================================
   SÉLECTION DU PROFIL - SAE COLIS
   ============================================================ */
require_once __DIR__ . '/includes/auth.php';

/* Déjà connecté ? -> redirection vers son espace */
if ($u = current_user()) {
    $home = ['admin' => 'admin/accueil.php', 'financier' => 'financier/tableau-de-bord.php',
             'postier' => 'postier/tableau-de-bord.php', 'demandeur' => 'demandeur/accueil.php'];
    header('Location: ' . $home[$u['role']]);
    exit;
}

/* Connexion par choix de profil (simulation du CAS universitaire) */
csrf_check();
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['role'])) {
    $role = $_POST['role'];
    if (in_array($role, ['admin', 'financier', 'postier', 'demandeur'], true) && login($role)) {
        $home = ['admin' => 'admin/accueil.php', 'financier' => 'financier/tableau-de-bord.php',
                 'postier' => 'postier/tableau-de-bord.php', 'demandeur' => 'demandeur/accueil.php'];
        header('Location: ' . $home[$role]);
        exit;
    }
    flash('Profil inconnu.', 'error');
}

$profils = [
    ['admin', 'Administrateur', 'Gère tout le site et résout les problèmes : commandes, utilisateurs, budgets, données.', 'cog', '#fef3c7', '#c8933a'],
    ['financier', 'Service Financier', 'Validez les devis selon les budgets, suivez les livraisons et payez les fournisseurs sans retard.', 'euro', '#dcfce7', '#2f9e6e'],
    ['postier', 'Service Postier', 'Identifiez le département destinataire de chaque colis, même si le bon de livraison est détérioré.', 'send', '#dbeafe', '#4a7fb5'],
    ['demandeur', 'Département', 'Déposez vos devis, consultez votre budget alloué et confirmez la réception des colis.', 'user_icon', '#ede9fe', '#8b6db5'],
];
?><!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Connexion — Suivi Colis | Université Sorbonne Paris Nord</title>
<link rel="stylesheet" href="css/style.css">
</head>
<body class="page-login">

<header class="login-hero">
  <img src="assets/logo.jpeg" alt="Université Sorbonne Paris Nord">
  <h1>Plateforme de Gestion et de Suivi des Colis</h1>
  <p>IUT de Villetaneuse — Sélectionnez votre profil pour accéder à votre espace (authentification USPN simulée).
  L'interface s'adapte automatiquement à votre rôle.</p>
</header>

<div class="login-grid">
<?= show_flash() ?>
<?php foreach ($profils as [$role, $nom, $desc, $ic, $bg, $col]): ?>
  <form method="post" class="profile-card">
    <?= csrf_field() ?>
    <input type="hidden" name="role" value="<?= e($role) ?>">
    <span class="pc-ic" style="background:<?= $bg ?>;color:<?= $col ?>;">
      <?= $ic === 'user_icon'
          ? '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="3.6"/><path d="M5 20.2c.9-3.6 3.7-5.6 7-5.6s6.1 2 7 5.6"/></svg>'
          : icon($ic, 20) ?>
    </span>
    <h2><?= e($nom) ?></h2>
    <p><?= e($desc) ?></p>
    <button class="btn btn-primary" style="justify-content:center;" type="submit">Accéder à cet espace</button>
  </form>
<?php endforeach; ?>
</div>

<p class="login-foot">Université Sorbonne Paris Nord — SAE Colis — Projet universitaire</p>
</body>
</html>
