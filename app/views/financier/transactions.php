<?php
require __DIR__ . '/_nav.php';
csrf_check();

$db = db();

/* --- Enregistrer un paiement (changer le statut) --- */
if (isset($_POST['payer_id'])) {
    $db->prepare("UPDATE transactions SET statut = 'Payé' WHERE id = ?")->execute([(int)$_POST['payer_id']]);
    flash('Paiement enregistré.');
    header('Location: transactions.php');
    exit;
}

/* --- Nouvelle transaction --- */
$erreurs = [];
if (isset($_POST['nouvelle_trx'])) {
    $client  = trim($_POST['client'] ?? '');
    $type    = trim($_POST['type'] ?? 'Colis standard');
    $montant = (float)str_replace(',', '.', $_POST['montant'] ?? '0');
    $statut  = in_array($_POST['statut'] ?? '', ['Payé', 'En attente', 'Impayé'], true) ? $_POST['statut'] : 'En attente';
    if ($client === '') $erreurs[] = 'Le client est obligatoire.';
    if ($montant <= 0)  $erreurs[] = 'Le montant doit être supérieur à 0.';
    if (!$erreurs) {
        $ref = next_ref('transactions', 'reference', 'TRX-' . date('Y') . '-');
        $db->prepare("INSERT INTO transactions (reference, date_t, client, type, montant, statut) VALUES (?,date('now'),?,?,?,?)")
           ->execute([$ref, $client, $type, $montant, $statut]);
        flash("Transaction $ref enregistrée (" . eur($montant) . ').');
        header('Location: transactions.php');
        exit;
    }
}

$q = trim($_GET['q'] ?? '');
$sql = "SELECT * FROM transactions";
$args = [];
if ($q !== '') { $sql .= " WHERE reference LIKE ? OR client LIKE ?"; $args = ["%$q%", "%$q%"]; }
$sql .= " ORDER BY date_t DESC, id DESC";
$st = $db->prepare($sql); $st->execute($args);
$liste = $st->fetchAll();

/* Export CSV */
if (isset($_GET['export'])) {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="transactions_' . date('Ymd') . '.csv"');
    $out = fopen('php://output', 'w');
    fputs($out, "\xEF\xBB\xBF");
    fputcsv($out, ['Référence', 'Date', 'Client', 'Type', 'Montant', 'Statut'], ';');
    foreach ($liste as $t) fputcsv($out, [$t['reference'], $t['date_t'], $t['client'], $t['type'], $t['montant'], $t['statut']], ';');
    fclose($out);
    exit;
}

$paye    = $db->query("SELECT COALESCE(SUM(montant),0) s, COUNT(*) n FROM transactions WHERE statut = 'Payé'")->fetch();
$attente = $db->query("SELECT COALESCE(SUM(montant),0) s, COUNT(*) n FROM transactions WHERE statut = 'En attente'")->fetch();
$impaye  = $db->query("SELECT COALESCE(SUM(montant),0) s, COUNT(*) n FROM transactions WHERE statut = 'Impayé'")->fetch();

$ouvrirForm = isset($_GET['nouvelle']) || $erreurs;

page_head('Transactions', 'financier', 'transactions', $NAV, 'SERVICE FINANCIER');
?>
<div class="page-header" style="display:flex;justify-content:space-between;align-items:flex-start;">
  <div><h1>Gestion des Transactions</h1><p>Consultez et gérez toutes les transactions financières</p></div>
  <div class="actions-row" style="margin:0;">
    <a class="btn btn-outline" href="?export=1&q=<?= urlencode($q) ?>"><?= icon('dl', 14) ?> Exporter (CSV)</a>
    <a class="btn btn-gold" href="?nouvelle=1"><?= icon('plus', 14) ?> Nouvelle transaction</a>
  </div>
</div>

<?php foreach ($erreurs as $err): ?><div class="flash flash-error"><?= e($err) ?></div><?php endforeach; ?>

