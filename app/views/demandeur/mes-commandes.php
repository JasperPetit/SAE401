<?php
require __DIR__ . '/_nav.php';

$db = db();
$q = trim($_GET['q'] ?? '');
$statut = $_GET['statut'] ?? '';
$detail = (int)($_GET['detail'] ?? 0);

$sql = "SELECT c.*, f.nom AS fournisseur,
        (SELECT COUNT(*) FROM commande_articles a WHERE a.commande_id = c.id) AS nb_articles
        FROM commandes c LEFT JOIN fournisseurs f ON f.id = c.fournisseur_id WHERE 1=1";
$args = [];
if ($q !== '')      { $sql .= " AND (c.reference LIKE ? OR f.nom LIKE ? OR c.demandeur LIKE ?)"; array_push($args, "%$q%", "%$q%", "%$q%"); }
if ($statut !== '') { $sql .= " AND c.statut = ?"; $args[] = $statut; }
$sql .= " ORDER BY c.date_creation DESC, c.id DESC";
$st = $db->prepare($sql); $st->execute($args);
$commandes = $st->fetchAll();

$cmdDetail = null; $artDetail = [];
if ($detail) {
    $s = $db->prepare("SELECT c.*, f.nom AS fournisseur FROM commandes c LEFT JOIN fournisseurs f ON f.id = c.fournisseur_id WHERE c.id = ?");
    $s->execute([$detail]);
    $cmdDetail = $s->fetch();
    if ($cmdDetail) {
        $s = $db->prepare("SELECT * FROM commande_articles WHERE commande_id = ?");
        $s->execute([$detail]);
        $artDetail = $s->fetchAll();
    }
}

page_head('Mes Commandes', 'demandeur', 'mescommandes', $NAV, 'DÉPT. INFORMATIQUE');
?>
<div class="page-header" style="display:flex;justify-content:space-between;align-items:flex-start;">
  <div><h1>Mes Commandes</h1><p>Suivez chaque étape : devis, validation financière, signature, livraison, paiement</p></div>
  <a class="btn btn-gold" href="nouvelle-commande.php"><?= icon('plus', 14) ?> Nouvelle commande</a>
</div>

<form method="get" class="search-row">
  <div class="search-wrap"><?= icon('search', 14) ?>
    <input class="search-input" name="q" value="<?= e($q) ?>" placeholder="Rechercher une commande..."></div>
  <select class="f-input" name="statut" style="width:170px;" onchange="this.form.submit()">
    <option value="">Tous les statuts</option>
    <?php foreach (['Devis transmis au SF', 'Devis validé', 'Bon de commande signé', 'Livraison en cours', 'Réception confirmée', 'Fournisseur payé', 'Devis refusé'] as $s): ?>
    <option <?= $statut === $s ? 'selected' : '' ?>><?= $s ?></option>
    <?php endforeach; ?>
  </select>
  <button class="btn btn-outline" type="submit"><?= icon('filter', 14) ?> Filtrer</button>
</form>

<?php if ($cmdDetail): ?>
<div class="card" style="border-left:3px solid var(--gold);">
  <div class="card-header">
    <div class="card-title">Détail de la commande <?= e($cmdDetail['reference']) ?></div>
    <a class="btn btn-outline btn-sm" href="mes-commandes.php">Fermer</a>
  </div>
  <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:12px;font-size:12.5px;margin-bottom:14px;">
    <div><div style="color:var(--text-light);font-size:11px;">Date</div><strong><?= date_fr($cmdDetail['date_creation']) ?></strong></div>
    <div><div style="color:var(--text-light);font-size:11px;">Fournisseur</div><strong><?= e($cmdDetail['fournisseur'] ?? '-') ?></strong></div>
    <div><div style="color:var(--text-light);font-size:11px;">Lieu de livraison</div><strong><?= e($cmdDetail['lieu_livraison'] ?? '-') ?></strong></div>
    <div><div style="color:var(--text-light);font-size:11px;">Statut</div><?= badge($cmdDetail['statut']) ?></div>
  </div>
  <table>
    <thead><tr><th>Article</th><th>Référence</th><th>Quantité</th><th>Prix unitaire</th><th>Sous-total</th></tr></thead>
    <tbody>
    <?php foreach ($artDetail as $a): ?>
      <tr><td style="font-weight:600;color:var(--navy);"><?= e($a['nom']) ?></td><td><?= e($a['reference']) ?></td>
      <td><?= (int)$a['quantite'] ?></td><td><?= eur((float)$a['prix']) ?></td>
      <td style="font-weight:600;"><?= eur($a['quantite'] * $a['prix']) ?></td></tr>
    <?php endforeach; ?>
    </tbody>
  </table>
  <?php if ($cmdDetail['notes']): ?>
  <p style="font-size:12.5px;color:var(--text-light);margin-top:10px;"><strong>Notes :</strong> <?= e($cmdDetail['notes']) ?></p>
  <?php endif; ?>
</div>
<?php endif; ?>

<div class="card">
  <table>
    <thead><tr><th>Référence</th><th>Date</th><th>Fournisseur</th><th>Service</th><th>Statut</th><th>Articles</th><th>Total</th><th>Action</th></tr></thead>
    <tbody>
    <?php if (!$commandes): ?>
      <tr><td colspan="8" class="empty-state">Aucune commande ne correspond à votre recherche.</td></tr>
    <?php endif; ?>
    <?php foreach ($commandes as $c): ?>
      <tr>
        <td style="font-weight:600;color:var(--navy);"><?= e($c['reference']) ?></td>
        <td><?= date_fr($c['date_creation']) ?></td>
        <td><?= e($c['fournisseur'] ?? '-') ?></td>
        <td><?= e($c['service']) ?></td>
        <td><?= badge($c['statut']) ?></td>
        <td><?= (int)$c['nb_articles'] ?></td>
        <td style="font-weight:600;"><?= eur((float)$c['total']) ?></td>
        <td><a class="btn btn-outline btn-sm" href="?detail=<?= $c['id'] ?>&q=<?= urlencode($q) ?>&statut=<?= urlencode($statut) ?>">Détails</a></td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
  <p style="font-size:11.5px;color:var(--text-light);margin-top:10px;">Affichage de <?= count($commandes) ?> commande(s)</p>
</div>
<?php page_foot();
