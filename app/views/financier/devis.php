<?php
require __DIR__ . '/_nav.php';
csrf_check();

$db = db();

/* ============================================================
   ACTIONS DU SERVICE FINANCIER (processus M. Butelle)
   - Valider / refuser un devis selon le budget du département
   - Enregistrer le bon de commande signé par le directeur
   - Payer le fournisseur (seulement si réception confirmée)
   - Relancer le département qui n'a pas confirmé la réception
   ============================================================ */
if (isset($_POST['action'], $_POST['cmd_id'])) {
    $id = (int)$_POST['cmd_id'];
    $st = $db->prepare("SELECT c.*, d.nom AS dept FROM commandes c LEFT JOIN departements d ON d.id = c.departement_id WHERE c.id = ?");
    $st->execute([$id]);
    $cmd = $st->fetch();

    if ($cmd) {
        switch ($_POST['action']) {
            case 'valider':
                $db->prepare("UPDATE commandes SET statut = 'Devis validé' WHERE id = ?")->execute([$id]);
                flash("Devis {$cmd['reference']} validé — transmis au directeur de l'IUT pour signature.");
                break;
            case 'refuser':
                $db->prepare("UPDATE commandes SET statut = 'Devis refusé' WHERE id = ?")->execute([$id]);
                flash("Devis {$cmd['reference']} refusé (budget insuffisant ou demande non conforme).", 'error');
                break;
            case 'bc_signe':
                $db->prepare("UPDATE commandes SET statut = 'Bon de commande signé' WHERE id = ?")->execute([$id]);
                flash("Bon de commande {$cmd['reference']} signé par le directeur — commande envoyée au fournisseur.");
                break;
            case 'expedier':
                $db->prepare("UPDATE commandes SET statut = 'Livraison en cours' WHERE id = ?")->execute([$id]);
                flash("Commande {$cmd['reference']} : livraison en cours.");
                break;
            case 'payer':
                if ($cmd['statut'] === 'Réception confirmée') {
                    $db->prepare("UPDATE commandes SET statut = 'Fournisseur payé' WHERE id = ?")->execute([$id]);
                    $ref = next_ref('transactions', 'reference', 'TRX-' . date('Y') . '-');
                    $db->prepare("INSERT INTO transactions (reference, date_t, client, type, montant, statut)
                                  VALUES (?,date('now'),?,?,?,'Payé')")
                       ->execute([$ref, 'Dépt. ' . $cmd['dept'], 'Paiement fournisseur', $cmd['total']]);
                    flash("Fournisseur payé pour {$cmd['reference']} (" . eur((float)$cmd['total']) . ") — transaction $ref enregistrée.");
                } else {
                    flash("Paiement impossible : le département n'a pas encore confirmé la réception.", 'error');
                }
                break;
            case 'relancer':
                flash("Relance envoyée au département {$cmd['dept']} pour confirmer la réception de {$cmd['reference']}.");
                break;
        }
    }
    header('Location: devis.php');
    exit;
}

/* Budgets des départements (consultation demandée par le prof) */
$budgets = $db->query(
    "SELECT d.nom, d.budget_alloue,
            COALESCE((SELECT SUM(total) FROM commandes c
                      WHERE c.departement_id = d.id
                        AND c.statut NOT IN ('Devis transmis au SF','Devis refusé')), 0) AS engage
     FROM departements d ORDER BY d.nom")->fetchAll();

$commandes = $db->query(
    "SELECT c.*, d.nom AS dept, d.budget_alloue, f.nom AS fournisseur,
            COALESCE((SELECT SUM(total) FROM commandes c2
                      WHERE c2.departement_id = c.departement_id
                        AND c2.statut NOT IN ('Devis transmis au SF','Devis refusé')), 0) AS deja_engage
     FROM commandes c
     LEFT JOIN departements d ON d.id = c.departement_id
     LEFT JOIN fournisseurs f ON f.id = c.fournisseur_id
     WHERE c.statut != 'Fournisseur payé'
     ORDER BY CASE c.statut
        WHEN 'Devis transmis au SF' THEN 1
        WHEN 'Réception confirmée'  THEN 2
        WHEN 'Livraison en cours'   THEN 3
        WHEN 'Devis validé'         THEN 4
        WHEN 'Bon de commande signé' THEN 5
        ELSE 6 END, c.date_creation DESC")->fetchAll();

page_head('Validation des devis', 'financier', 'devis', $NAV, 'SERVICE FINANCIER');
?>
<div class="page-header"><h1>Validation des devis et suivi des commandes</h1>
<p>Validez les devis selon le budget, suivez les livraisons et payez les fournisseurs sans retard</p></div>

<div class="card">
  <div class="card-title" style="margin-bottom:10px;">Budgets des départements</div>
  <table>
    <thead><tr><th>Département</th><th>Budget alloué</th><th>Engagé</th><th>Restant</th><th>Consommation</th></tr></thead>
    <tbody>
    <?php foreach ($budgets as $b):
        $restant = $b['budget_alloue'] - $b['engage'];
        $pct = $b['budget_alloue'] > 0 ? min(100, round($b['engage'] / $b['budget_alloue'] * 100)) : 0; ?>
    <tr>
      <td style="font-weight:600;color:var(--navy);"><?= e($b['nom']) ?></td>
      <td><?= eur0((float)$b['budget_alloue']) ?></td>
      <td><?= eur0((float)$b['engage']) ?></td>
      <td style="color:<?= $restant < 0 ? 'var(--red)' : 'var(--green)' ?>;font-weight:600;"><?= eur0($restant) ?></td>
      <td style="min-width:140px;">
        <div class="prog-bar"><div style="width:<?= $pct ?>%;background:<?= $pct > 90 ? 'var(--red)' : ($pct > 70 ? 'var(--orange)' : 'var(--green)') ?>;"></div></div>
      </td>
    </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</div>

<div class="card">
  <div class="card-title" style="margin-bottom:4px;">Commandes en cours de traitement</div>
  <p style="font-size:12px;color:var(--text-light);margin-bottom:10px;">
    Processus : devis transmis &rarr; validation SF &rarr; signature du directeur &rarr; livraison &rarr; confirmation du département &rarr; paiement du fournisseur</p>
  <?php if (!$commandes): ?><p class="empty-state">Aucune commande en cours.</p><?php endif; ?>
  <?php foreach ($commandes as $c):
      $resteApres = $c['budget_alloue'] - $c['deja_engage'] - ($c['statut'] === 'Devis transmis au SF' ? $c['total'] : 0); ?>
  <div class="list-item">
    <div class="li-icon"><?= icon('file', 16) ?></div>
    <div class="li-info">
      <div class="li-title"><?= e($c['reference']) ?> — Dépt. <?= e($c['dept'] ?? '-') ?> &rarr; <?= e($c['fournisseur'] ?? '-') ?></div>
      <div class="li-meta"><?= date_fr($c['date_creation']) ?> · <strong style="color:var(--navy);"><?= eur((float)$c['total']) ?></strong>
        <?php if ($c['statut'] === 'Devis transmis au SF'): ?>
          · budget restant après validation : <strong style="color:<?= $resteApres < 0 ? 'var(--red)' : 'var(--green)' ?>;"><?= eur0($resteApres) ?></strong>
        <?php endif; ?>
      </div>
    </div>
    <div class="li-actions">
      <?= badge($c['statut']) ?>
      <form method="post" style="display:flex;gap:6px;">
        <?= csrf_field() ?>
        <input type="hidden" name="cmd_id" value="<?= $c['id'] ?>">
        <?php if ($c['statut'] === 'Devis transmis au SF'): ?>
          <button class="btn btn-gold btn-sm" name="action" value="valider">Valider le devis</button>
          <button class="btn btn-outline btn-sm" name="action" value="refuser" style="color:var(--red);">Refuser</button>
        <?php elseif ($c['statut'] === 'Devis validé'): ?>
          <button class="btn btn-gold btn-sm" name="action" value="bc_signe">Bon de commande signé (directeur)</button>
        <?php elseif ($c['statut'] === 'Bon de commande signé'): ?>
          <button class="btn btn-outline btn-sm" name="action" value="expedier">Livraison lancée</button>
        <?php elseif ($c['statut'] === 'Livraison en cours'): ?>
          <button class="btn btn-outline btn-sm" name="action" value="relancer">Relancer le département</button>
        <?php elseif ($c['statut'] === 'Réception confirmée'): ?>
          <button class="btn btn-gold btn-sm" name="action" value="payer">Payer le fournisseur</button>
        <?php endif; ?>
      </form>
    </div>
  </div>
  <?php endforeach; ?>
</div>
<?php page_foot();
