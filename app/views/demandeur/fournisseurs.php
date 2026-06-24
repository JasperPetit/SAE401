<?php
require __DIR__ . '/_nav.php';

$db = db();
$q = trim($_GET['q'] ?? '');
if ($q !== '') {
    $st = $db->prepare("SELECT * FROM fournisseurs WHERE nom LIKE ? OR categorie LIKE ? OR specialites LIKE ? ORDER BY nom");
    $st->execute(["%$q%", "%$q%", "%$q%"]);
} else {
    $st = $db->query("SELECT * FROM fournisseurs ORDER BY nom");
}
$fournisseurs = $st->fetchAll();

page_head('Fournisseurs', 'demandeur', 'fournisseurs', $NAV, 'DÉPT. INFORMATIQUE');
?>
<div class="page-header"><h1>Liste des Fournisseurs</h1><p>Consultez nos fournisseurs référencés</p></div>

<form method="get" class="search-row">
  <div class="search-wrap"><?= icon('search', 14) ?>
    <input class="search-input" name="q" value="<?= e($q) ?>" placeholder="Rechercher un fournisseur..."></div>
  <button class="btn btn-outline" type="submit">Rechercher</button>
</form>

<div class="fournisseur-grid">
<?php if (!$fournisseurs): ?><p class="empty-state">Aucun fournisseur trouvé.</p><?php endif; ?>
<?php foreach ($fournisseurs as $f): ?>
  <div class="fournisseur-card">
    <div class="four-header">
      <div><div class="four-name"><?= e($f['nom']) ?></div>
      <div style="font-size:11.5px;color:var(--text-light);"><?= e($f['categorie']) ?></div></div>
      <span class="star-rating"><?= icon('star', 13) ?> <?= number_format((float)$f['note'], 1) ?></span>
    </div>
    <div class="four-info"><?= icon('phone', 13) ?> <?= e($f['tel']) ?></div>
    <div class="four-info"><?= icon('mail', 13) ?> <?= e($f['email']) ?></div>
    <div class="four-info"><?= icon('pin', 13) ?> <?= e($f['adresse']) ?></div>
    <div style="margin-top:8px;">
      <?php foreach (explode(',', (string)$f['specialites']) as $tag): ?>
      <span class="four-tag"><?= e(trim($tag)) ?></span>
      <?php endforeach; ?>
    </div>
    <div class="four-footer">
      <span style="font-size:11.5px;color:var(--text-light);"><?= (int)$f['nb_commandes'] ?> commandes passées</span>
      <a class="btn btn-outline btn-sm" href="mailto:<?= e($f['email']) ?>">Contacter</a>
    </div>
  </div>
<?php endforeach; ?>
</div>
<?php page_foot();
