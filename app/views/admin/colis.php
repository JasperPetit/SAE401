<?php
require __DIR__ . '/_nav.php';

$db = db();
$q = trim($_GET['q'] ?? '');
$sql = "SELECT * FROM colis";
$args = [];
if ($q !== '') { $sql .= " WHERE reference LIKE ? OR tracking LIKE ? OR destinataire LIKE ? OR destination LIKE ?"; $args = ["%$q%", "%$q%", "%$q%", "%$q%"]; }
$sql .= " ORDER BY date_maj DESC, id DESC";
$st = $db->prepare($sql); $st->execute($args);
$liste = $st->fetchAll();

page_head('Gestion des Colis', 'admin', 'colis', $NAV, 'ADMINISTRATEUR');
?>
<div class="page-header"><h1>Gestion des Colis</h1><p>Suivez et gérez tous vos colis en temps réel</p></div>

<form method="get" class="search-row">
  <div class="search-wrap"><?= icon('search', 14) ?>
    <input class="search-input" name="q" value="<?= e($q) ?>" placeholder="Rechercher par numéro de suivi, destinataire..."></div>
  <button class="btn btn-outline" type="submit"><?= icon('filter', 14) ?> Filtrer</button>
</form>

<?php if (!$liste): ?><div class="card"><p class="empty-state">Aucun colis trouvé.</p></div><?php endif; ?>
<?php foreach ($liste as $c): ?>
<div class="card" style="padding:16px 20px;">
  <div style="display:flex;align-items:center;gap:14px;flex-wrap:wrap;">
    <div style="width:36px;height:36px;border-radius:8px;background:#dbeafe;display:flex;align-items:center;justify-content:center;color:var(--blue);flex-shrink:0;"><?= icon('box', 16) ?></div>
    <div style="flex:1;min-width:220px;">
      <div style="font-size:13px;font-weight:600;color:var(--navy);"><?= e($c['reference']) ?>
        <span style="font-weight:400;color:var(--text-light);font-size:12px;"> · <?= e($c['tracking']) ?></span></div>
      <div style="font-size:12px;color:var(--text-light);margin-top:2px;">
        <?= e($c['destinataire']) ?> — <?= e($c['destination']) ?> &nbsp;·&nbsp; <?= dateheure_fr($c['date_maj']) ?>
      </div>
    </div>
    <?= badge($c['statut']) ?>
  </div>
</div>
<?php endforeach; ?>
<?php page_foot();
