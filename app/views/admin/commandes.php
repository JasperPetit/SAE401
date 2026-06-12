<?php
require __DIR__ . '/_nav.php';
csrf_check();

$db = db();

/* --- L'admin peut changer le statut d'une commande --- */
if (isset($_POST['cmd_id'], $_POST['statut'])) {
    $valides = ['Devis transmis au SF', 'Devis validé', 'Bon de commande signé', 'Livraison en cours', 'Réception confirmée', 'Fournisseur payé', 'Devis refusé'];
    if (in_array($_POST['statut'], $valides, true)) {
        $db->prepare("UPDATE commandes SET statut = ? WHERE id = ?")->execute([$_POST['statut'], (int)$_POST['cmd_id']]);
        flash('Statut de la commande mis à jour : ' . $_POST['statut']);
    }
    header('Location: commandes.php?q=' . urlencode($_GET['q'] ?? ''));
    exit;
}

$q = trim($_GET['q'] ?? '');
$sql = "SELECT c.*, f.nom AS fournisseur,
        (SELECT COUNT(*) FROM commande_articles a WHERE a.commande_id = c.id) AS nb_articles
        FROM commandes c LEFT JOIN fournisseurs f ON f.id = c.fournisseur_id";
$args = [];
if ($q !== '') { $sql .= " WHERE c.reference LIKE ? OR c.demandeur LIKE ? OR f.nom LIKE ?"; $args = ["%$q%", "%$q%", "%$q%"]; }
$sql .= " ORDER BY c.date_creation DESC, c.id DESC";
$st = $db->prepare($sql); $st->execute($args);
$commandes = $st->fetchAll();

page_head('Mes Commandes', 'admin', 'commandes', $NAV, 'ADMINISTRATEUR');
?>
<div class="page-header"><h1>Mes Commandes</h1><p>Rôle ADMIN : vue globale et résolution des problèmes — vous pouvez corriger le statut de n'importe quelle commande</p></div>

<form method="get" class="search-row">
  <div class="search-wrap"><?= icon('search', 14) ?>
    <input class="search-input" name="q" value="<?= e($q) ?>" placeholder="Rechercher une commande..."></div>
  <button class="btn btn-outline" type="submit">Rechercher</button>
</form>

<?php if (!$commandes): ?><div class="card"><p class="empty-state">Aucune commande.</p></div><?php endif; ?>
<?php foreach ($commandes as $c): ?>
<div class="card" style="padding:16px 20px;">
  <div style="display:flex;align-items:center;gap:14px;flex-wrap:wrap;">
    <div class="li-icon" style="width:36px;height:36px;border-radius:8px;background:#dbeafe;display:flex;align-items:center;justify-content:center;color:var(--blue);flex-shrink:0;"><?= icon('box', 16) ?></div>
    <div style="flex:1;min-width:220px;">
      <div style="font-size:13px;font-weight:600;color:var(--navy);"><?= e($c['reference']) ?></div>
      <div style="font-size:12px;color:var(--text-light);margin-top:2px;">
        <?= date_fr($c['date_creation']) ?> &nbsp;·&nbsp; <?= e($c['demandeur']) ?> &nbsp;·&nbsp;
        <?= (int)$c['nb_articles'] ?> article(s) &nbsp;·&nbsp; <?= e($c['fournisseur'] ?? '-') ?> &nbsp;·&nbsp;
        <strong style="color:var(--navy);"><?= eur((float)$c['total']) ?></strong>
      </div>
    </div>
    <?= badge($c['statut']) ?>
    <form method="post" style="display:flex;gap:6px;">
      <?= csrf_field() ?>
      <input type="hidden" name="cmd_id" value="<?= $c['id'] ?>">
      <select class="f-input" name="statut" style="width:200px;padding:6px 9px;font-size:12px;">
        <?php foreach (['Devis transmis au SF', 'Devis validé', 'Bon de commande signé', 'Livraison en cours', 'Réception confirmée', 'Fournisseur payé', 'Devis refusé'] as $s): ?>
        <option <?= $c['statut'] === $s ? 'selected' : '' ?>><?= $s ?></option>
        <?php endforeach; ?>
      </select>
      <button class="btn btn-outline btn-sm" type="submit">Appliquer</button>
    </form>
  </div>
</div>
<?php endforeach; ?>
<?php page_foot();