<?php if ($ouvrirForm): ?>
<div class="card" style="border-left:3px solid var(--gold);">
  <div class="card-header"><div class="card-title">Enregistrer une transaction</div>
  <a class="btn btn-outline btn-sm" href="transactions.php">Fermer</a></div>
  <form method="post">
    <?= csrf_field() ?>
    <input type="hidden" name="nouvelle_trx" value="1">
    <div style="display:grid;grid-template-columns:1.5fr 1fr .8fr .8fr auto;gap:10px;align-items:end;">
      <div><label class="f-label">Client <span class="req">*</span></label>
        <input class="f-input" name="client" placeholder="Ex : Département Informatique" required></div>
      <div><label class="f-label">Type</label>
        <select class="f-input" name="type"><option>Colis standard</option><option>Colis express</option><option>Colis prioritaire</option></select></div>
      <div><label class="f-label">Montant (€) <span class="req">*</span></label>
        <input class="f-input" type="number" step="0.01" min="0.01" name="montant" required></div>
      <div><label class="f-label">Statut</label>
        <select class="f-input" name="statut"><option>Payé</option><option>En attente</option><option>Impayé</option></select></div>
      <button class="btn btn-gold" type="submit">Enregistrer</button>
    </div>
  </form>
</div>
<?php endif; ?>

<form method="get" class="search-row">
  <div class="search-wrap"><?= icon('search', 14) ?>
    <input class="search-input" name="q" value="<?= e($q) ?>" placeholder="Rechercher une transaction..."></div>
  <button class="btn btn-outline" type="submit"><?= icon('filter', 14) ?> Filtrer</button>
</form>

<div class="stats-grid" style="grid-template-columns:repeat(3,1fr);">
  <div class="stat-card">
    <div><div class="label">Total des paiements reçus</div><div class="value" style="color:var(--green);font-size:20px;"><?= eur0((float)$paye['s']) ?></div>
    <div style="font-size:11px;color:var(--text-light);"><?= $paye['n'] ?> transactions</div></div>
    <div class="stat-icon green"><?= icon('check', 18) ?></div>
  </div>
  <div class="stat-card">
    <div><div class="label">En attente de paiement</div><div class="value" style="color:var(--orange);font-size:20px;"><?= eur0((float)$attente['s']) ?></div>
    <div style="font-size:11px;color:var(--text-light);"><?= $attente['n'] ?> transactions</div></div>
    <div class="stat-icon orange"><?= icon('clock', 18) ?></div>
  </div>
  <div class="stat-card">
    <div><div class="label">Paiements en retard</div><div class="value" style="color:var(--red);font-size:20px;"><?= eur0((float)$impaye['s']) ?></div>
    <div style="font-size:11px;color:var(--text-light);"><?= $impaye['n'] ?> transactions</div></div>
    <div class="stat-icon orange"><?= icon('alert', 18) ?></div>
  </div>
</div>

<div class="card">
  <div class="card-title" style="margin-bottom:8px;">Dernières transactions</div>
  <table>
    <thead><tr><th>ID Transaction</th><th>Date</th><th>Client</th><th>Type</th><th>Montant</th><th>Statut</th><th>Action</th></tr></thead>
    <tbody>
    <?php if (!$liste): ?><tr><td colspan="7" class="empty-state">Aucune transaction.</td></tr><?php endif; ?>
    <?php foreach ($liste as $t): ?>
    <tr>
      <td style="font-weight:600;color:var(--navy);"><?= e($t['reference']) ?></td>
      <td><?= date_fr($t['date_t']) ?></td>
      <td><?= e($t['client']) ?></td>
      <td><?= e($t['type']) ?></td>
      <td style="font-weight:600;"><?= eur0((float)$t['montant']) ?></td>
      <td><?= badge($t['statut']) ?></td>
      <td>
        <?php if ($t['statut'] !== 'Payé'): ?>
        <form method="post" style="display:inline;">
          <?= csrf_field() ?>
          <button class="btn btn-outline btn-sm" name="payer_id" value="<?= $t['id'] ?>">Encaisser</button>
        </form>
        <?php else: ?><span style="font-size:11px;color:var(--text-light);">—</span><?php endif; ?>
      </td>
    </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php page_foot();
