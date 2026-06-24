<?php
require __DIR__ . '/_nav.php';

$db = db();
$nbAttente = (int)$db->query("SELECT COUNT(*) FROM colis WHERE statut = 'En attente'")->fetchColumn();
$nbCours   = (int)$db->query("SELECT COUNT(*) FROM colis WHERE statut IN ('En cours','En transit','En livraison')")->fetchColumn();
$nbLivres  = (int)$db->query("SELECT COUNT(*) FROM colis WHERE statut = 'Livré'")->fetchColumn();
$nbTotal   = (int)$db->query("SELECT COUNT(*) FROM colis")->fetchColumn();

$recents = $db->query("SELECT reference, destination, statut, date_maj FROM colis ORDER BY date_maj DESC, id DESC LIMIT 4")->fetchAll();

page_head('Tableau de bord', 'postier', 'dashboard', $NAV, 'SERVICE POSTAL');
?>
<div class="page-header"><h1>Tableau de bord postal</h1>
<p>Vue d'ensemble des opérations du jour — <?= date('d/m/Y') ?></p></div>

<div class="stats-grid">
  <div class="stat-card">
    <div><div class="label">Colis en attente</div><div class="value" style="color:var(--orange);"><?= $nbAttente ?></div></div>
    <div class="stat-icon orange"><?= icon('clock', 18) ?></div>
  </div>
  <div class="stat-card">
    <div><div class="label">En cours de livraison</div><div class="value" style="color:var(--blue);"><?= $nbCours ?></div></div>
    <div class="stat-icon blue"><?= icon('truck', 18) ?></div>
  </div>
  <div class="stat-card">
    <div><div class="label">Livrés</div><div class="value" style="color:var(--green);"><?= $nbLivres ?></div></div>
    <div class="stat-icon green"><?= icon('check', 18) ?></div>
  </div>
  <div class="stat-card">
    <div><div class="label">Total des colis</div><div class="value"><?= $nbTotal ?></div></div>
    <div class="stat-icon blue"><?= icon('box', 18) ?></div>
  </div>
</div>

<div class="action-tiles">
  <a class="action-tile" style="background:var(--navy);text-decoration:none;" href="scanner.php">
    <span class="at-ic" style="background:rgba(255,255,255,.12);color:#fff;"><?= icon('scan', 20) ?></span>
    <span><span class="at-t" style="color:#fff;display:block;">Scanner un colis</span>
    <span class="at-d" style="color:rgba(255,255,255,.6);display:block;">Scannez le code-barres d'un colis</span></span>
  </a>
  <a class="action-tile" style="background:var(--gold);text-decoration:none;" href="nouvel-envoi.php">
    <span class="at-ic" style="background:rgba(255,255,255,.3);color:#fff;"><?= icon('send', 19) ?></span>
    <span><span class="at-t" style="color:#fff;display:block;">Nouvel envoi</span>
    <span class="at-d" style="color:rgba(255,255,255,.85);display:block;">Créer un nouveau bon d'expédition</span></span>
  </a>
</div>

<div class="card">
  <div class="card-title" style="margin-bottom:8px;">Activité récente</div>
  <?php if (!$recents): ?><p class="empty-state">Aucun colis enregistré.</p><?php endif; ?>
  <?php foreach ($recents as $r): ?>
  <div class="list-item">
    <div class="li-icon"><?= icon('box', 16) ?></div>
    <div class="li-info"><div class="li-title"><?= e($r['reference']) ?></div>
    <div class="li-meta">Destination : <?= e($r['destination']) ?></div></div>
    <div class="li-actions"><span class="li-meta"><?= $r['date_maj'] ? date('H:i', strtotime($r['date_maj'])) : '' ?></span><?= badge($r['statut']) ?></div>
  </div>
  <?php endforeach; ?>
</div>
<?php page_foot();
