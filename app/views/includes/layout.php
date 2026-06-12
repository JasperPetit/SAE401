<?php
/* ============================================================
   GABARIT COMMUN (sidebar + topbar) - SAE COLIS
   Même design que la maquette : on réutilise css/style.css
   ============================================================ */
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/auth.php';

/**
 * En-tête de page.
 * @param string $titre   Titre de l'onglet
 * @param string $service admin|postier|financier|demandeur
 * @param string $actif   identifiant du lien actif
 * @param array  $nav     [ [href, icone, libellé, id], ... ]
 * @param string $role    libellé du badge de rôle
 */
function page_head(string $titre, string $service, string $actif, array $nav, string $role): void {
    $u = current_user();
?><!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= e($titre) ?> — Suivi Colis IUT</title>
<link rel="stylesheet" href="../css/style.css">
</head>
<body class="service-<?= e($service) ?>">

<aside class="sidebar">
  <div class="sidebar-logo">
    <img src="../assets/logo.jpeg" alt="Université Sorbonne Paris Nord">
    <div class="sidebar-subtitle">IUT de Villetaneuse</div>
  </div>
  <nav class="nav">
<?php foreach ($nav as [$href, $ic, $lbl, $id]): ?>
    <a class="nav-item<?= $id === $actif ? ' active' : '' ?>" href="<?= e($href) ?>"><?= icon($ic) ?><?= e($lbl) ?></a>
<?php endforeach; ?>
  </nav>
  <div class="sidebar-footer">
    <div style="font-size:11px;color:rgba(255,255,255,.7);margin-bottom:6px;"><?= e($u['nom'] ?? '') ?></div>
    <span class="role-badge"><?= e($role) ?></span>
    <a href="../logout.php" class="logout-link"><?= icon('logout', 13) ?> Se déconnecter</a>
  </div>
</aside>

<div class="topbar">
  <div class="topbar-title">Suivi Colis <span>IUT de Villetaneuse</span></div>
  <div class="topbar-right">
    <span style="font-size:12px;color:var(--text-light);"><?= e($u['email'] ?? '') ?></span>
    <button class="btn-icon" title="Notifications"><?= icon('bell') ?></button>
  </div>
</div>

<main class="main">
<?= show_flash() ?>
<?php
}

function page_foot(): void {
?>
</main>
</body>
</html>
<?php
}
