<?php
require __DIR__ . '/_nav.php';
csrf_check();

$db = db();

/* --- Marquer payée --- */
if (isset($_POST['payer_id'])) {
    $db->prepare("UPDATE factures SET statut = 'Payée' WHERE id = ?")->execute([(int)$_POST['payer_id']]);
    flash('Facture marquée comme payée.');
    header('Location: factures.php');
    exit;
}

/* --- Nouvelle facture --- */
$erreurs = [];
if (isset($_POST['nouvelle_fact'])) {
    $client   = trim($_POST['client'] ?? '');
    $montant  = (float)str_replace(',', '.', $_POST['montant'] ?? '0');
    $echeance = $_POST['echeance'] ?: date('Y-m-d', strtotime('+7 days'));
    if ($client === '') $erreurs[] = 'Le client est obligatoire.';
    if ($montant <= 0)  $erreurs[] = 'Le montant doit être supérieur à 0.';
    if (!$erreurs) {
        $num = next_ref('factures', 'numero', 'FACT-' . date('Y') . '-');
        $db->prepare("INSERT INTO factures (numero, date_emission, client, echeance, montant, statut)
                      VALUES (?,date('now'),?,?,?,'En attente')")
           ->execute([$num, $client, $echeance, $montant]);
        flash("Facture $num créée (" . eur($montant) . ').');
        header('Location: factures.php');
        exit;
    }
}

$stats = [
    'emises'  => (int)$db->query("SELECT COUNT(*) FROM factures")->fetchColumn(),
    'payees'  => (int)$db->query("SELECT COUNT(*) FROM factures WHERE statut = 'Payée'")->fetchColumn(),
    'attente' => (int)$db->query("SELECT COUNT(*) FROM factures WHERE statut = 'En attente'")->fetchColumn(),
    'retard'  => (int)$db->query("SELECT COUNT(*) FROM factures WHERE statut = 'En attente' AND echeance < date('now')")->fetchColumn(),
];
$liste = $db->query("SELECT * FROM factures ORDER BY date_emission DESC, id DESC")->fetchAll();

$ouvrirForm = isset($_GET['nouvelle']) || $erreurs;

page_head('Factures', 'financier', 'factures', $NAV, 'SERVICE FINANCIER');
?>
<div class="page-header" style="display:flex;justify-content:space-between;align-items:flex-start;">
  <div><h1>Gestion des Factures</h1><p>Créez et gérez vos factures de colis</p></div>
  <a class="btn btn-gold" href="?nouvelle=1"><?= icon('plus', 14) ?> Nouvelle facture</a>
</div>

<?php foreach ($erreurs as $err): ?><div class="flash flash-error"><?= e($err) ?></div><?php endforeach; ?>

<?php if ($ouvrirForm): ?>
<div class="card" style="border-left:3px solid var(--gold);">
  <div class="card-header"><div class="card-title">Créer une facture</div>
  <a class="btn btn-outline btn-sm" href="factures.php">Fermer</a></div>
  <form method="post">
    <?= csrf_field() ?>
    <input type="hidden" name="nouvelle_fact" value="1">
    <div style="display:grid;grid-template-columns:1.5fr .8fr .9fr auto;gap:10px;align-items:end;">
      <div><label class="f-label">Client <span class="req">*</span></label>
        <input class="f-input" name="client" placeholder="Ex : Service RH" required></div>
      <div><label class="f-label">Montant (€) <span class="req">*</span></label>
        <input class="f-input" type="number" step="0.01" min="0.01" name="montant" required></div>
      <div><label class="f-label">Échéance</label>
        <input class="f-input" type="date" name="echeance" value="<?= date('Y-m-d', strtotime('+7 days')) ?>"></div>
      <button class="btn btn-gold" type="submit">Créer</button>
    </div>
  </form>
</div>
<?php endif; ?>

<div class="stats-grid">
  <div class="stat-card"><div><div class="label">Factures émises</div><div class="value"><?= $stats['emises'] ?></div></div>
    <div class="stat-icon blue"><?= icon('file', 18) ?></div></div>
  <div class="stat-card"><div><div class="label">Factures payées</div><div class="value" style="color:var(--green);"><?= $stats['payees'] ?></div></div>
    <div class="stat-icon green"><?= icon('check', 18) ?></div></div>
  <div class="stat-card"><div><div class="label">En attente</div><div class="value" style="color:var(--orange);"><?= $stats['attente'] ?></div></div>
    <div class="stat-icon orange"><?= icon('clock', 18) ?></div></div>
  <div class="stat-card"><div><div class="label">En retard</div><div class="value" style="color:var(--red);"><?= $stats['retard'] ?></div></div>
    <div class="stat-icon orange"><?= icon('alert', 18) ?></div></div>
</div>

<div class="card">
  <div class="card-title" style="margin-bottom:8px;">Liste des factures</div>
  <table>
    <thead><tr><th>Numéro</th><th>Date émission</th><th>Client</th><th>Échéance</th><th>Montant</th><th>Statut</th><th>Actions</th></tr></thead>
    <tbody>
    <?php if (!$liste): ?><tr><td colspan="7" class="empty-state">Aucune facture.</td></tr><?php endif; ?>
    <?php foreach ($liste as $f):
        $enRetard = $f['statut'] === 'En attente' && $f['echeance'] < date('Y-m-d'); ?>
    <tr>
      <td style="font-weight:600;color:var(--navy);"><?= e($f['numero']) ?></td>
      <td><?= date_fr($f['date_emission']) ?></td>
      <td><?= e($f['client']) ?></td>
      <td<?= $enRetard ? ' style="color:var(--red);font-weight:600;"' : '' ?>><?= date_fr($f['echeance']) ?></td>
      <td style="font-weight:600;"><?= eur0((float)$f['montant']) ?></td>
      <td><?= $enRetard ? badge('En retard') : badge($f['statut']) ?></td>
      <td>
        <?php if ($f['statut'] !== 'Payée'): ?>
        <form method="post" style="display:inline;">
          <?= csrf_field() ?>
          <button class="btn btn-outline btn-sm" name="payer_id" value="<?= $f['id'] ?>">Marquer payée</button>
        </form>
        <?php else: ?><span style="font-size:11px;color:var(--text-light);">—</span><?php endif; ?>
      </td>
    </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php page_foot();
